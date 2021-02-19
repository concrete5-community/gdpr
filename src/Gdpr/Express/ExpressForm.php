<?php

namespace A3020\Gdpr\Express;

use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Doctrine\ORM\EntityManager;

class ExpressForm
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns all the Form Result nodes
     *
     * Such a node is where the form submissions are stored under.
     * Compare it to a folder structure.
     *
     * @return ExpressEntryResults[]
     */
    public function getFormResultNodes()
    {
        /** @var \Concrete\Core\Tree\Node\Node $node */
        $node = ExpressEntryCategory::getNodeByName(\Concrete\Block\ExpressForm\Controller::FORM_RESULTS_CATEGORY_NAME);
        if (!$node) {
            return [];
        }

        // Once we have the category 'Form Results', let's load all the underlying entries
        // These are in fact the folders where form submissions are stored in.
        $node->populateDirectChildrenOnly();

        $children = [];
        foreach ($node->getChildNodes() as $child) {
            /** @var \Concrete\Core\Tree\Node\Node $child */
            if (!$child instanceof ExpressEntryResults) {
                continue;
            }

            $children[] = $child;
        }

        return $children;
    }

    /**
     * Each Express Form is an entity
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return \Concrete\Core\Entity\Express\Entity[]
     */
    public function getFormResultEntities()
    {
        $entities = [];
        foreach ($this->getFormResultNodes() as $node) {
            $entity = $this->getFormResultEntityByNode($node);
            if ($entity) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * This is the express entity to save the form submission against.
     *
     * @param ExpressEntryResults $node
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return \Concrete\Core\Entity\Express\Entity
     */
    public function getFormResultEntityByNode(ExpressEntryResults $node)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByResultsNode($node);
    }
}