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
use App\Form\DeleteFormType;
use App\Manager\BillManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Accountant controller.
 *
 * @Route("/accountant")
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
     * Lists all user entities.
     *
     * @Route("/bill", name="accountant_bill_list", methods={"get"})
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

        $pagination = $billManager->paginate(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('accountant/bill/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a bill entity.
     *
     * @Route("/bill/{id}", name="accountant_bill_show", methods={"get"})
     *
     * @param Bill        $bill        The bill to display
     * @param BillManager $billManager The bill manager
     *
     * @return Response
     */
    public function show(Bill $bill, BillManager $billManager): Response
    {
        $deleteForm = $this->createForm(DeleteFormType::class, $bill);
        $logs = $billManager->retrieveLogs($bill);

        return $this->render('accountant/bill/show.html.twig', [
            'logs' => $logs,
            'bill' => $bill,
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
