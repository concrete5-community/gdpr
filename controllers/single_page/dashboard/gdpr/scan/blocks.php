<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Scan;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Form\Helper;

final class Blocks extends DashboardController
{
    /** @see \A3020\Gdpr\Ajax\Scan\Blocks */

    public function view()
    {
        $this->set('ignoreFixedBlocks', (bool) $this->config->get('gdpr.scan.block_types.ignore_fixed_blocks', false));
        $this->set('ignoreCoreBlocks', (bool) $this->config->get('gdpr.scan.block_types.ignore_core_blocks', false));
        $this->set('customBlockTypes', implode("\n", $this->config->get('gdpr.scan.block_types.custom', [])));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.scan.blocks')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/scan/blocks');
        }

        /** @var Helper $helper */
        $helper = $this->app->make(Helper::class);

        $this->config->save('gdpr.scan.block_types.ignore_fixed_blocks', (bool) $this->post('ignoreFixedBlocks'));
        $this->config->save('gdpr.scan.block_types.ignore_core_blocks', (bool ) $this->post('ignoreCoreBlocks'));
        $this->config->save('gdpr.scan.block_types.custom', $helper->convertTextArea($this->post('customBlockTypes')));

        return $this->action('/dashboard/gdpr/scan/blocks');
    }
}
