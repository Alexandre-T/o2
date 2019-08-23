<?php

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
     * @param LoggerInterface $log the logger for TPE
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Return from cic bank.
     *
     * @Route("/retour-cic", name="cic-return", methods={"post","get"})
     *
     * @param TpeConfig $tpeConfig
     *
     * @return Response
     */
    public function cic(TpeConfig $tpeConfig): Response
    {
        $data = empty($_POST)?$_GET:$_POST;

        if (null === $data) {
            return new Response(Api::NOTIFY_FAILURE);
        }

        $this->log->info(implode(';', $data));

        $configuration = $tpeConfig->getConfiguration();
        $api = new Api();
        $api->setConfig($configuration);

        if (!$api->checkPaymentResponse($data)) {
            return new Response(Api::NOTIFY_FAILURE);
        }

        return new Response(Api::NOTIFY_SUCCESS);
    }
}