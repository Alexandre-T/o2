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

use App\Form\LoginFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * The login page.
     *
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $authenticationUtils the authentication utils
     *
     * @return RedirectResponse|Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return new RedirectResponse('/');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $email = $authenticationUtils->getLastUsername();

        // create the form
        $form = $this->createForm(LoginFormType::class, ['mail' => $email]);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * The logout route should not be reached.
     *
     * @Route("/logout", name="security_logout")
     *
     * @throws Exception This route cannot be reached
     */
    public function logoutAction(): void
    {
        throw new Exception('this should not be reached!');
    }

    /**
     * The registration is not available.
     *
     * @Route("/register", name="security_register")
     *
     * @return Response
     */
    public function registerAction(): Response
    {
        if ($this->getUser()) {
            return new RedirectResponse('/');
        }

        return $this->render('security/register.html.twig');
    }
}
