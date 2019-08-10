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
use App\Security\Voter\BillVoter;
use Doctrine\ORM\Query\QueryException as QueryExceptionAlias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Bill controller.
 *
 * @Route("/customer/bill", name="customer_bill_")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class BillController extends AbstractPaginateController
{
    /**
     * Limit of bills per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Lists all user entities.
     *
     * @Route("/list", name="list", methods={"get"})
     *
     * @param BillManager $billManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @throws QueryExceptionAlias this should not happened
     *
     * @return Response|RedirectResponse
     */
    public function list(BillManager $billManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['number', 'amount'])) {
            return $this->redirectToRoute('customer_bill_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'number');
        $sort = $this->getOrder($request, 'desc');

        $pagination = $billManager->paginateWithUser(
            $this->getUser(),
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('customer/bill/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a bill entity.
     *
     * @Route("/{id}", name="show", methods={"get"})
     *
     * @param Bill $bill The bill to display
     *
     * @return Response
     */
    public function show(Bill $bill): Response
    {
        // check for "show" access: calls all voters
        $this->denyAccessUnlessGranted(BillVoter::SHOW, $bill);

        $order = $bill->getOrder();
        $payment = $order->getPayment();

        return $this->render('customer/bill/show.html.twig', [
            'bill' => $bill,
            'order' => $order,
            'payment' => $payment,
        ]);
    }
}
