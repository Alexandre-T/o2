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
use App\Manager\BillManager;
use App\Manager\OrderManager;
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
     * Finds and displays a bill entity.
     * Redirect user to list bill use case.
     *
     * @Route("/bill/credit/{bill}", name="bill_credit", methods={"get"})
     *
     * @param Bill         $bill         The bill to display
     * @param OrderManager $orderManager The order manager
     * @param Request      $request      The request to recover page, and current sort
     *
     * @return RedirectResponse
     */
    public function creditAndList(Bill $bill, OrderManager $orderManager, Request $request): RedirectResponse
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
     * Lists all user entities.
     *
     * @Route("/bill", name="bill_list", methods={"get"})
     *
     * @param BillManager $billManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @return Response|RedirectResponse
     */
    public function list(BillManager $billManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['number', 'customers', 'amount'])) {
            return $this->redirectToRoute('accountant_bill_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'number');
        $sort = $this->getOrder($request);
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
     * Finds and displays a bill entity.
     *
     * @Route("/bill/{id}", name="bill_show", methods={"get"})
     *
     * @param Bill        $bill        The bill to display
     * @param BillManager $billManager The bill manager
     *
     * @return Response
     */
    public function show(Bill $bill, BillManager $billManager): Response
    {
        $instruction = null;
        $logs = $billManager->retrieveLogs($bill);
        $order = $bill->getOrder();
        $payments = [];

        if (null !== $order) {
            $instruction = $order->getPaymentInstruction();
        }

        if (null !== $instruction) {
            $payments = $instruction->getPayments();
        }

        return $this->render('accountant/bill/show.html.twig', [
            'logs' => $logs,
            'bill' => $bill,
            'order' => $order,
            'instruction' => $instruction,
            'payments' => $payments,
        ]);
    }
}
