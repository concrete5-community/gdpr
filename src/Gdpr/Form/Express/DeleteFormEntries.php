<?php

namespace A3020\Gdpr\Form\Express;

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
     * @var ExpressFormHelper
     */
    private $expressForm;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(ObjectManager $manager, ExpressFormHelper $expressForm, EntityManager $entityManager)
    {
        $this->manager = $manager;
        $this->expressForm = $expressForm;
        $this->entityManager = $entityManager;
    }

    /**
     * Remove all express form submissions
     *
     * We can pass a date, e.g. to only remove submission
     * that are older than a week. By default all submissions are removed.
     *
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
     * @param array $options
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteByNode(ExpressEntryResults $node, $options = [])
    {
        $entity = $this->expressForm->getFormResultEntityByNode($node);
        if ($entity) {
            $this->deleteByEntity($entity, $options);
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
            if (isset($options['delete_files']) && $options['delete_files']) {
                $this->deleteFiles($entry);
            }

            $this->manager->deleteEntry($entry);
            $deleted++;
        }

        return $deleted;
    }

    /**
     * @param \Concrete\Core\Entity\Express\Entry $entry
     */
    private function deleteFiles($entry)
    {
        // Go through the submission attributes
        foreach ($entry->getAttributes() as $attribute) {
            /** @var \Concrete\Core\Entity\Attribute\Value\ExpressValue $attribute */

            /** @var \Concrete\Core\Entity\Attribute\Type $type */
            $type = $attribute->getAttributeTypeObject();

            // Skip all attributes that are not a file upload attribute
            if ($type->getAttributeTypeHandle() !== 'image_file') {
                continue;
            }

            /** @var \Concrete\Core\Entity\File\File $file */
            $file = $attribute->getValue();

            if (!is_object($file)) {
                continue;
            }

            $file->delete();
        }
    }
}
