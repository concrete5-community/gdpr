<?php

namespace A3020\Gdpr\Installer;

use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class TaskInstaller
{
    /** @var TaskService */
    protected $service;

    /** @var Package */
    protected $package;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(TaskService $service, Package $package, EntityManagerInterface $entityManager)
    {
        $this->service = $service;
        $this->package = $package;
        $this->entityManager = $entityManager;
    }

    public function isInstalled($taskHandle): bool
    {
        return (bool) $this->service->getByHandle($taskHandle);
    }

    public function install($taskHandle): void
    {
        if (!$this->service->getByHandle($taskHandle)) {
            $task = new Task();
            $task->setHandle($taskHandle);
            $task->setPackage($this->package);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }
    }

    public function uninstall($taskHandle): void
    {
        $task = $this->service->getByHandle($taskHandle);
        if ($task) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }
    }
}