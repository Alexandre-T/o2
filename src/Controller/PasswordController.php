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
use App\Form\Model\PasswordLost;
use App\Form\Model\PasswordReset;
use App\Form\PasswordLostFormType;
use App\Form\PasswordResetFormType;
use App\Mailer\MailerInterface;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PasswordController extends AbstractController
{
    /**
     * The use case to ask for a new password.
     *
     * @Route("/password-lost", name="security_password_lost")
     *
     * @param Request                 $request       form request form request
     * @param EntityManagerInterface  $entityManager manager to save user
     * @param MailerInterface         $mailer        mailer to sent resetting link
     * @param TokenGeneratorInterface $token         token generator
     *
     * @throws Exception when datetime crash
     *
     * @return RedirectResponse|Response
     */
    public function lost(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $token
    ): Response {
        if ($this->getUser()) {
            return new RedirectResponse('/');
        }

        // create the modal then the form
        $model = new PasswordLost();
        $form = $this->createForm(PasswordLostFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRepository $userRepository */
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneByMail($model->getMail());

            if ($user instanceof User) {
                //user exists, send an activation mail
                $user->setResettingToken($token->generateToken());
                $user->setResettingAt(new DateTimeImmutable());
                $entityManager->persist($user);
                $entityManager->flush();
                $mailer->sendResettingEmailMessage($user);
            }

            $this->addFlash('success', 'flash.password-lost.sent');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('password/lost.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset password use case.
     *
     * @Route(path="/password-reset", name="security_reset")
     *
     * @param Request                $request       request containing token
     * @param EntityManagerInterface $entityManager entity manager to save new password
     *
     * @return Response|RedirectResponse
     */
    public function reset(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return new RedirectResponse('/');
        }

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $token = $request->get('token');

        $user = $userRepository->findOneByToken($token);

        if (null === $user) {
            //token does not exist or is out-of-date
            $this->addFlash('error', 'flash.password-reset.out-of-date');

            return $this->redirectToRoute('security_password_lost');
        }

        // create the modal then the form
        $model = new PasswordReset();
        $model->setToken($token);
        $form = $this->createForm(PasswordResetFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //user exists, store new password
            $user->setPlainPassword($model->getPassword());
            $user->setResettingAt(null);
            $user->setResettingToken(null);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'flash.password-reset.done');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
