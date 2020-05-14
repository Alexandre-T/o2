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
use App\Form\DeleteFormType;
use App\Form\PasswordAdminFormType;
use App\Form\UserFormType;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UserController class.
 *
 * @Route("administration/user", name="administration_user_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends AbstractPaginateController
{
    /**
     * Limit of user per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="new", methods={"get", "post"})
     *
     * @param UserManager         $userManager the user manager
     * @param Request             $request     request to get data form
     * @param TranslatorInterface $trans       the translator interface
     *
     * @return RedirectResponse |Response
     */
    public function create(UserManager $userManager, Request $request, TranslatorInterface $trans): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);
            //Flash message
            $message = $trans->trans('entity.user.created %name%', ['%name%' => $user->getLabel()]);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('administration_user_show', ['id' => $user->getId()]);
        }

        return $this->render('administration/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="delete", methods={"delete"})
     *
     * @param User                $customer The user entity
     * @param Request             $request  The request
     * @param UserManager         $manager  The user manager
     * @param TranslatorInterface $trans    The translator
     */
    public function delete(
        User $customer,
        Request $request,
        UserManager $manager,
        TranslatorInterface $trans
    ): RedirectResponse {
        $form = $this->createForm(DeleteFormType::class, $customer);
        $form->handleRequest($request);

        $isDeletable = $manager->isDeletable($customer);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $manager->delete($customer);
            $message = $trans->trans('entity.user.deleted %name%', ['%name%' => $customer->getLabel()]);
            $this->addFlash('success', $message);
        } elseif (!$isDeletable) {
            $message = $trans->trans('entity.user.deleted %name%', ['%name%' => $customer->getLabel()]);
            $this->addFlash('warning', $message);

            return $this->redirectToRoute('administration_user_show', ['id' => $customer->getId()]);
        }

        return $this->redirectToRoute('administration_user_index');
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="edit", methods={"get", "post"})
     *
     * @param User                $customer    The user entity
     * @param Request             $request     The request
     * @param UserManager         $userManager the user manager
     * @param TranslatorInterface $trans       the translator
     *
     * @return RedirectResponse|Response
     */
    public function edit(
        User $customer,
        Request $request,
        UserManager $userManager,
        TranslatorInterface $trans
    ): Response {
        $deleteForm = $this->createForm(DeleteFormType::class, $customer, [
            'action' => $this->generateUrl('administration_user_delete', ['id' => $customer->getId()]),
        ]);
        $editForm = $this->createForm(UserFormType::class, $customer, ['update' => true]);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager->save($customer);
            $message = $trans->trans('entity.user.updated %name%', ['%name%' => $customer->getLabel()]);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('administration_user_show', ['id' => $customer->getId()]);
        }

        $logs = $userManager->retrieveLogs($customer);

        return $this->render('administration/user/edit.html.twig', [
            'deletable' => $userManager->isDeletable($customer),
            'logs' => $logs,
            'information' => $customer,
            'user' => $customer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Lists all user entities.
     *
     * @Route("/", name="index", methods={"get"})
     *
     * @param UserManager $userManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @return Response|RedirectResponse
     */
    public function list(UserManager $userManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['username', 'mail'])) {
            return $this->redirectToRoute('administration_user_index');
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

        return $this->render('administration/user/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a form to update password of an existing user entity.
     *
     * @Route("/{id}/password", name="password", methods={"get", "post"})
     *
     * @param User                $customer The user entity
     * @param Request             $request  The request
     * @param UserManager         $manager  The user manager
     * @param TranslatorInterface $trans    The translator
     *
     * @return RedirectResponse|Response
     */
    public function password(
        User $customer,
        Request $request,
        UserManager $manager,
        TranslatorInterface $trans
    ): Response {
        $deleteForm = $this->createForm(DeleteFormType::class, $customer, [
            'action' => $this->generateUrl('administration_user_delete', ['id' => $customer->getId()]),
        ]);
        $passwordForm = $this->createForm(PasswordAdminFormType::class, $customer);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $manager->save($customer);
            $message = $trans->trans('entity.user.password %name%', ['%name%' => $customer->getLabel()]);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('administration_user_show', ['id' => $customer->getId()]);
        }

        $logs = $manager->retrieveLogs($customer);

        return $this->render('administration/user/password.html.twig', [
            'deletable' => $manager->isDeletable($customer),
            'logs' => $logs,
            'information' => $customer,
            'user' => $customer,
            'password_form' => $passwordForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="show", methods={"get"})
     *
     * @param User        $customer    The user to display
     * @param UserManager $userManager The user manager
     */
    public function show(User $customer, UserManager $userManager): Response
    {
        $deleteForm = $this->createForm(DeleteFormType::class, $customer);
        $logs = $userManager->retrieveLogs($customer);

        return $this->render('administration/user/show.html.twig', [
            'logs' => $logs,
            'user' => $customer,
            'deletable' => $userManager->isDeletable($customer),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
