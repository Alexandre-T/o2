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

use App\Entity\File;
use App\Entity\Programmation;
use App\Exception\SettingsException;
use App\Form\Model\UploadProgrammation;
use App\Form\UploadProgrammationFormType;
use App\Mailer\MailerInterface;
use App\Manager\ProgrammationManager;
use App\Manager\SettingsManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Programmer controller.
 *
 * @Route("/programmer", name="programmer_")
 *
 * @Security("is_granted('ROLE_PROGRAMMER')")
 */
class ProgrammerController extends AbstractPaginateController
{
    /**
     * Limit of programmations per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Download programmation final file.
     *
     * @Route("/download-final/{id}", name="download_final", methods={"get"})
     *
     * @param DownloadHandler $downloadHandler Vich download helper
     * @param Programmation   $programmation   The programmation to display
     *
     * @return Response|StreamedResponse
     */
    public function downloadFinal(DownloadHandler $downloadHandler, Programmation $programmation): Response
    {
        if (empty($programmation->getFinalFile())) {
            $this->addFlash('warning', 'flash.programmation.final-file.not-available');

            return $this->redirectToRoute('programmer_show', [
                'id' => $programmation->getId(),
            ]);
        }

        //Force download
        $file = $programmation->getFinalFile();
        $filename = mb_convert_encoding($file->getOriginalName(), 'ASCII');

        return $downloadHandler->downloadObject($file, 'file', null, $filename);
    }

    /**
     * Download programmation original file.
     *
     * @Route("/download-original/{id}", name="download_original", methods={"get"})
     *
     * @param DownloadHandler $downloadHandler Vich download helper
     * @param Programmation   $programmation   The programmation to display
     *
     * @return Response|StreamedResponse
     */
    public function downloadOriginal(DownloadHandler $downloadHandler, Programmation $programmation): Response
    {
        $file = $programmation->getOriginalFile();
        $filename = mb_convert_encoding($file->getOriginalName(), 'ASCII');

        return $downloadHandler->downloadObject($file, 'file', null, $filename);
    }

    /**
     * Lists all programmation.
     *
     * @Route("/list", name="list", methods={"get"})
     *
     * @param ProgrammationManager $programmationManager the user manage to paginate users
     * @param Request              $request              the requests to handle page and sorting
     *
     * @return Response|RedirectResponse
     */
    public function list(ProgrammationManager $programmationManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['createdAt', 'make', 'model', 'deliveredAt'])) {
            return $this->redirectToRoute('programmer_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'createdAt');
        $sort = $this->getOrder($request, 'desc');

        $pagination = $programmationManager->paginate(
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('programmer/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a programmation entity.
     *
     * @Route("/{id}", name="show", methods={"get"})
     *
     * @param Programmation $programmation The programmation to display
     *
     * @return Response
     */
    public function show(Programmation $programmation): Response
    {
        return $this->render('programmer/show.html.twig', [
            'programmation' => $programmation,
        ]);
    }

    /**
     * Finds and displays a programmation entity.
     *
     * @Route("/upload/{id}", name="upload", methods={"get", "post"})
     *
     * @param LoggerInterface      $logger               to log an alert when settings are missing
     * @param MailerInterface      $mailer               to send a mail to customer
     * @param ProgrammationManager $programmationManager To save programmation
     * @param Programmation        $programmation        The programmation to display
     * @param Request              $request              The request containing data form
     * @param SettingsManager      $settingsManager      To retrieve emails
     * @param TranslatorInterface  $trans                The translator
     *
     * @return Response
     */
    public function upload(
     LoggerInterface $logger,
     MailerInterface $mailer,
     ProgrammationManager $programmationManager,
     Programmation $programmation,
     Request $request,
     SettingsManager $settingsManager,
     TranslatorInterface $trans
    ): Response {
        $model = new UploadProgrammation($programmation);
        $editForm = $this->createForm(UploadProgrammationFormType::class, $model);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $model->copyProgrammation($programmation);
            $programmation->setFinalFile(new File());
            $model->copyFile($programmation->getFinalFile());
            $programmationManager->publish($programmation);
            $programmationManager->save($programmation);

            try {
                /** @var string $sender */
                $sender = $settingsManager->getValue('mail-sender');
                $mailer->sendReturningProgrammation($programmation, $sender);
            } catch (SettingsException $exception) {
                $logger->alert('Email was not sent: '.$exception->getMessage());
            }

            $this->addFlash('success', $trans->trans('entity.programmation.uploaded'));

            return $this->redirectToRoute('programmer_show', ['id' => $programmation->getId()]);
        }

        return $this->render('programmer/upload.html.twig', [
            'form' => $editForm->createView(),
            'programmation' => $programmation,
        ]);
    }
}
