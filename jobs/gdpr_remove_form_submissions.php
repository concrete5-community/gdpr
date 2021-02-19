<?php

namespace Concrete\Package\Gdpr\Job;

use A3020\Gdpr\Express\Delete\DeleteFormEntries;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Application;
use DateTime;
use Throwable;

final class GdprRemoveFormSubmissions extends Job
{
    public function getJobName()
    {
        return t('GDPR - Remove form submissions');
    }

    public function getJobDescription()
    {
        return t('Automatically remove all form submissions stored in Form Results. Important: there is no way to restore form submissions once they are removed!');
    }

    public function run()
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var DeleteFormEntries $helper */
            $helper = $app->make(DeleteFormEntries::class);
            $deletedSubmissions = $helper->delete($this->getOptions());
        } catch (Throwable $e) {
            \Log::addError($e->getMessage());

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

        $keepDays = (int) $config->get('gdpr.settings.express_forms.keep_days', 0);

        $now = new DateTime();
        return [
            'created_before' => $now->sub(new \DateInterval('P'.$keepDays.'D')),
        ];
    }
}
