<?php

namespace A3020\Gdpr\Form\Command;

use A3020\Gdpr\Form\Express\DeleteFormEntries;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Log;
use DateInterval;
use DateTime;
use Throwable;

class DeleteExpressFormEntriesCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    public function __invoke(DeleteExpressFormEntriesCommand $command)
    {
        $app = Application::getFacadeApplication();

        try {
            /** @var \A3020\Gdpr\Form\Express\DeleteFormEntries $helper */
            $helper = $app->make(DeleteFormEntries::class);
            $deletedSubmissions = $helper->delete($this->getOptions());
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            $this->output->write(t('Something went wrong. Please check the Logs.'));
            return false;
        }

        $this->output->write(t2(
            '%d form submission has been deleted.',
            '%d form submissions have been deleted.',
            $deletedSubmissions
        ));
    }

    private function getOptions()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make(Repository::class);

        $keepDays = (int) $config->get('gdpr.settings.express_forms.keep_days', 0);

        $now = new DateTime();

        return [
            'created_before' => $now->sub(new DateInterval('P'.$keepDays.'D')),
            'delete_files' => (bool) $config->get('gdpr.settings.express_forms.delete_files', false),
        ];
    }
}