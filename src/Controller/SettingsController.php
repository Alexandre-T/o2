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

use App\Entity\Settings;
use App\Form\SettingsFormType;
use App\Manager\SettingsManager;
use Doctrine\ORM\Query\QueryException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * SettingsController class.
 *
 * @Route("administration/settings", name="administration_settings_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class SettingsController extends AbstractPaginateController
{
    /**
     * Limit of settings per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Displays a form to edit an existing settings entity.
     *
     * @Route("/{id}/edit", name="edit", methods={"get", "post"})
     *
     * @param Settings            $settings        The settings entity
     * @param Request             $request         The request
     * @param SettingsManager     $settingsManager the settings manager
     * @param TranslatorInterface $trans           the translator
     *
     * @return RedirectResponse|Response
     */
    public function edit(
     Settings $settings,
     Request $request,
     SettingsManager $settingsManager,
     TranslatorInterface $trans
    ): Response {
        switch ($settings->getCode()) {
            case 'service-until':
                $type = 'date';
                break;
            case 'service-status':
                $type = 'status';
                break;
            default:
                $type = 'string';
        }

        $editForm = $this->createForm(SettingsFormType::class, $settings, [
            'code' => $settings->getCode(),
            'value_class' => $type,
        ]);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $settingsManager->save($settings);
            $label = $trans->trans($settings->getCode());
            $message = $trans->trans('entity.settings.updated %label%', ['%label%' => $label]);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('administration_settings_list');
        }

        $logs = $settingsManager->retrieveLogs($settings);

        return $this->render('administration/settings/edit.html.twig', [
            'logs' => $logs,
            'settings' => $settings,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Lists only updatable settings entities.
     *
     * @Route("/", name="list", methods={"get"})
     *
     * @param SettingsManager $settingsManager the settings manage to paginate settings
     * @param Request         $request         the requests to handle page and sorting
     *
     * @throws QueryException should not happen because criteria are fixed
     *
     * @return Response|RedirectResponse
     */
    public function list(SettingsManager $settingsManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['code'])) {
            return $this->redirectToRoute('administration_settings_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'code');
        $sort = $this->getOrder($request);

        $pagination = $settingsManager->paginateUpdatable(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('administration/settings/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
