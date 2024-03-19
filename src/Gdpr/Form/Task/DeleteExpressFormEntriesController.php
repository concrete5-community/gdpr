<?php

namespace A3020\Gdpr\Form\Task;

use A3020\Gdpr\Form\Command\DeleteExpressFormEntriesCommand;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\CommandTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;

class DeleteExpressFormEntriesController extends AbstractController
{
    public function getName(): string
    {
        return t('GDPR - Remove express form submissions');
    }

    public function getDescription(): string
    {
        return t('Automatically remove express form submissions stored in Form Results. Important: there is no way to restore form submissions once they are removed!');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new CommandTaskRunner($task, new DeleteExpressFormEntriesCommand(), t('Done.'));
    }

}