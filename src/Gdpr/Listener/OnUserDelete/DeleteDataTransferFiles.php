<?php

namespace A3020\Gdpr\Listener\OnUserDelete;

use A3020\Gdpr\DataTransfer\File\FileRepository;
use A3020\Gdpr\Entity\DataTransferFile;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class DeleteDataTransferFiles
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function __construct(FileRepository $fileRepository, LoggerInterface $logger, Filesystem $fileSystem)
    {
        $this->fileRepository = $fileRepository;
        $this->logger = $logger;
        $this->fileSystem = $fileSystem;
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
            $this->logger->debug($e->getMessage());
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

        return $this->fileSystem->delete($file);
    }
}
