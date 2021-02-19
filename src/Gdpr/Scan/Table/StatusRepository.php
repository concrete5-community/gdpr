<?php

namespace A3020\Gdpr\Scan\Table;

use A3020\Gdpr\Entity\TableScanStatus;
use Doctrine\ORM\EntityManager;

class StatusRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(TableScanStatus::class);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param string $tableName
     *
     * @return TableScanStatus|null
     */
    public function findByTableName($tableName)
    {
        return $this->repository->findOneBy([
            'tableName' => $tableName,
        ]);
    }

    /**
     * @param string $tableName
     *
     * @return bool
     */
    public function isTableFixed($tableName)
    {
        return (bool) $this->findByTableName($tableName);
    }

    public function save(TableScanStatus $status)
    {
        $this->entityManager->persist($status);
        $this->entityManager->flush();
    }
}
