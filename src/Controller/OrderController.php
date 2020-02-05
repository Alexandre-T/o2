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

use App\Entity\User;
use App\Manager\OrderManager;
use App\Model\OrderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Order controller.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class OrderController extends AbstractPaginateController
{
    /*
     * Limit per page of 25 orders.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Show canceled orders.
     *
     * @Route("/accountant/orders/canceled", name="accountant_orders_canceled")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     */
    public function canceledOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'accountant/orders/canceled.html.twig',
            OrderInterface::STATUS_CANCELED,
            'customer_orders_canceled'
        );
    }

    /**
     * Show paid orders.
     *
     * @Route("/accountant/orders/paid", name="accountant_orders_paid")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     */
    public function paidOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'accountant/orders/paid.html.twig',
            OrderInterface::STATUS_PAID,
            'customer_orders_paid'
        );
    }

    /**
     * Show pending orders.
     *
     * @Route("/accountant/orders/pending", name="accountant_orders_pending")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     */
    public function pendingOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'accountant/orders/pending.html.twig',
            OrderInterface::STATUS_PENDING,
            'customer_orders_pending'
        );
    }

    /**
     * Show canceled orders.
     *
     * @Route("/customer/orders/canceled", name="customer_orders_canceled")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     */
    public function myCanceledOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'customer/orders/canceled.html.twig',
            OrderInterface::STATUS_CANCELED,
            'customer_orders_canceled',
            $this->getUser()
        );
    }

    /**
     * Show paid orders.
     *
     * @Route("/customer/orders/paid", name="customer_orders_paid")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     */
    public function myPaidOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'customer/orders/paid.html.twig',
            OrderInterface::STATUS_PAID,
            'customer_orders_paid',
            $this->getUser()
        );
    }

    /**
     * Show pending orders.
     *
     * @Route("/customer/orders/pending", name="customer_orders_pending")
     *
     * @param OrderManager $orderManager order manager to get pending orders of current user
     * @param Request      $request      request to get pagination key
     */
    public function myPendingOrders(OrderManager $orderManager, Request $request): Response
    {
        return $this->orders(
            $orderManager,
            $request,
            'customer/orders/pending.html.twig',
            OrderInterface::STATUS_PENDING,
            'customer_orders_pending',
            $this->getUser()
        );
    }

    /**
     * Show pending orders.
     *
     * @param OrderManager       $orderManager order manager to get pending orders of current user
     * @param Request            $request      The request to get pagination value
     * @param string             $template     template name
     * @param int                $status       the status of orders
     * @param string             $route        the route when filters are not valide
     * @param UserInterface|User $user         add a filter on this user when provided
     */
    private function orders(
        OrderManager $orderManager,
        Request $request,
        string $template,
        int $status,
        string $route,
        User $user = null
    ): Response {
        $method = $this->getMethod($status);
        if (!$this->validateSortedField($request, ['identifier', 'amount'])) {
            return $this->redirectToRoute($route);
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'identifier');
        $sort = $this->getOrder($request, 'desc');

        $pagination = $orderManager->{$method}(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort,
            $user
        );

        return $this->render( $template, [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Return the method to call.
     *
     * @param int status order status
     */
    private function getMethod(int $status): string
    {
        switch ($status) {
            case OrderInterface::STATUS_CANCELED:
                return 'paginateCanceled';
            case OrderInterface::STATUS_PAID:
                return 'paginatePaid';
            case OrderInterface::STATUS_PENDING:
            default:
                return 'paginatePending';
        }
    }
}
