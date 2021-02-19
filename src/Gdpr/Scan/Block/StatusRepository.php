<?php

namespace A3020\Gdpr\Scan\Block;

use A3020\Gdpr\Entity\BlockScanStatus;
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
        $this->repository = $this->entityManager->getRepository(BlockScanStatus::class);
    }

    /**
     * @param string $blockType
     *
     * @return bool
     */
    public function isBlockTypeFixed($blockType)
    {
        return (bool) $this->repository->findOneBy([
            'blockTypeHandle' => $blockType,
            'isFixedOnAllPages' => true,
        ]);
    }

    /**
     * @param string $blockType
     * @param int $pageId
     *
     * @return bool
     */
    public function isBlockTypeFixedOnPage($blockType, $pageId)
    {
        return (bool) $this->repository->findOneBy([
            'blockTypeHandle' => $blockType,
            'pageId' => $pageId,
            'isFixed' => true,
        ]);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param string $blockType
     * @param int $pageId
     *
     * @return BlockScanStatus|null
     */
    public function findBy($blockType, $pageId)
    {
        return $this->repository->findOneBy([
            'blockTypeHandle' => $blockType,
            'pageId' => $pageId,
        ]);
    }

    public function save(BlockScanStatus $status)
    {
        $this->entityManager->persist($status);
        $this->entityManager->flush();
    }

    public function updateFixedOnAllPages($blockType, $fixedOnAllPages)
    {
        $entities = $this->repository->findBy([
            'blockTypeHandle' => $blockType,
        ]);

        if (!$entities) {
            return;
        }

        /** @var BlockScanStatus $entity */
        foreach ($entities as $entity) {
            $entity->setFixedOnAllPages($fixedOnAllPages);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }
}
