<?php

namespace A3020\Gdpr\DataTransfer;

use A3020\Gdpr\DataTransfer\File\Creator;
use A3020\Gdpr\DataTransfer\Service\Zip;
use A3020\Gdpr\Entity\DataTransferFile;
use A3020\Gdpr\Event\ProcessDataTransferRequest;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Mail\Service;
use Concrete\Core\Support\Facade\Url;

class Processor implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Service
     */
    private $mailService;
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Creator
     */
    private $fileCreator;
    /**
     * @var Zip
     */
    private $zipService;

    public function __construct(Repository $config, Zip $zipService, Service $mailService, Creator $fileCreator)
    {
        $this->mailService = $mailService;
        $this->config = $config;
        $this->fileCreator = $fileCreator;
        $this->zipService = $zipService;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function process(User $user)
    {
        $event = new ProcessDataTransferRequest($user);

        // Other packages / code can hook into this event and add
        // information or files to the ZIP file.
        $this->app['director']->dispatch('on_gdpr_process_data_transfer_request', $event);

        // Make the zip, and get the relative path to the file
        $zipFile = $this->zipService->makeZip(json_encode($event), $event->getFiles());
        $zipFile = str_replace(DIR_BASE, '', $zipFile);

        $dataTransferFile = $this->fileCreator->save(
            $user,
            $zipFile
        );

        return $this->sendEmail($user, $dataTransferFile);
    }

    /**
     * @param User $user
     * @param DataTransferFile $dataTransferFile
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function sendEmail(User $user, DataTransferFile $dataTransferFile)
    {
        $this->mailService->to($user->getUserEmail());
        $this->mailService->addParameter('dateHelper', $this->app->make(\Concrete\Core\Localization\Service\Date::class));
        $this->mailService->addParameter('dataTransferFile', $dataTransferFile);
        $this->mailService->addParameter('user', $user);
        $this->mailService->addParameter('downloadLink', Url::to('/gdpr/data_transfer', 'download', $dataTransferFile->getHash()));
        $this->mailService->load('data_transfer', 'gdpr');

        return $this->mailService->sendMail();
    }
}
