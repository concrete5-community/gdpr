<?php

namespace A3020\Gdpr\DataTransfer;

use A3020\Gdpr\Entity\DataTransferRequest;
use Doctrine\ORM\EntityManager;

class RequestRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(DataTransferRequest::class);
    }

    /**
     * @param int $id
     *
     * @return DataTransferRequest|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return DataTransferRequest[]
     */
    public function findAll()
    {
        return $this->repository->findBy([], ['requestedAt' => 'desc']);
    }

    /**
     * @return DataTransferRequest[]
     */
    public function findNotProcessed()
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('dtr')
            ->from(DataTransferRequest::class, 'dtr')
            ->where($qb->expr()->isNull('dtr.mailedAt'))

            // We need dashboard functionality for this first
            //->andWhere($qb->expr()->isNotNull('dtr.approvedAt'))

            ->getQuery()
            ->getResult();
    }

    public function save(DataTransferRequest $dataTransferRequest)
    {
        $this->entityManager->persist($dataTransferRequest);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function markAsMailed(DataTransferRequest $dataTransferRequest)
    {
        $dataTransferRequest->markAsMailed();
        $this->save($dataTransferRequest);
        $this->flush();
    }
}
