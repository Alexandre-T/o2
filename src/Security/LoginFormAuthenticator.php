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

namespace App\Security;

use App\Form\LoginFormType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Login form authenticator class.
 *
 * TODO: https://symfonycasts.com/screencast/symfony-security/remember-me#play
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * Flash bag.
     *
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Url generator.
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface         $formFactory     the form factory
     * @param FlashBagInterface            $flashBag        the flash bag to send notification to user
     * @param LoggerInterface              $logger          the logger interface to log connections
     * @param UrlGeneratorInterface        $urlGenerator    the url generator to redirect user
     * @param TranslatorInterface          $translator      the translator interface to translate notifications
     * @param UserPasswordEncoderInterface $passwordEncoder the password encoder to test password sent with encrypted
     */
    public function __construct(
     FormFactoryInterface $formFactory,
     FlashBagInterface $flashBag,
     LoggerInterface $logger,
     UrlGeneratorInterface $urlGenerator,
     TranslatorInterface $translator,
     UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Check credentials.
     *
     * @param array         $credentials Credentials containing password
     * @param UserInterface $user        User to match with password
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check the user's password or other credentials and return true or false
        $password = $credentials['password'];

        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    /**
     * Get credentials.
     *
     * @param Request $request the request submitted by form
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        //We create the form and handle the request
        $form = $this->formFactory->create(LoginFormType::class);

        //Magic!
        $form->handleRequest($request);

        //Store the identifier to push it in the login form when credential errors occurred..
        $credentials = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['mail']
        );

        return $credentials;
    }

    /**
     * Get user.
     *
     * @param mixed                 $credentials  the credentials
     * @param UserProviderInterface $userProvider the user provider
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Load user by calling the user provider.
        return $userProvider->loadUserByUsername($credentials['mail']);
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request                 $request   the request is not use here
     * @param AuthenticationException $exception the exception to log credentials error
     *
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $credentials = $exception->getToken()->getCredentials();
        $mail = $credentials['mail'] ?? 'none provided';

        //We log connection.
        $this->logger->notice("Connection failed with mail(%{$mail}%) Reason: %{$exception->getMessage()}%");

        $this->flashBag->add('error', 'security.connection.failed');

        return new RedirectResponse($this->urlGenerator->generate('security_login'));
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request        $request     the request to redirect user
     * @param TokenInterface $token       the token to extract username
     * @param string         $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //We log connection.
        $this->logger->notice('Connection successful: %username%', [
            '%username%' => $token->getUser()->getUsername(),
        ]);

        //Message for interface.
        $this->flashBag->add(
            'success',
            $this->translator->trans('security.connection.successful %username%', [
                '%username%' => $token->getUser()->getUsername(),
            ]));

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * So it return true when we are on login page and posting some information.
     *
     * @see https://symfonycasts.com/screencast/symfony4-upgrade/sf34-deprecations#deprecation-guardauthenticator-supports
     *
     * @param Request $request the request to test if route is login page
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'security_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Return the login url.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('security_login');
    }
}
