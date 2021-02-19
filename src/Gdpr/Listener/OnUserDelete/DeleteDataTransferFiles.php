<?php

namespace A3020\Gdpr\Listener\OnUserDelete;

use A3020\Gdpr\DataTransfer\File\FileRepository;
use A3020\Gdpr\Entity\DataTransferFile;
use Concrete\Core\Logging\Logger;
use Exception;

class DeleteDataTransferFiles
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(FileRepository $fileRepository, Logger $logger)
    {
        $this->fileRepository = $fileRepository;
        $this->logger = $logger;
    }

    /**
     * Delete data transfer files
     *
     * In case:
     * - A user first requests his / her data
     * - Then closes the account
     * - The stored data transfer file should be deleted
     *
     * @param \Concrete\Core\User\Event\DeleteUser $event
     */
    public function handle($event)
    {
        try {
            $ui = $event->getUserInfoObject();

            foreach ($this->fileRepository->findByUser($ui->getEntityObject()) as $dataTransferFile) {
                $this->deleteFile($dataTransferFile);
            }
        } catch (Exception $e) {
            $this->logger->addDebug($e->getMessage());
        }
    }

    /**
     * @param DataTransferFile $dataTransferFile
     *
     * @return bool
     */
    private function deleteFile(DataTransferFile $dataTransferFile)
    {
        $file = DIR_BASE.$dataTransferFile->getFileLocation();
        if (!file_exists($file)) {
            return false;
        }

        return @unlink($file);
    }
}
