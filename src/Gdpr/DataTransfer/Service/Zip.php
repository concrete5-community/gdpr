<?php

namespace A3020\Gdpr\DataTransfer\Service;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Utility\Service\Identifier;
use Illuminate\Filesystem\Filesystem;

class Zip implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var \Concrete\Core\File\Service\Zip
     */
    private $coreZipService;
    /**
     * @var \Concrete\Core\Logging\Logger
     */
    private $logger;
    /**
     * @var Identifier
     */
    private $identifier;

    public function __construct(Filesystem $filesystem, \Concrete\Core\File\Service\Zip $coreZipService, \Concrete\Core\Logging\Logger $logger, Identifier $identifier)
    {
        $this->filesystem = $filesystem;
        $this->coreZipService = $coreZipService;
        $this->logger = $logger;
        $this->identifier = $identifier;
    }

    /**
     * @param string $json
     * @param array $files The paths should be absolute!
     * @param array $options
     *
     * @throws \Exception
     *
     * @return string
     */
    public function makeZip($json, array $files = [], array $options = [])
    {
        $identifier = $this->identifier->getString(32);

        // This is the file name of the zip
        if (!isset($options['destinationFileName'])) {
            $options['destinationFileName'] = $identifier.'.zip';
        }

        // This is where the .zip file will be placed (absolute path)
        if (!isset($options['destinationDirectory'])) {
            $options['destinationDirectory'] = DIR_FILES_UPLOADED_STANDARD.'/data_transfers';
        }

        // This is a tmp folder where we move the files to before archiving them
        $sourceDirectory = $options['destinationDirectory'].'/'.$identifier;

        $this->filesystem->makeDirectory($sourceDirectory, DIRECTORY_PERMISSIONS_MODE_COMPUTED, true);
        $this->filesystem->put($sourceDirectory.'/data.json', $json);

        if (count($files)) {
            $this->filesystem->makeDirectory($sourceDirectory.'/files');

            foreach ($files as $file) {
                $targetDirectory = $sourceDirectory.'/files/';
                if (!$this->copy($file['path'], $targetDirectory, basename($file['path']))) {
                    $this->logger->addError(t("Couldn't copy file from %s to %s.", $file['path'], $targetDirectory));
                }
            }
        }

        $this->coreZipService->zip(
            $sourceDirectory,
            $options['destinationDirectory'].'/'.$options['destinationFileName']
        );

        // Remove tmp directory
        $this->filesystem->deleteDirectory($sourceDirectory);

        // Return the absolute path to the ZIP file
        return $options['destinationDirectory'].'/'.$options['destinationFileName'];
    }

    /**
     * @param string $origin
     * @param string $targetDirectory
     * @param string $filename
     *
     * @return bool
     */
    protected function copy($origin, $targetDirectory, $filename)
    {
        if (!file_exists($origin)) {
            return false;
        }

        // There is a possibility the file already exists
        // if so, append a random number to the filename
        if (file_exists($targetDirectory.'/'.$filename)) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = $name.'_'.rand().'.'.$extension;
        }

        return @copy($origin, $targetDirectory.'/'.$filename);
    }
}
