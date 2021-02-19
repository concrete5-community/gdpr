<?php

namespace A3020\Gdpr\DataTransfer\File;

use A3020\Gdpr\Entity\DataTransferFile;
use A3020\Gdpr\Entity\DataTransferRequest;
use Doctrine\ORM\EntityManager;

class FileRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(DataTransferFile::class);
    }

    /**
     * @param int $id
     *
     * @return DataTransferFile|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param string $hash
     *
     * @return DataTransferFile|null
     */
    public function findByHash($hash)
    {
        return $this->repository->findOneBy([
            'hash' => $hash,
        ]);
    }

    /**
     * @return DataTransferFile[]
     */
    public function findAll()
    {
        return $this->repository->findBy([]);
    }

    public function save(DataTransferFile $dataTransferFile)
    {
        $this->entityManager->persist($dataTransferFile);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function markAsDownloaded(DataTransferFile $dataTransferFile)
    {
        $dataTransferFile->markAsDownloaded();
        $this->save($dataTransferFile);
        $this->flush();
    }
}
