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

use App\Entity\File;
use App\Entity\Programmation;
use App\Entity\User;
use App\Form\CreditFormType;
use App\Form\Model\ChangePassword;
use App\Form\Model\CreditOrder;
use App\Form\Model\Programmation as ProgrammationModel;
use App\Form\PasswordFormType;
use App\Form\ProfileFormType;
use App\Form\ProgrammationFormType;
use App\Manager\OrderManager;
use App\Manager\ProgrammationManager;
use App\Manager\UserManager;
use App\Security\Voter\ProgrammationVoter;
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
 * //TODO add a prefix customer_ with name attribute in route annotation
 *
 * @Route("/customer")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class CustomerController extends AbstractController
{
    /**
     * Customer order a new programmation.
     *
     * @Route("/programmation/new", name="customer_file_new")
     *
     * @param Request              $request              request handling data
     * @param ProgrammationManager $programmationManager programmation manger to save new programmation
     * @param UserManager          $userManager          to update credit of user
     *
     * @return Response
     */
    public function newProgrammation(
     Request $request,
     ProgrammationManager $programmationManager,
     UserManager $userManager
    ): Response {
        $model = new ProgrammationModel();
        $model->setCustomerCredit($this->getUser()->getCredit());
        $form = $this->createForm(ProgrammationFormType::class, $model);
        $form->handleRequest($request);
        //dd($form,$model);
        // TODO add a rule to test that user->getCredit() gte model->getCredit
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $programmation = new Programmation();
            $file = new File();
            $model->copyProgrammation($programmation);
            $model->copyFile($file);
            $programmation->setCustomer($user);
            $programmation->setOriginalFile($file);
            $userManager->debit($programmation);
            $programmationManager->save($file);
            $programmationManager->save($programmation);
            $userManager->save($user);
            $this->addFlash('success', 'flash.order.step1');

            return $this->redirectToRoute('customer_programmation_show', [
                'id' => $programmation->getId(),
            ]);
        }

        return $this->render('customer/file/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Step1: Customer select items.
     *
     * @Route("/order-credit", name="customer_order_credit")
     *
     * @param Request      $request      Request handling data
     * @param OrderManager $orderManager Command manager
     *
     * @return Response|RedirectResponse
     */
    public function orderCredit(Request $request, OrderManager $orderManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $orderManager->getOrCreateCartedOrder($user);
        $model = new CreditOrder();
        $model->init($order);
        $form = $this->createForm(CreditFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderManager->pushOrderedArticles($order, $model);
            $orderManager->save($order);
            $this->addFlash('success', 'flash.order.step1');

            return $this->redirectToRoute('customer_payment_method');
        }

        return $this->render('customer/order-credit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
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
     * Finds and displays a programmation entity.
     *
     * @Route("/programmation/{id}", name="customer_programmation_show", methods={"get"})
     *
     * @param Programmation $programmation the programmation to show
     *
     * @return Response
     */
    public function showProgrammation(Programmation $programmation): Response
    {
        $this->denyAccessUnlessGranted(ProgrammationVoter::SHOW, $programmation);
        dd($programmation, $this->getUser());

        return $this->render('customer/programmation/show.html.twig', [
            'programmation', $programmation,
        ]);
    }
}
