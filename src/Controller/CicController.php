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

use App\Model\TpeConfig;
use Ekyna\Component\Payum\Monetico\Api\Api;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CicController
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * CicController constructor.
     *
     * @param LoggerInterface $log the logger for tpe
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Return from cic bank.
     *
     * @Route("/retour-cic", name="cic-return", methods={"post", "get"})
     *
     * @param TpeConfig $tpeConfig the tpe config
     *
     * @return Response
     */
    public function cic(TpeConfig $tpeConfig): Response
    {
        $data = empty($_POST) ? $_GET : $_POST;

        if (null === $data) {
            return new Response(Api::NOTIFY_FAILURE);
        }

        $this->log->info(json_encode($data));

        $configuration = $tpeConfig->getConfiguration();
        $api = new Api();
        $api->setConfig($configuration);

        if (!$api->checkPaymentResponse($data)) {
            return new Response(Api::NOTIFY_FAILURE);
        }

        return new Response(Api::NOTIFY_SUCCESS);
    }
}
