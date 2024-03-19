<?php

namespace A3020\Gdpr\Form\Task;

use A3020\Gdpr\Form\Command\DeleteLegacyFormEntriesCommand;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\CommandTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;

class DeleteLegacyFormEntriesController extends AbstractController
{
    public function getName(): string
    {
        return t('GDPR - Remove legacy form submissions');
    }

    public function getDescription(): string
    {
        return t('Automatically remove legacy form submissions stored in Form Results. Important: there is no way to restore form submissions once they are removed!');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new CommandTaskRunner($task, new DeleteLegacyFormEntriesCommand(), t('Done.'));
    }

}