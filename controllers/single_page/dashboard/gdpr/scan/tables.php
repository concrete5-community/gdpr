<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Scan;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Form\Helper;
use Concrete\Core\Routing\Redirect;

final class Tables extends DashboardController
{
    /** @see \A3020\Gdpr\Ajax\Scan\Tables */

    public function view()
    {
        $this->set('ignoreEmptyTables', (bool) $this->config->get('gdpr.scan.tables.ignore_empty_tables', true));
        $this->set('ignoreCoreTables', (bool) $this->config->get('gdpr.scan.tables.ignore_core_tables', false));
        $this->set('customTableColumns', implode("\n", $this->config->get('gdpr.scan.tables.custom', [])));
        $this->set('searchFor', $this->config->get('gdpr::database_columns.default'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.scan.tables')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/gdpr/scan/tables');
        }

        /** @var Helper $helper */
        $helper = $this->app->make(Helper::class);

        $this->config->save('gdpr.scan.tables.ignore_empty_tables', (bool) $this->post('ignoreEmptyTables'));
        $this->config->save('gdpr.scan.tables.ignore_core_tables', (bool ) $this->post('ignoreCoreTables'));
        $this->config->save('gdpr.scan.tables.custom', $helper->convertTextArea($this->post('customTableColumns')));

        return Redirect::to('/dashboard/gdpr/scan/tables');
    }
}
