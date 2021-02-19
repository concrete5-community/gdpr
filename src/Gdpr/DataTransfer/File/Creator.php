<?php

namespace A3020\Gdpr\DataTransfer\File;

use A3020\Gdpr\Entity\DataTransferFile;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\User;
use DateInterval;
use DateTime;

class Creator
{
    /**
     * @var \Concrete\Core\Utility\Service\Identifier
     */
    private $identifier;
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(\Concrete\Core\Utility\Service\Identifier $identifier, FileRepository $fileRepository, Repository $config)
    {
        $this->identifier = $identifier;
        $this->fileRepository = $fileRepository;
        $this->config = $config;
    }

    /**
     * @param User $user
     * @param string $zip location of .zip file
     *
     * @return DataTransferFile
     */
    public function save(User $user, $zip)
    {
        $dtf = new DataTransferFile();
        $dtf->setUser($user);
        $dtf->setFileLocation($zip);
        $dtf->setHash($this->generateHash());
        $dtf->setValidUntil($this->getValidUntil());

        $this->fileRepository->save($dtf);
        $this->fileRepository->flush();

        return $dtf;
    }

    private function generateHash($length = 32)
    {
        return $this->identifier->getString($length);
    }

    private function getValidUntil()
    {
        $daysValid = (int) $this->config->get('gdpr.settings.data_transfer.days_valid', 10);
        $date = new DateTime();
        $date->add(new DateInterval('P'.$daysValid.'D'));

        return $date;
    }
}
