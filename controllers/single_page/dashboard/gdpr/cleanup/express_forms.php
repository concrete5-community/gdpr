<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Exception;

final class ExpressForms extends DashboardController
{
    public function view()
    {
        $formInformation = false;

        try {
            // Express is likely to fail, so we'll catch and log errors
            $formInformation = $this->getFormInformation();
        } catch (Exception $e) {
            \Log::addError($e->getMessage());
        }

        $this->set('formInformation', $formInformation);
    }

    /**
     * This removes all form submissions for a certain Express Form.
     *
     * @param int $nodeId
     * @param string $token
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function deleteEntries($nodeId = null, $token = null)
    {
        if (!$this->token->validate('gdpr.cleanup.express_forms.delete', $token)) {
            $this->flash('error', t("Invalid form token. Please try again."));

            return Redirect::to('/dashboard/gdpr/cleanup/express_forms');
        }

        /** @var ObjectManager $objectManager */
        $objectManager = $this->app->make(ObjectManager::class);

        try {
            /** @var \Concrete\Core\Tree\Node\Node $child */
            $node = ExpressEntryCategory::getByID($nodeId);

            if (!$node || !$node instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
                $this->flash('error', t("This Express Entity doesn't exist (anymore)."));

                return Redirect::to('/dashboard/gdpr/cleanup/express_forms');
            }

            $entryList = new EntryList($this->getEntity($node));

            foreach ($entryList->getResults() as $entry) {
                $objectManager->deleteEntry($entry);
            }
        } catch (Exception $e) {
            \Log::addError($e->getMessage());


            $this->flash('error', t("Something went wrong. Please check the Logs."));

            return Redirect::to('/dashboard/gdpr/cleanup/express_forms');
        }

        $this->flash('success', t("All form submissions have been removed."));

        return Redirect::to('/dashboard/gdpr/cleanup/express_forms');
    }

    /**
     * Get a list of form names and the number of submissions (entries)
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function getFormInformation()
    {
        /** @var \Concrete\Core\Tree\Node\Node $node */
        $node = ExpressEntryCategory::getNodeByName(\Concrete\Block\ExpressForm\Controller::FORM_RESULTS_CATEGORY_NAME);
        if (!$node) {
            return [];
        }

        // Once we have the category 'Form Results', let's grab all the entries
        $node->populateDirectChildrenOnly();

        $forms = [];

        foreach ($node->getChildNodes() as $child) {
            /** @var \Concrete\Core\Tree\Node\Node $child */
            if (!$child instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
                continue;
            }

            $entryList = new EntryList($this->getEntity($child));

            $forms[] = [
                'id' => $child->getTreeNodeID(),
                'name' => $child->getTreeNodeName(),
                'entries' => (int) $entryList->getTotalResults(),
            ];
        }

        // Forms with many submissions should be shown first
        usort($forms, function($a, $b) {
            return $a['entries'] < $b['entries'];
        });

        return $forms;
    }

    /**
     * @param \Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent
     *
     * @return \Concrete\Core\Entity\Express\Entity
     * @throws \Doctrine\ORM\ORMException
     */
    private function getEntity(\Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByResultsNode($parent);
    }
}
