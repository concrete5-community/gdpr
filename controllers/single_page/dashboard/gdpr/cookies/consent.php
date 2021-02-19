<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cookies;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Routing\Redirect;
use Exception;

final class Consent extends DashboardController
{
    public function view()
    {
        $this->set('config', $this->config);
        $this->set('complianceTypeOptions', [
            'opt-in' => t('Opt-in'),
            'opt-out' => t('Opt-out'),
            'notice' => t('Notice'),
        ]);
        $this->set('positionOptions', [
            'bottom' => t('Banner bottom'),
            'top' => t('Banner top'),
            'bottom-left' => t('Floating left'),
            'bottom-right' => t('Floating right'),
        ]);
        $this->set('themeOptions', [
            'block' => t('Block'),
            'classic' => t('Classic'),
            'edgeless' => t('Edgeless'),
        ]);

        $this->set('colorPicker', $this->app->make('helper/form/color'));
        $this->set('pageSelector', $this->app->make('helper/form/page_selector'));
    }

    public function save()
    {
        if (!$this->token->validate('gdpr.cookies.consent')) {
            throw new Exception($this->token->getErrorMessage());
        }

        $this->config->save('gdpr.cookies.consent.enabled', (bool) $this->post('enabled'));

        $this->config->save('gdpr.cookies.consent.type', $this->post('type'));
        $this->config->save('gdpr.cookies.consent.position', $this->post('position'));
        $this->config->save('gdpr.cookies.consent.theme', $this->post('theme'));

        $this->config->save('gdpr.cookies.consent.banner_background_color', $this->convertToHex($this->post('bannerBackgroundColor')));
        $this->config->save('gdpr.cookies.consent.banner_text_color', $this->convertToHex($this->post('bannerTextColor')));
        $this->config->save('gdpr.cookies.consent.button_background_color', $this->convertToHex($this->post('buttonBackgroundColor')));
        $this->config->save('gdpr.cookies.consent.button_text_color', $this->convertToHex($this->post('buttonTextColor')));

        $this->config->save('gdpr.cookies.consent.read_more_page', (int) $this->post('readMorePage'));

        $this->config->save('gdpr.cookies.consent.message', trim($this->post('message')));
        $this->config->save('gdpr.cookies.consent.dismiss_button_text', trim($this->post('dismissButtonText')));
        $this->config->save('gdpr.cookies.consent.deny_button_text', trim($this->post('denyButtonText')));
        $this->config->save('gdpr.cookies.consent.allow_button_text', trim($this->post('allowButtonText')));
        $this->config->save('gdpr.cookies.consent.policy_link_text', trim($this->post('policyLinkText')));

        $this->flash('success', t('Settings saved'));

        return Redirect::to('/dashboard/gdpr/cookies/consent');
    }

    /**
     * We need a hex value otherwise the mouseover in the dialog won't work well.
     *
     * @param string $rgb e.g. 'rgb(0,0,0)'
     *
     * @return string
     */
    private function convertToHex($rgb)
    {
        if (strpos($rgb, '#') !== false) {
            // Is already hex color
            return $rgb;
        }

        list($r, $g, $b) = array_map('trim', explode(',', str_replace(['rgb(', ')',], '', $rgb)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
