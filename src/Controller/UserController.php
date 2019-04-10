<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteFormType;
use App\Form\PasswordAdminFormType;
use App\Form\UserFormType;
use App\Manager\UserManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UserController class.
 *
 * @Route("administration/user")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends AbstractController
{
    /**
     * Limit of user per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="administration_user_delete", methods={"delete"})
     *
     * @param User                $user        The $user entity
     * @param Request             $request     The request
     * @param UserManager         $userManager
     * @param TranslatorInterface $trans
     *
     * @return RedirectResponse
     */
    public function deleteAction(User $user, Request $request, UserManager $userManager, TranslatorInterface $trans)
    {
        $form = $this->createForm(DeleteFormType::class, $user);
        $form->handleRequest($request);
        $isDeletable = $userManager->isDeletable($user);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $session = $this->get('session');
            $userManager->delete($user);
            $message = $trans->trans('entity.user.deleted %name%', ['%name%' => $user->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        } elseif (!$isDeletable) {
            $session = $this->get('session');
            $message = $trans->trans('entity.user.deleted %name%', ['%name%' => $user->getLabel()]);
            $session->getFlashBag()->add('warning', $message);

            return $this->redirectToRoute('administration_user_show', ['id' => $user->getId()]);
        }

        return $this->redirectToRoute('administration_user_index');
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="administration_user_edit", methods={"get", "post"})
     *
     * @param User                $user        The user entity
     * @param Request             $request     The request
     * @param UserManager         $userManager
     * @param TranslatorInterface $trans
     *
     * @return RedirectResponse|Response
     */
    public function editAction(User $user, Request $request, UserManager $userManager, TranslatorInterface $trans)
    {
        $deleteForm = $this->createForm(DeleteFormType::class, $user, [
            'action' => $this->generateUrl('administration_user_delete', ['id' => $user->getId()]),
        ]);
        $editForm = $this->createForm(UserFormType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager->save($user);
            $session = $this->get('session');
            $message = $trans->trans('entity.user.updated %name%', ['%name%' => $user->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('administration_user_show', array('id' => $user->getId()));
        }

        $logs = $userManager->retrieveLogs($user);

        return $this->render('administration/user/edit.html.twig', [
            'deletable' => $userManager->isDeletable($user),
            'logs' => $logs,
            'information' => $user,
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Lists all user entities.
     *
     * @Route("/", name="administration_user_index", methods={"get"})
     *
     * @param UserManager $userManager the user manage to paginate users
     * @param Request     $request     the requests to handle page and sorting
     *
     * @return Response
     */
    public function indexAction(UserManager $userManager, Request $request)
    {
        //Query parameters check
        $field = 'mail' == $request->query->getAlpha('sort') ? 'mail' : 'username';
        $sort = 'desc' == $request->query->getAlpha('direction') ? 'desc' : 'asc';

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
     * Creates a new user entity.
     *
     * @Route("/new", name="administration_user_new", methods={"get","post"})
     *
     * @param UserManager         $userManager
     * @param Request             $request
     * @param TranslatorInterface $trans
     *
     * @return RedirectResponse |Response
     */
    public function newAction(UserManager $userManager, Request $request, TranslatorInterface $trans)
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);
            //Flash message
            $session = $this->get('session');
            $message = $trans->trans('entity.user.created %name%', ['%name%' => $user->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('administration_user_show', array('id' => $user->getId()));
        }

        return $this->render('administration/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to update password of an existing user entity.
     *
     * @Route("/{id}/password", name="administration_user_password", methods={"get", "post"})
     *
     * @param User                $user        The user entity
     * @param Request             $request     The request
     * @param UserManager         $userManager
     * @param TranslatorInterface $trans
     *
     * @return RedirectResponse|Response
     */
    public function passwordAction(User $user, Request $request, UserManager $userManager, TranslatorInterface $trans)
    {
        $deleteForm = $this->createForm(DeleteFormType::class, $user, [
            'action' => $this->generateUrl('administration_user_delete', ['id' => $user->getId()]),
        ]);
        $passwordForm = $this->createForm(PasswordAdminFormType::class, $user);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $userManager->save($user);
            $session = $this->get('session');
            $message = $trans->trans('entity.user.password %name%', ['%name%' => $user->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('administration_user_show', array('id' => $user->getId()));
        }

        $logs = $userManager->retrieveLogs($user);

        return $this->render('administration/user/password.html.twig', [
            'deletable' => $userManager->isDeletable($user),
            'logs' => $logs,
            'information' => $user,
            'user' => $user,
            'password_form' => $passwordForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="administration_user_show", methods={"get"})
     *
     * @param User        $user
     * @param UserManager $userManager
     *
     * @return Response
     */
    public function showAction(User $user, UserManager $userManager)
    {
        $deleteForm = $this->createForm(DeleteFormType::class, $user);
        $logs = $userManager->retrieveLogs($user);

        return $this->render('administration/user/show.html.twig', [
            'isDeletable' => $userManager->isDeletable($user),
            'logs' => $logs,
            'information' => $user,
            'user' => $user,
            'deletable' => $userManager->isDeletable($user),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
