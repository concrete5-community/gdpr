<?php

namespace A3020\Gdpr\DataTransfer\Queue;

use A3020\Gdpr\DataTransfer\Processor;
use A3020\Gdpr\DataTransfer\RequestRepository;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Exception;
use Throwable;
use ZendQueue\Message as ZendQueueMessage;

class Process implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var RequestRepository
     */
    private $requestRepository;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct(RequestRepository $requestRepository, Processor $processor)
    {
        $this->requestRepository = $requestRepository;
        $this->processor = $processor;
    }

    public function process(ZendQueueMessage $msg)
    {
        try {
            $body = json_decode($msg->body, true);

            $this->processRequest($body['id']);
        } catch (Throwable $e) {
            $logger = $this->app->make('log');
            $logger->addDebug($e->getMessage() . $e->getFile() . $e->getLine() . $e->getTraceAsString());

            throw new Exception(t('An error occurred during processing. Please check the Logs.'));
        }
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function processRequest($id)
    {
        $dataTransferRequest = $this->requestRepository->find($id);
        if (!$dataTransferRequest) {
            return false;
        }

        // User could've been removed in the meanwhile
        $user = $dataTransferRequest->getUser();
        if (!$user) {
            return false;
        }

        if (!$this->processor->process($user)) {
            throw new Exception(t('An error occurred when sending the email.'));
        }

        $this->requestRepository->markAsMailed($dataTransferRequest);

        return true;
    }
}
