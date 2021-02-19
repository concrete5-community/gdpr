<?php

namespace A3020\Gdpr\BlockType;

use Concrete\Core\Package\PackageService;
use Symfony\Component\Finder\Finder;

class Scanner
{
    /**
     * @var PackageService
     */
    private $packageService;

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(PackageService $packageService, Finder $finder)
    {
        $this->packageService = $packageService;
        $this->finder = $finder;
    }

    public function getBlockTypes($options = [])
    {
        $blockTypes = [
            'core_conversation' => t('Because the %s and %s are stored.', 'commentRatingIP', 'commentRatingUserID'),
            'express_form' => t('Because an email address is stored and because certain form fields might store personal data.'),
            'form' => t('Because the %s and %s are stored and because certain form fields might store personal data.', 'recipientEmail', 'uID'),
            'd3_mailchimp' => t('Because the email address is sent to MailChimp.'),
            'mailchimp' => t('Because the email address is sent to MailChimp.'),
            'survey' => t('Because the %s and %s are stored.', 'uID', 'ipAddress'),
        ];

        if (isset($options['custom_block_types'])) {
            foreach ($options['custom_block_types'] as $handle) {
                $blockTypes[$handle] = '-';
            }
        }

        return $this->mergeWithDirectoryScan($blockTypes);
    }

    private function mergeWithDirectoryScan(array $blockTypes)
    {
        $directories = $this->getBlockDirectories();

        $this->add(
            $blockTypes,
            $this->findFilesWithForm($directories),
            t('Because a template contains a form and might process user data.')
        );

        $this->add(
            $blockTypes,
            $this->findFilesWithEmail($directories),
            t('Because it seems to send email and might process user data.')
        );

        return $blockTypes;
    }

    /**
     * @param array $blockTypes
     * @param Finder $files
     * @param string $message
     *
     * @return array
     */
    private function add(array &$blockTypes, Finder $files, $message)
    {
        foreach ($files as $file) {
            /** @var \SplFileInfo $file */
            $blockTypeHandle = reset(explode('/', $file->getRelativePath()));
            if (isset($blockTypes[$blockTypeHandle])) {
                continue;
            }

            $blockTypes[$blockTypeHandle] = $message.'<br>'.$file->getPathname();
        }

        return $blockTypes;
    }

    /**
     * @param array $directories
     *
     * @return Finder
     */
    private function findFilesWithForm(array $directories)
    {
        return $this->finder
            ->files()
            ->name('view.php')
            ->contains('<form')
            ->in($directories);
    }

    /**
     * @param array $directories
     *
     * @return Finder
     */
    private function findFilesWithEmail(array $directories)
    {
        return $this->finder
            ->files()
            ->name('*.php')
            ->contains("/mail\(|make\('mail'\)|make\('helper\/mail'\)/")
            ->in($directories);
    }

    /**
     * Return list of directories that might contain blocks
     *
     * @return array
     */
    private function getBlockDirectories()
    {
        $directories = [
            DIR_FILES_BLOCK_TYPES,
        ];

        foreach ($this->packageService->getInstalledHandles() as $packageHandle) {
            $directories[] = DIR_PACKAGES.'/'.$packageHandle.'/'.DIRNAME_BLOCKS;
        }

        return array_filter($directories, function($directory) {
            return is_dir($directory);
        });
    }
}
