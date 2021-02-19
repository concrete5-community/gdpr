<?php

namespace Concrete\Package\Gdpr\Job;

use A3020\Gdpr\DataTransfer\Queue\Create;
use A3020\Gdpr\DataTransfer\Queue\Finish;
use A3020\Gdpr\DataTransfer\Queue\Process;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Support\Facade\Application;

final class GdprProcessDataTransferRequests extends QueueableJob
{
    protected $jQueueBatchSize = 1;

    /** @var \Concrete\Core\Application\Application
     * Not named 'app' on purpose because parent class might change
     */
    private $appInstance;

    public function getJobName()
    {
        return t('GDPR - Process data transfer requests');
    }

    public function getJobDescription()
    {
        return t('Sends an email with a link to a ZIP file from where the user can download his / her data.');
    }

    public function __construct()
    {
        $this->appInstance = Application::getFacadeApplication();

        parent::__construct();
    }

    /**
     * Start the job by creating a queue.
     *
     * @param \ZendQueue\Queue $q
     */
    public function start(\ZendQueue\Queue $q)
    {
        $queue = $this->appInstance->make(Create::class);
        $queue->create($q);
    }

    /**
     * Process a QueueMessage.
     *
     * @param \ZendQueue\Message $msg
     */
    public function processQueueItem(\ZendQueue\Message $msg)
    {
        $queue = $this->appInstance->make(Process::class);
        $queue->process($msg);
    }

    /**
     * Finish processing a queue.
     *
     * @param \ZendQueue\Queue $q
     *
     * @return mixed
     */
    public function finish(\ZendQueue\Queue $q)
    {
        $queue = $this->appInstance->make(Finish::class);

        return $queue->finish($q);
    }
}
