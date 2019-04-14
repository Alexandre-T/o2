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
use App\Form\CreditFormType;
use App\Form\Model\ChangePassword;
use App\Form\Model\CreditOrder;
use App\Form\PasswordFormType;
use App\Form\ProfileFormType;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Customer controller.
 *
 * @Route("/customer")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class CustomerController extends AbstractController
{
    /**
     * Edit profile.
     *
     * @Route("/profile", name="customer_profile")
     *
     * @param Request                $request       the request handling data
     * @param EntityManagerInterface $entityManager entity manager to save user
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfileFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'flash.profile.updated');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'flash.profile.not-updated');
        }

        return $this->render('customer/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit profile.
     *
     * @Route("/password", name="customer_password")
     *
     * @param Request                $request       the request handling data
     * @param EntityManagerInterface $entityManager entity manager to save user
     *
     * @return Response|RedirectResponse
     */
    public function password(Request $request, EntityManagerInterface $entityManager): Response
    {
        $model = new ChangePassword();
        $form = $this->createForm(PasswordFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $user->setPlainPassword($model->getNewPassword());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'flash.password.updated');

            return $this->redirectToRoute('home');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'flash.password.not-updated');
        }

        return $this->render('customer/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Select credit.
     *
     * @Route("/select-credit", name="customer_credit")
     *
     * @param Request      $request      Request handling data
     * @param OrderManager $orderManager Command manager
     *
     * @return Response|RedirectResponse
     */
    public function selectCredit(Request $request, OrderManager $orderManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $orderManager->getOrCreateCartedOrder($user);
        $model = new CreditOrder();
        $model->initializeWithOrder($order);
        $form = $this->createForm(CreditFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->copyAddress($user);
            $orderManager->pushOrderedArticles($order, $model);
            $orderManager->save($order);
            $this->addFlash('success', 'flash.order.step1');

            return $this->redirectToRoute('customer_buy');
        }

        return $this->render('customer/select-credit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Select credit.
     *
     * @Route("/buy-credit", name="customer_buy")
     *
     * @param OrderManager $orderManager Command manager
     *
     * @return Response|RedirectResponse
     */
    public function buyCredit(OrderManager $orderManager): Response
    {
        $user = $this->getUser();

        if (!$orderManager->hasCartedOrder($user)) {
            return $this->redirectToRoute('customer_credit');
        }

        //find order
        //if order.price is zero redirectToRoute

        return $this->render('customer/buy-credit.html.twig');
    }
}
