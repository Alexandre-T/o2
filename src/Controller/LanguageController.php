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

use App\Entity\LanguageInterface;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Language controller.
 *
 * @Route("/language", name="language_")
 */
class LanguageController extends AbstractPaginateController
{
    /**
     * Switch to english language.
     *
     * @Route("/english", name="english", methods={"get"})
     *
     * @param UserManager $userManager user manager to save new default language
     * @param Request     $request     the request to set locale
     *
     * @return RedirectResponse
     */
    public function english(UserManager $userManager, Request $request): RedirectResponse
    {
        $this->switchLanguage($userManager, $request, LanguageInterface::ENGLISH);

        $this->addFlash('success', 'flash.language.english');

        return $this->redirectToRoute('home');
    }

    /**
     * Switch to french language.
     *
     * @Route("/french", name="french", methods={"get"})
     *
     * @param UserManager $userManager user manager to save new default language
     * @param Request     $request     the request to set locale
     *
     * @return RedirectResponse
     */
    public function french(UserManager $userManager, Request $request): RedirectResponse
    {
        $this->switchLanguage($userManager, $request, LanguageInterface::FRENCH);

        $this->addFlash('success', 'flash.language.french');

        return $this->redirectToRoute('home');
    }

    /**
     * Switch parameters to new language.
     *
     * @param UserManager $userManager user manager to save new language profile
     * @param Request     $request     the request to set locale
     * @param string      $language    the language incoming from route
     */
    private function switchLanguage(UserManager $userManager, Request $request, string $language): void
    {
        $user = $this->getUser();
        if ($user instanceof LanguageInterface) {
            $user->setLanguage($language);
            $userManager->save($user);
        }

        $request->getSession()->set('_locale', $language);
        $request->attributes->set('_locale', $language);
    }
}
