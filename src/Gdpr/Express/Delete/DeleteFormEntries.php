<?php

namespace A3020\Gdpr\Express\Delete;

use A3020\Gdpr\Express\ExpressForm;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Doctrine\ORM\EntityManager;

class DeleteFormEntries
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var ExpressForm
     */
    private $expressForm;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(ObjectManager $manager, ExpressForm $expressForm, EntityManager $entityManager)
    {
        $this->manager = $manager;
        $this->expressForm = $expressForm;
        $this->entityManager = $entityManager;
    }

    /**
     * @return int
     * @param array $options
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete($options = [])
    {
        $deleted = 0;
        foreach ($this->expressForm->getFormResultEntities() as $entity) {
            $deleted += $this->deleteByEntity($entity, $options);
        }

        return $deleted;
    }

    /**
     * Delete by results folder, like on /dashboard/reports/forms/view/xxx
     *
     * @param ExpressEntryResults $node
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteByNode(ExpressEntryResults $node)
    {
        $entity = $this->expressForm->getFormResultEntityByNode($node);
        if ($entity) {
            $this->deleteByEntity($entity);
        }
    }

    /**
     * @param \Concrete\Core\Entity\Express\Entity $entity
     * @param array $options
     *
     * @return int
     */
    public function deleteByEntity(\Concrete\Core\Entity\Express\Entity $entity, $options = [])
    {
        $entryList = new EntryList($entity);

        if (isset($options['created_before'])) {
            $entryList->getQueryObject()
                ->andWhere('exEntryDateCreated < :entry_date_created')
                ->setParameter('entry_date_created', $options['created_before'], \Doctrine\DBAL\Types\Type::DATETIME);
        }

        $deleted = 0;
        foreach ($entryList->getResults() as $entry) {
            $this->manager->deleteEntry($entry);
            $deleted++;
        }

        return $deleted;
    }
}
