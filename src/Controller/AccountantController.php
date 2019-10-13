<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Payment;
use App\Entity\User;
use App\Factory\BillFactory;
use App\Form\AccountantCreditFormType;
use App\Form\Model\AccountantCreditOrder;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use App\Manager\PaymentManager;
use App\Manager\UserManager;
use Payum\Core\Payum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Accountant controller.
 *
 * @Route("/accountant", name="accountant_")
 *
 * @Security("is_granted('ROLE_ACCOUNTANT')")
 */
class AccountantController extends AbstractPaginateController
{
    /**
     * Limit of bills per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Create a bill for selected user.
     *
     * @Route("/bill/new/{user}", name="user_new", methods={"get", "post"})
     *
     * @param User         $user         Next bill owner
     * @param BillManager  $billManager  Bill manager to create bill
     * @param OrderManager $orderManager Order manager to create order
     * @param Payum        $payum        Payum manager
     * @param Request      $request      The request
     * @param UserManager  $userManager  User manager
     *
     * @return Response
     */
    public function bill(
     User $user,
     BillManager $billManager,
     OrderManager $orderManager,
     Payum $payum,
     Request $request,
     UserManager $userManager
    ): Response {
        $order = $orderManager->getOrCreateCartedOrder($user);
        $model = new AccountantCreditOrder();
        $model->init($order);
        $form = $this->createForm(AccountantCreditFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderManager->pushOrderedArticles($order, $model);
            $orderManager->accountantValidate($order);
            $bill = BillFactory::create($order, $user);
            if ($model->isCredit()) {
                $user->addCredit($order->getCredits());
                $this->addFlash('info', 'message.bill-new.user-credited');
            }

            //Payment
            $payment = $this->createPayment($payum, $model, $bill, $user);
            $order->addPayment($payment);

            //Save entities
            $orderManager->save($order);
            $billManager->save($bill);
            $userManager->save($user);

            //transform to bill
            return $this->redirectToRoute('accountant_bill_show', ['id' => $bill->getId()]);
        }

        return $this->render('accountant/user/new-bill.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'user' => $user,
        ]);
    }

    /**
     * Finds a bill entity, credit order then redirect user to list bill use case.
     *
     * @Route("/bill/credit/{bill}", name="bill_credit", methods={"get"})
     *
     * @param Bill         $bill         The bill to display
     * @param OrderManager $orderManager The order manager
     * @param Request      $request      The request to recover page, and current sort
     *
     * @return RedirectResponse
     */
    public function creditAndRedirect(Bill $bill, OrderManager $orderManager, Request $request): RedirectResponse
    {
        $parameters['page'] = $request->get('page', 1);
        $parameters['sort'] = $this->getSortedField($request, 'number');
        $parameters['highlight'] = $bill->getId();
        $parameters['direction'] = $this->getOrder($request);
        $parameters['color'] = 'warning';

        if ($bill->getOrder()->isCredited()) {
            $this->addFlash('warning', 'flash.order.already-credited');

            return $this->redirectToRoute('accountant_bill_list', $parameters);
        }

        $orderManager->credit($bill->getOrder());
        $this->addFlash('success', 'flash.order.credited');
        $parameters['color'] = 'success';

        return $this->redirectToRoute('accountant_bill_list', $parameters);
    }

    /**
     * Finds and displays a bill entity.
     * Redirect user to show page.
     *
     * @Route("/bill/credit/{bill}/show", name="bill_credit_show", methods={"get"})
     *
     * @param Bill         $bill         The bill to display
     * @param OrderManager $orderManager The order manager
     *
     * @return RedirectResponse
     */
    public function creditAndShow(Bill $bill, OrderManager $orderManager): RedirectResponse
    {
        if ($bill->getOrder()->isCredited()) {
            $this->addFlash('warning', 'flash.order.already-credited');

            return $this->redirectToRoute('accountant_bill_show', ['id' => $bill->getId()]);
        }

        $orderManager->credit($bill->getOrder());
        $this->addFlash('success', 'flash.order.credited');

        return $this->redirectToRoute('accountant_bill_show', ['id' => $bill->getId()]);
    }

    /**
     * Lists all bill entities.
     *
     * @Route("/bill", name="bill_list", methods={"get"})
     *
     * @param BillManager $billManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @return Response|RedirectResponse
     */
    public function listBill(BillManager $billManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['number', 'customers', 'amount'])) {
            return $this->redirectToRoute('accountant_bill_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'number');
        $sort = $this->getOrder($request, 'desc');
        $highlight = $request->get('highlight', 0);
        $color = $request->get('color', 'info');

        $pagination = $billManager->paginate(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('accountant/bill/list.html.twig', [
            'pagination' => $pagination,
            'highlight' => $highlight,
            'color' => $color,
        ]);
    }

    /**
     * Lists all user entities.
     *
     * @Route("/user", name="user_list", methods={"get"})
     *
     * @param UserManager $userManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @return Response|RedirectResponse
     */
    public function listUser(UserManager $userManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['username', 'mail', 'credit'])) {
            return $this->redirectToRoute('accountant_user_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'username');
        $sort = $this->getOrder($request);

        $pagination = $userManager->paginate(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('accountant/user/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a bill to print.
     *
     * @Route("/bill/print/{id}", name="bill_print", methods={"get"})
     *
     * @param PaymentManager $paymentManager The order manager to get last payment
     * @param Bill           $bill           The bill to print
     *
     * @return Response
     */
    public function print(PaymentManager $paymentManager, Bill $bill): Response
    {
        $order = $bill->getOrder();
        $payment = null;

        if (null !== $order) {
            $payment = $paymentManager->getValidPayment($order);
        }

        return $this->render('accountant/bill/print.html.twig', [
            'bill' => $bill,
            'payment' => $payment,
            'order' => $order,
        ]);
    }

    /**
     * Finds and displays a bill entity.
     *
     * @Route("/bill/{id}", name="bill_show", methods={"get"})
     *
     * @param Bill           $bill           The bill to display
     * @param BillManager    $billManager    The bill manager
     * @param PaymentManager $paymentManager The payment manager
     *
     * @return Response
     */
    public function show(Bill $bill, BillManager $billManager, PaymentManager $paymentManager): Response
    {
        $payment = null;
        $logs = $billManager->retrieveLogs($bill);
        $order = $bill->getOrder();

        if (null !== $order) {
            $payment = $paymentManager->getValidPayment($order);
        }

        return $this->render('accountant/bill/show.html.twig', [
            'logs' => $logs,
            'bill' => $bill,
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * Create the payment.
     *
     * @param Payum                 $payum Payum manager
     * @param AccountantCreditOrder $model the data model
     * @param Bill                  $bill  the bill to get amount
     * @param User                  $user  the user to get identifier
     *
     * @return Payment
     */
    private function createPayment(Payum $payum, AccountantCreditOrder $model, Bill $bill, User $user): Payment
    {
        $storage = $payum->getStorage(Payment::class);

        /** @var Payment $payment */
        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setDescription($model->getMethod());
        $payment->setTotalAmount($bill->getAmount());
        //override setClientId to prepare a json method
        $payment->setClientId($user->getId());
        //add the setOrderId method
        $payment->setClientEmail($user->getMail());

        $storage->update($payment);

        return $payment;
    }
}
