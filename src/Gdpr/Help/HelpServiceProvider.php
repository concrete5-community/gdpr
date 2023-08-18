<?php

namespace A3020\Gdpr\Help;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;

class HelpServiceProvider extends Provider
{
    public function register()
    {
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/cleanup/express_forms',
            t("Form submissions often contain personal data. Most likely you've been sending the submissions to an email address. However, the data might still be in concrete5.")
            . ' ' .
            t("Because not all concrete5 versions support bulk deletion of Express Form Results, you can do that here.")
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/cleanup/legacy_forms',
            t("Form submissions often contain personal data. Most likely you've been sending the submissions to an email address. However, the data might still be in concrete5.")
            . ' ' .
            t("On this page you can install a job that automatically removes legacy form submissions.")
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/cleanup/logs',
            t('Logs could contain personal information. This page allows you to bulk remove log entries that match certain criteria.')
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/cleanup/orphaned_files',
            t("Orphaned files don't have an author (anymore). The original author / uploader probably has been removed. " .
                "However, by default files remain in the file manager when a user is deleted."
            )
            . ' <br><br>' .
            t("The reason this is GDPR related is because files could contain sensitive information. " .
                "On this page you can reassign or delete the orphaned files."
            )
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/cookies/consent',
            t("The cookie consent solution is as-is. It's technically very challenging to come up with a solution that is GDPR compliant, built within acceptable time, and easy to use.")
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/data_breach/notify_users',
            t("In case of a data breach, you may want to notify your users.")
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/data_transfer/requests',
            t('A user has the right to request his/her data. This page shows all data transfer requests.').'<br><br>'.
            t("Data transfer requests can be created via the %s event or by using the '%s' block that can be installed via the Settings page.",
                'on_gdpr_process_data_transfer_request',
                t('GDPR - Data Transfer Request')
            )
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/scan/blocks',
            t("This page shows a list of pages that contain blocks that might process user data. Please check the pages manually to verify they are GDPR compliant.") .'<br><br>'.
            t("A hand crafted list of block types is used for this, but you can also add your own block type handles in the settings.").'<br><br>'.
            t('Also the block folders are scanned for certain keywords. E.g. whether mail is sent, and whether form-tags are used in template files.')
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/scan/overall',
            t('This page will perform various checks on your current configuration.')
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/scan/packages',
            t("This page shows a list of add-ons that need to be manually reviewed as they might process user data.") . '<br><br>' .
            t('The GDPR comes with a list of add-ons that certainly process user data. You may extend this list via the settings on this page. '.
                'However, this is probably only interesting if you manage many websites and you are copying the configuration files.') . '<br><br>'.
            t('In the future this page might actually scan the packages directory for certain keywords.')
        );

        $searchFor = $this->getSearchFor();
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/scan/tables',
            t("This page shows a list of database tables that contain columns that might contain user data.") .'<br><br>'.
            t("If you are using custom code / add-ons, you may have to deal with personal data that is stored in those tables.").'<br><br>'.

            t('To find table columns the following search terms are used: %s',
                implode(', ', $searchFor)
            ).'<br><br>'.

            t('The search terms can be extended via the settings on this page.')
        );
        $this->app['help/dashboard']->registerMessageString(
            '/dashboard/gdpr/checklist',
            t("If a checkbox is checked, the value is stored. This page serves as a progress indicator. Additional checks can be added via a config file.").'<br><br>'.
            t("The checklist is probably not complete. Don't interpret a completely checked off list as being GDPR compliant.")
        );
    }

    /**
     * Get a list of column names to search for
     *
     * We use preg_match so regexes may be used.
     *
     * @return array
     */
    private function getSearchFor()
    {
        $config = $this->app->make(Repository::class);

        $columns = array_merge($config->get('gdpr::database_columns.default'), $config->get('gdpr.scan.tables.custom', []));
        $columns = array_map('strtolower', $columns);

        return array_unique($columns);
    }
}