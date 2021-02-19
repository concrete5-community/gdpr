<?php

namespace A3020\Gdpr\Cookie;

use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;

class Configuration
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Liaison
     */
    private $configuration;

    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->configuration = new Liaison($config, 'gdpr_cookies');

        $this->initiate();
    }

    public function getConfiguration()
    {
        return $this->configuration->get('configuration');
    }

    private function initiate()
    {
        $position = $this->config->get('gdpr.cookies.consent.position', 'bottom');
        if ($position !== 'bottom') {
            $this->configuration->set('configuration.position', $position);
        }

        $type = $this->config->get('gdpr.cookies.consent.type', 'notice');
        if ($type !== 'notice') {
            $this->configuration->set('configuration.type', $type);
        }

        $theme = $this->config->get('gdpr.cookies.consent.theme', 'block');
        if ($theme !== 'block') {
            $this->configuration->set('configuration.theme', $theme);
        }

        $bannerBackgroundColor = $this->config->get('gdpr.cookies.consent.banner_background_color', '#000');
        $bannerTextColor = $this->config->get('gdpr.cookies.consent.banner_text_color', '#fff');
        $buttonBackgroundColor = $this->config->get('gdpr.cookies.consent.button_background_color', '#f1d600');
        $buttonTextColor = $this->config->get('gdpr.cookies.consent.button_text_color', '#000');

        $this->configuration->set('configuration.palette.popup.background', $bannerBackgroundColor);
        $this->configuration->set('configuration.palette.popup.text', $bannerTextColor);
        $this->configuration->set('configuration.palette.button.background', $buttonBackgroundColor);
        $this->configuration->set('configuration.palette.button.text', $buttonTextColor);

        $readMorePage = $this->config->get('gdpr.cookies.consent.read_more_page');
        if ($readMorePage) {
            $page = Page::getByID($readMorePage);
            if ($page && !$page->isError()) {
                $this->configuration->set('configuration.content.href', $page->getCollectionLink());
            }
        }

        $message = $this->config->get('gdpr.cookies.consent.message');
        $message = !empty($message) ? $message : 'This website uses cookies to ensure you get the best experience on our website.';

        $dismiss = $this->config->get('gdpr.cookies.consent.dismiss_button_text');
        $dismiss = !empty($dismiss) ? $dismiss : 'Got it!';

        $allow = $this->config->get('gdpr.cookies.consent.allow_button_text');
        $allow = !empty($allow) ? $allow : 'Allow cookies';

        $deny = $this->config->get('gdpr.cookies.consent.deny_button_text');
        $deny = !empty($deny) ? $deny : 'Decline';

        $readMoreLinkText = $this->config->get('gdpr.cookies.consent.policy_link_text');
        $readMoreLinkText = !empty($readMoreLinkText) ? $readMoreLinkText : 'Learn more';

        $this->configuration->set('configuration.content.message', tc('CookieBar', $message));
        $this->configuration->set('configuration.content.dismiss', tc('CookieBar', $dismiss));
        $this->configuration->set('configuration.content.allow', tc('CookieBar', $allow));
        $this->configuration->set('configuration.content.deny', tc('CookieBar', $deny));
        $this->configuration->set('configuration.content.link', tc('CookieBar', $readMoreLinkText));

        $this->configuration->set('configuration.compliance', $this->getCompliance());
    }

    private function getCompliance()
    {
        return [
            'info' => '<div class="cc-compliance">{{dismiss}}</div>',
            'opt-in' => '<div class="cc-compliance cc-highlight">{{deny}}{{allow}}</div>',
            'opt-out' => '<div class="cc-compliance cc-highlight">{{deny}}{{dismiss}}</div>',
        ];
    }
}
