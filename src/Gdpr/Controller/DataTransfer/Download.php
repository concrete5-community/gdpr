<?php

namespace A3020\Gdpr\Controller\DataTransfer;

use A3020\Gdpr\DataTransfer\File\FileRepository;
use A3020\Gdpr\DataTransfer\RequestRepository;
use A3020\Gdpr\Entity\DataTransferFile;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\File\Service\File;
use Concrete\Core\Http\ResponseFactory;
use Exception;

class Download extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var File
     */
    private $fileService;

    /**
     * @var RequestRepository
     */
    private $fileRepository;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(File $fileService, FileRepository $fileRepository, ResponseFactory $responseFactory)
    {
        parent::__construct();

        $this->fileService = $fileService;
        $this->fileRepository = $fileRepository;
        $this->responseFactory = $responseFactory;
    }

    public function download($hash = null)
    {
        if (!$hash) {
            return $this->responseFactory->notFound('');
        }

        /** @var DataTransferFile $dataTransferFile */
        $dataTransferFile = $this->fileRepository->findByHash($hash);
        if (!$dataTransferFile) {
            return $this->responseFactory->notFound('');
        }

        if (!$dataTransferFile->isValid()) {
            return $this->responseFactory->notFound(t('Download has expired'));
        }

        $file = DIR_BASE.$dataTransferFile->getFileLocation();
        if (!file_exists($file)) {
            return $this->responseFactory->notFound(t('File not found'));
        }

        try {
            $this->fileRepository->markAsDownloaded($dataTransferFile);
        } catch (Exception $e) {
            \Log::addError($e->getMessage());
        }

        $this->fileService->forceDownload($file);
    }
}
