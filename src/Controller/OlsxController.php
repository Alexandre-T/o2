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

use Alexandre\EvcBundle\Exception\CredentialException;
use Alexandre\EvcBundle\Exception\EvcException;
use Alexandre\EvcBundle\Exception\LogicException;
use Alexandre\EvcBundle\Exception\NetworkException;
use Alexandre\EvcBundle\Service\EvcServiceInterface;
use App\Entity\User;
use App\Form\Model\OlsxRegister;
use App\Form\OlsxRegisterFormType;
use App\Mailer\MailerInterface;
use App\Manager\UserManager;
use Doctrine\ORM\Query\QueryException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
 * @Security("is_granted('ROLE_USER')")
 */
class OlsxController extends AbstractPaginateController
{
    public const LIMIT_PER_PAGE = 25;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OlsxController constructor.
     *
     * @param LoggerInterface $logger the logger is used to log each EvcException
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Activate a personal customer so he can buy OLSX credit.
     *
     * @Route("/olsx/activate/{customer}", name="accountant_olsx_activate", methods={"get"})
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     *
     * @param User                $customer    Customer in our database
     * @param EvcServiceInterface $evcService  Evc Service is used to retrieve some information about customer
     * @param UserManager         $userManager User manager to convert user
     */
    public function activate(User $customer, EvcServiceInterface $evcService, UserManager $userManager): Response
    {
        $olsxIdentifier = $customer->getOlsxIdentifier();
        $response = $this->redirectToRoute('accountant_registering_show', ['customer' => $customer->getId()]);

        try {
            if (!$evcService->isPersonal($olsxIdentifier)) {
                $this->addFlash('error', 'flash.olsx.not-personal');

                return $response;
            }

            $userManager->activateOlsx($customer);
            $this->addFlash('success', 'flash.olsx.activated');
        } catch (EvcException $exception) {
            $this->analyze($exception);
        }

        return $response;
    }

    /**
     * Convert a customer to a personal customer.
     *
     * @Route("/olsx/personal/{customer}", name="accountant_olsx_personal", methods={"get"})
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     *
     * @param User                $customer    Customer in our database
     * @param EvcServiceInterface $evcService  Evc Service is used to retrieve some information about customer
     * @param UserManager         $userManager User manager to convert user
     */
    public function personal(User $customer, EvcServiceInterface $evcService, UserManager $userManager): Response
    {
        $olsxIdentifier = $customer->getOlsxIdentifier();
        $response = $this->redirectToRoute('accountant_registering_show', ['customer' => $customer->getId()]);

        try {
            if (!$evcService->exists($olsxIdentifier)) {
                $this->addFlash('error', 'flash.olsx.not-exists');

                return $response;
            }

            if ($evcService->isPersonal($olsxIdentifier)) {
                $this->addFlash('error', 'flash.olsx.already-personal');

                return $response;
            }

            $userManager->convertAsPersonal($customer, $evcService);
            $this->addFlash('success', 'flash.olsx.converted-to-personal');
        } catch (EvcException $exception) {
            $this->analyze($exception);
        }

        return $response;
    }

    /**
     * Registering to OLSX service.
     *
     * @Route("/olsx/register", name="olsx_register", methods={"get", "post"})
     *
     * @param Request             $request     request handling data
     * @param UserManager         $userManager the user manager to save updates
     * @param EvcServiceInterface $evcService  evc service
     * @param MailerInterface     $mailer      to sent a mail on success
     */
    public function register(
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
            $this->addFlash('success', 'flash.olsx.registering');
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

    /**
     * Accountant (current user) list all registering customers for the OLSX Service.
     *
     * @Route("/olsx/registering", name="accountant_olsx_registering", methods={"get"})
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     *
     * @param Request     $request     the request contains pagination parameters
     * @param UserManager $userManager the userManager
     *
     * @throws QueryException when an error occurred on query
     */
    public function registering(Request $request, UserManager $userManager): Response
    {
        if (!$this->validateSortedField($request, ['customers', 'identifiers'])) {
            return $this->redirectToRoute('accountant_olsx_registering');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'customers');
        $sort = $this->getOrder($request, 'desc');

        $pagination = $userManager->paginateRegisteringUsers(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('accountant/olsx/registering.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a customer entity.
     *
     * @Route("/olsx/show/{customer}", name="accountant_registering_show", methods={"get"})
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     *
     * @param User                $customer   Customer in our database
     * @param EvcServiceInterface $evcService Evc Service is used to retrieve some information about customer
     */
    public function showRegistering(User $customer, EvcServiceInterface $evcService): Response
    {
        $id = $customer->getOlsxIdentifier();
        $isPersonal = $exists = false;
        $account = null;

        try {
            $exists = $evcService->exists($id);
            if ($exists) {
                $isPersonal = $evcService->isPersonal($id);
            }

            if ($isPersonal) {
                $account = $evcService->checkAccount($id);
            }
        } catch (EvcException $exception) {
            $this->analyze($exception);

            return $this->redirectToRoute('home');
        }

        return $this->render('accountant/olsx/show.html.twig', [
            'customer' => $customer,
            'exists' => $exists,
            'personal' => $isPersonal,
            'account' => $account,
        ]);
    }

    /**
     * Test the EVC service for administrator.
     *
     * @Route("/olsx/test", name="administration_olsx_test", methods={"get"})
     *
     * @Security("is_granted('ROLE_ACCOUNTANT') or is_granted('ROLE_ADMIN')")
     *
     * @param EvcServiceInterface $evcService Evc Service is used to retrieve some information about customer
     */
    public function test(EvcServiceInterface $evcService): Response
    {
        $message = '';

        try {
            $evcService->exists(33333);
            $this->addFlash('success', 'flash.olsx.test-ok');
        } catch (EvcException $exception) {
            $this->analyze($exception);
            $message = $exception->getMessage();
        }

        return $this->render('administration/olsx/test.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * Unactivate a personal customer so he can anymore buy OLSX credit.
     *
     * @Route("/olsx/unactivate/{customer}", name="accountant_olsx_unactivate", methods={"get"})
     *
     * @Security("is_granted('ROLE_ACCOUNTANT')")
     *
     * @param User        $customer    Customer in our database
     * @param UserManager $userManager User manager to convert user
     */
    public function unactivate(User $customer, UserManager $userManager): Response
    {
        $userManager->unactivateOlsx($customer);
        $this->addFlash('success', 'flash.olsx.unactivated');

        return $this->redirectToRoute('accountant_registering_show', ['customer' => $customer->getId()]);
    }

    /**
     * When an error is catch, this add a flash message and log error.
     *
     * @param EvcException $exception the exception to analyze
     */
    private function analyze(EvcException $exception): void
    {
        $this->logger->error($exception->getMessage());

        if ($exception instanceof NetworkException) {
            $this->addFlash('error', 'flash.olsx.network-exception');
        }

        if ($exception instanceof CredentialException) {
            $this->addFlash('error', 'flash.olsx.credential-exception');
        }

        if ($exception instanceof LogicException) {
            $this->addFlash('error', 'flash.olsx.logic-exception');
        }
    }
}
