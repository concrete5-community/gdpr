<?php

namespace A3020\Gdpr\Check;

use A3020\Gdpr\Entity\Check;
use Doctrine\ORM\EntityManager;

class CheckRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Check::class);
    }

    /**
     * @param int $id
     *
     * @return Check|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return Check[]
     */
    public function findAll()
    {
        return $this->repository->findBy([], ['category' => 'asc']);
    }

    public function updateOrCreate(Check $check)
    {
        /** @var Check $entity */
        $entity = $this->repository->findOneBy([
            'handle' => $check->getHandle(),
        ]);

        if ($entity) {
            $entity->setName($check->getName());
            $entity->setDescription($check->getDescription());
            $entity->setCategory($check->getCategory());
            $check = $entity;
        }

        $this->save($check);
    }

    public function save(Check $check)
    {
        $this->entityManager->persist($check);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }
}
