<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Form\Express\DeleteFormEntries;
use A3020\Gdpr\Form\Express\ExpressFormHelper;
use A3020\Gdpr\Installer\TaskInstaller;
use A3020\Gdpr\Job\JobInstallService;
use A3020\Gdpr\Traits\PackageTrait;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Support\Facade\Log;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Exception;

final class ExpressForms extends DashboardController
{
    use PackageTrait;

    /** @var JobInstallService */
    protected $jobInstallService;

    /** @var TaskInstaller */
    protected $taskInstaller;

    public function on_start()
    {
        parent::on_start();

        $this->jobInstallService = $this->app->make(JobInstallService::class);
        $this->taskInstaller = $this->app->make(TaskInstaller::class, ['package' => $this->getPackage()]);
    }

    public function view()
    {
        $formInformation = false;

        try {
            // Express is likely to fail, so we'll catch and log errors
            $formInformation = $this->getFormInformation();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $this->set('formInformation', $formInformation);
        if ($this->isVersion9()) {
            $this->set('enableJobToRemoveFormSubmissions', $this->taskInstaller->isInstalled('gdpr_remove_form_submissions'));
        } else {
            $this->set('enableJobToRemoveFormSubmissions', $this->jobInstallService->isInstalled('gdpr_remove_form_submissions'));
        }
        $this->set('deleteAssociatedFiles', $this->config->get('gdpr.settings.express_forms.delete_files', false));
        $this->set('expressFormsKeepDays', $this->config->get('gdpr.settings.express_forms.keep_days'));
        $this->set('isVersion9', $this->isVersion9());
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.cleanup.express_forms.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/express_forms');
        }

        if ($this->isVersion9()) {
            if ($this->post('enableJobToRemoveFormSubmissions')) {
                $this->taskInstaller->install('gdpr_remove_form_submissions');
            } else {
                $this->taskInstaller->uninstall('gdpr_remove_form_submissions');
            }
        } else {
            $this->jobInstallService->installOrDeinstall('gdpr_remove_form_submissions', $this->post('enableJobToRemoveFormSubmissions'));
        }

        $keepDays = $this->post('expressFormsKeepDays');
        $this->config->save('gdpr.settings.express_forms.keep_days', $keepDays !== '' ? (int) $keepDays : null);
        $this->config->save('gdpr.settings.express_forms.delete_files', (bool) $this->post('deleteAssociatedFiles'));

        $this->flash('success', t('Your settings have been saved.'));

        return $this->action('/dashboard/gdpr/cleanup/express_forms');
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
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/express_forms');
        }

        try {
            /** @var \Concrete\Core\Tree\Node\Node $child */
            $node = ExpressEntryCategory::getByID($nodeId);

            if (!$node || !$node instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
                $this->flash('error', t("This Express Entity doesn't exist (anymore)."));

                return $this->action('/dashboard/gdpr/cleanup/express_forms');
            }

            /** @var DeleteFormEntries $deleteFormEntries */
            $deleteFormEntries = $this->app->make(DeleteFormEntries::class);
            $deleteFormEntries->deleteByNode($node, $this->getOptions());
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $this->flash('error', t("Something went wrong. Please check the Logs."));

            return $this->action('/dashboard/gdpr/cleanup/express_forms');
        }

        $this->flash('success', t("All form submissions have been removed."));

        return $this->action('/dashboard/gdpr/cleanup/express_forms');
    }

    /**
     * Get a list of form names and the number of submissions (entries)
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function getFormInformation()
    {
        /** @var ExpressFormHelper $helper */
        $helper = $this->app->make(ExpressFormHelper::class);

        $forms = [];
        foreach ($helper->getFormResultNodes() as $child) {
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

    private function getOptions()
    {
        return [
            'delete_files' => (bool) $this->config->get('gdpr.settings.express_forms.delete_files', false),
        ];
    }
}
