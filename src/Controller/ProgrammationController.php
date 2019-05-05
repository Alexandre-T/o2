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

use App\Entity\Programmation;
use App\Manager\ProgrammationManager;
use App\Security\Voter\ProgrammationVoter;
use Doctrine\ORM\Query\QueryException as QueryExceptionAlias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Programmation controller.
 *
 * @Route("/customer/programmation", name="customer_programmation_")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class ProgrammationController extends AbstractPaginateController
{
    /**
     * Limit of programmations per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Download programmation final file.
     *
     * @Route("/download/{id}", name="download", methods={"get"})
     *
     * @param DownloadHandler $downloadHandler Vich download helper
     * @param Programmation   $programmation   The programmation to display
     *
     * @return Response|StreamedResponse
     */
    public function download(DownloadHandler $downloadHandler, Programmation $programmation): Response
    {
        // check for "show" access: calls all voters
        $this->denyAccessUnlessGranted(ProgrammationVoter::SHOW, $programmation);

        if (empty($programmation->getFinalFile())) {
            $this->addFlash('warning', 'flash.programmation.final-file.not-available');
            $this->redirectToRoute('customer_programmation_show', [
                'id' => $programmation->getId(),
            ]);
        }

        //Force download
        $file = $programmation->getFinalFile();
        $filename = $file->getOriginalName();

        return $downloadHandler->downloadObject($file, 'name', null, $filename);
    }

    /**
     * Lists all programmation for current customer.
     *
     * @Route("/list", name="list", methods={"get"})
     *
     * @param ProgrammationManager $programmationManager the user manage to paginate users
     * @param Request              $request              the requests to handle page and sorting
     *
     * @throws QueryExceptionAlias this should not happened
     *
     * @return Response|RedirectResponse
     */
    public function list(ProgrammationManager $programmationManager, Request $request): Response
    {
        if (!$this->validateSortedField($request, ['createdAt', 'deliveredAt'])) {
            return $this->redirectToRoute('customer_programmation_list');
        }

        //Query parameters check
        $field = $this->getSortedField($request, 'createdAt');
        $sort = $this->getOrder($request, 'desc');

        $pagination = $programmationManager->paginateWithUser(
            $this->getUser(),
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            $field,
            $sort
        );

        return $this->render('customer/programmation/list.html.twig', [
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
        // check for "show" access: calls all voters
        $this->denyAccessUnlessGranted(ProgrammationVoter::SHOW, $programmation);

        return $this->render('customer/programmation/show.html.twig', [
            'programmation' => $programmation,
        ]);
    }
}
