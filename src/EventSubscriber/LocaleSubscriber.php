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

namespace App\EventSubscriber;

use App\Entity\LanguageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * The default locale.
     *
     * @var string the defaultLocale
     */
    private $defaultLocale;

    /**
     * LocaleSubscriber constructor defines the default locale.
     *
     * @param string $defaultLocale the default locale
     */
    public function __construct($defaultLocale = LanguageInterface::FRENCH)
    {
        //Try to override with parameters
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Subscribed events getter.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    /**
     * Job on kernel request.
     *
     * @param RequestEvent $event Subscribed event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (! $request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        $locale = $request->attributes->get('_locale');
        if ($locale) {
            $request->getSession()->set('_locale', $locale);

            return;
        }

        // no explicit locale has been set on this request, use one from the session
        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
    }
}
