<?php

namespace A3020\Gdpr\Listener\OnProcessDataTransferRequest;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileList;

class SubmitData implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param \A3020\Gdpr\Event\ProcessDataTransferRequest $event
     */
    public function handle($event)
    {
        if (!$this->config->get('gdpr.settings.data_transfer.submit_data')) {
            return;
        }

        $user = $event->getUser();

        $event->addData([
            'Username' => $user->getUserName(),
            'Email Address' => $user->getUserEmail(),
        ]);

        /** @var FileList $fileList */
        $fileList = $this->app->make(FileList::class);
        $fileList->filterByAuthorUserID($user->getUserID());

        foreach ($fileList->getResults() as $file) {
            /** @var File $file */
            $event->addFile(DIR_BASE.'/'.$file->getVersion()->getRelativePath());
        }
    }
}
