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
use App\Exception\SettingsException;
use App\Manager\BillManager;
use App\Manager\ProgrammationManager;
use App\Manager\SettingsManager;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Default Controller.
 *
 * @category App\Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class DefaultController extends AbstractController
{
    /**
     * Homepage.
     *
     * @Route("/", name="home", methods={"get"})
     * @Route("/", name="homepage", methods={"get"})
     *
     * @param BillManager          $billManager          bill manager
     * @param ProgrammationManager $programmationManager programmation manager
     * @param UserManager          $userManager          user manager
     *
     * @return Response
     */
    public function index(
     BillManager $billManager,
     ProgrammationManager $programmationManager,
     UserManager $userManager
    ): Response {
        $parameters = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $parameters = [
                'users' => $userManager->count(),
            ];
        }

        if ($this->isGranted('ROLE_PROGRAMMER')) {
            $parameters = array_merge($parameters, [
                'programmer_programmations' => $programmationManager->countPending(),
            ]);
        }

        if ($this->isGranted('ROLE_ACCOUNTANT')) {
            $parameters = array_merge($parameters, [
                'accountant_bills' => $billManager->count(),
            ]);
        }

        if ($this->isGranted('ROLE_USER')) {
            $parameters = array_merge($parameters, $this->getUserParameters());
        }

        return $this->render('default/index.html.twig', $parameters);
    }

    /**
     * Legacy mentions.
     *
     * @Route("/legacy", name="legacy", methods={"get"})
     *
     * @param SettingsManager $settingsManager the settings manager to retrieve data
     *
     * @throws SettingsException when data is non-existent
     *
     * @return Response
     */
    public function legacy(SettingsManager $settingsManager): Response
    {
        $data = [];

        $data['legacy_society'] = $settingsManager->getValue('bill-name');
        $data['legacy_form'] = $settingsManager->getValue('bill-status');
        $data['legacy_address'] = $settingsManager->getValue('bill-street-address');
        $data['legacy_address'] .= "\n".$settingsManager->getValue('bill-complement');
        $data['legacy_address'] .= "\n".$settingsManager->getValue('bill-postal-code');
        $data['legacy_address'] .= ' '.$settingsManager->getValue('bill-locality');
        $data['legacy_address'] .= "\n".$settingsManager->getValue('bill-country');
        $data['legacy_capital'] = $settingsManager->getValue('bill-status');
        $data['legacy_mail'] = $settingsManager->getValue('mail-sender');
        $data['legacy_tel'] = $settingsManager->getValue('bill-telephone');
        $data['legacy_rcs'] = $settingsManager->getValue('legacy-rcs');
        $data['legacy_tva'] = $settingsManager->getValue('bill-vat-number');
        $data['legacy_publication'] = $settingsManager->getValue('legacy-publication');

        $data['host_name'] = $settingsManager->getValue('host-name');
        $data['host_form'] = $settingsManager->getValue('host-form');
        $data['host_address'] = $settingsManager->getValue('host-address');
        $data['host_tel'] = $settingsManager->getValue('host-tel');

        return $this->render('default/legacy.html.twig', $data);
    }

    /**
     * Term of conditions page.
     *
     * @Route("/tos", name="tos", methods={"get"})
     *
     * @return Response
     */
    public function tos(): Response
    {
        return $this->render('default/tos.html.twig');
    }

    /**
     * Return parameters for users.
     *
     * @return array
     */
    private function getUserParameters(): array
    {
        /** @var User $user */
        $user = $this->getUser();

        return [
            'bills' => count($user->getBills()),
            'credits' => $user->getCredit(),
            'programmations' => count($user->getProgrammations()),
        ];
    }
}
