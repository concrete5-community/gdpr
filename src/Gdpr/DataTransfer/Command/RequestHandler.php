<?php

namespace A3020\Gdpr\DataTransfer\Command;

use A3020\Gdpr\DataTransfer\Processor;
use A3020\Gdpr\DataTransfer\RequestRepository;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Throwable;

class RequestHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    public function __invoke(Request $command)
    {
        $requestID = $command->getRequestID();

        try {
            $this->processRequest($requestID);
        } catch (Throwable $e) {
            $app = Application::getFacadeApplication();
            $logger = $app->make('log');
            $logger->debug($e->getMessage() . $e->getFile() . $e->getLine() . $e->getTraceAsString());

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
        $app = Application::getFacadeApplication();
        $requestRepository = $app->make(RequestRepository::class);
        $processor = $app->make(Processor::class);

        $dataTransferRequest = $requestRepository->find($id);
        if (!$dataTransferRequest) {
            return false;
        }

        // User could've been removed in the meanwhile
        $user = $dataTransferRequest->getUser();
        if (!$user) {
            return false;
        }

        if (!$processor->process($user)) {
            throw new Exception(t('An error occurred when sending the email.'));
        }

        $requestRepository->markAsMailed($dataTransferRequest);

        return true;
    }
}