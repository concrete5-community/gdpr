<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Tracking\Code;
use Concrete\Core\Routing\Redirect;

final class Settings extends DashboardController
{
    public function view()
    {
        // User logs
        $this->set('removeBasedOnUserId', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_user_id', true));
        $this->set('removeBasedOnUsername', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_username', false));
        $this->set('removeBasedOnEmailAddress', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_email_address', false));

        // Tracking
        $this->set('trackingCodeFound', $this->hasTrackingCode());
        $this->set('disableTrackingCode', (bool) $this->config->get('gdpr.settings.tracking.disabled', false));

        // concrete5.org
        $this->set('redirectWelcomePage', (bool) $this->config->get('gdpr.settings.redirect_welcome_page'));
        $this->set('disableMarketplaceIntegration', !(bool) $this->config->get('concrete.marketplace.enabled'));
        $this->set('disableMarketplaceIntelligentSearch', !(bool) $this->config->get('concrete.marketplace.intelligent_search'));
        $this->set('disableExternalIntelligentSearchHelp', !(bool) $this->config->get('concrete.external.intelligent_search_help'));
        $this->set('disableExternalNews', !(bool) $this->config->get('concrete.external.news'));
        $this->set('disableConcreteBackground', (bool) $this->config->get('concrete.white_label.background_url'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/gdpr/settings');
        }

        // User logs
        $this->config->save('gdpr.settings.logs.remove_based_on_user_id', (bool) $this->post('removeBasedOnUserId'));
        $this->config->save('gdpr.settings.logs.remove_based_on_username', (bool) $this->post('removeBasedOnUsername'));
        $this->config->save('gdpr.settings.logs.remove_based_on_email_address', (bool) $this->post('removeBasedOnEmailAddress'));

        // Tracking
        $this->config->save('gdpr.settings.tracking.disabled', (bool) $this->post('disableTrackingCode'));

        // concrete5.org
        $this->config->save('gdpr.settings.redirect_welcome_page', (bool) $this->post('redirectWelcomePage'));
        $this->config->save('concrete.marketplace.enabled', !(bool) $this->post('disableMarketplaceIntegration'));
        $this->config->save('concrete.marketplace.intelligent_search', !(bool) $this->post('disableMarketplaceIntelligentSearch'));
        $this->config->save('concrete.external.intelligent_search_help', !(bool) $this->post('disableExternalIntelligentSearchHelp'));
        $this->config->save('concrete.external.news', !(bool) $this->post('disableExternalNews'));

        if ((bool) $this->post('disableConcreteBackground')) {
            $this->config->save('concrete.white_label.background_url', 'none');
        } else {
            $this->config->save('concrete.white_label.background_url', false);
        }

        $this->flash('success', t('Your settings have been saved.'));

        return Redirect::to('/dashboard/gdpr/settings');
    }

    private function hasTrackingCode()
    {
        /** @var Code $code */
        $code = $this->app->make(Code::class);

        return $code->has();
    }
}
