<?php

namespace A3020\Gdpr\Listener\OnDataTransferRequest;

use A3020\Gdpr\DataTransfer\RequestRepository;
use A3020\Gdpr\Entity\DataTransferRequest;
use A3020\Gdpr\Event\DataTransferRequest as DataTransferRequestEvent;
use Psr\Log\LoggerInterface;
use Throwable;

class Store
{
    /**
     * @var \A3020\Gdpr\DataTransfer\RequestRepository
     */
    private $requestRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(RequestRepository $requestRepository, LoggerInterface $logger)
    {
        $this->requestRepository = $requestRepository;
        $this->logger = $logger;
    }

    /**
     * This will store a request in the database
     *
     * @param DataTransferRequestEvent $event
     */
    public function handle($event)
    {
        try {
            $user = $event->getUser();
            if (!$user) {
                return;
            }

            $dataTransferRequest = new DataTransferRequest();
            $dataTransferRequest->setUser($user);
            $dataTransferRequest->setIncludeFiles($event->shouldIncludeFiles());

            $this->requestRepository->save($dataTransferRequest);
            $this->requestRepository->flush();
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
