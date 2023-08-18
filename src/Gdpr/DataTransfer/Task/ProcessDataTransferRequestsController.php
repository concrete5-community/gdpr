<?php

namespace A3020\Gdpr\DataTransfer\Task;

use A3020\Gdpr\DataTransfer\Command\Request;
use A3020\Gdpr\DataTransfer\RequestRepository;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Support\Facade\Application;

class ProcessDataTransferRequestsController extends AbstractController
{
    public function getName(): string
    {
        return t('GDPR - Process data transfer requests');
    }

    public function getDescription(): string
    {
        return t('Sends an email with a link to a ZIP file from where the user can download his / her data.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $app = Application::getFacadeApplication();
        /** @var RequestRepository $requestRepository */
        $requestRepository = $app->make(RequestRepository::class);

        $batch = Batch::create();
        foreach ($requestRepository->findNotProcessed() as $request) {
            $batch->add(new Request($request->getId()));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Processing data transfer requests...'));
    }
}
