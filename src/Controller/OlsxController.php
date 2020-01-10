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

use Alexandre\EvcBundle\Service\EvcServiceInterface;
use App\Entity\User;
use App\Form\Model\OlsxRegister;
use App\Form\OlsxRegisterFormType;
use App\Mailer\MailerInterface;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * OLSX Controller.
 *
 * @category App\Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("olsx", name="olsx_")
 * @Security("is_granted('ROLE_USER')")
 */
class OlsxController extends AbstractController
{
    /**
     * Registering to OLSX service.
     *
     * @Route("/register", name="register", methods={"get", "post"})
     *
     * @param Request             $request     request handling data
     * @param UserManager         $userManager the user manager to save updates
     * @param EvcServiceInterface $evcService  evc service
     * @param MailerInterface     $mailer      to sent a mail on success
     */
    public function registering(
        Request $request,
        UserManager $userManager,
        EvcServiceInterface $evcService,
        MailerInterface $mailer
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->isOlsxCustomer()) {
            $this->addFlash('error', 'flash.olsx.already-registered');

            return $this->redirectToRoute('home');
        }

        $model = new OlsxRegister($evcService);
        $model->setCode($user->getOlsxIdentifier());
        $form = $this->createForm(OlsxRegisterFormType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'flash.olsx-registering');
            $user->setOlsxIdentifier($model->getCode());
            $user->setRegistering();
            $userManager->save($user);
            $mailer->sendOlsxRegistering($user);

            return $this->redirectToRoute('home');
        }

        return $this->render('olsx/register.html.twig', [
            'form' => $form->createView(),
            'isRegistering' => $user->isOlsxRegistering(),
        ]);
    }
}
