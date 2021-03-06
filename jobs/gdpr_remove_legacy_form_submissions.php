<?php

namespace Concrete\Package\Gdpr\Job;

use A3020\Gdpr\Form\Legacy\DeleteFormEntries;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Log;
use DateTime;
use Throwable;

final class GdprRemoveLegacyFormSubmissions extends Job
{
    public function getJobName()
    {
        return t('GDPR - Remove legacy form submissions');
    }

    public function getJobDescription()
    {
        return t('Automatically remove legacy form submissions stored in Form Results. Important: there is no way to restore form submissions once they are removed!');
    }

    public function run()
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var \A3020\Gdpr\Form\Legacy\DeleteFormEntries $helper */
            $helper = $app->make(DeleteFormEntries::class);
            $deletedSubmissions = $helper->delete($this->getOptions());
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return t('Something went wrong. Please check the Logs.');
        }

        return t2(
            '%d form submission has been deleted.',
            '%d form submissions have been deleted.',
            $deletedSubmissions
        );
    }

    private function getOptions()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make(Repository::class);

        $keepDays = (int) $config->get('gdpr.settings.legacy_forms.keep_days', 0);

        if (!$keepDays) {
            return [];
        }

        $now = new DateTime();

        return [
            'created_before' => $now->sub(new \DateInterval('P'.$keepDays.'D')),
        ];
    }
}
