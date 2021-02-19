<?php

namespace A3020\Gdpr\Ajax\Scan;

use A3020\Gdpr\Controller\AjaxController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Package;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Package as PackageFacade;

class Packages extends AjaxController
{
    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    /**
     * Return a list of packages that are known to be problematic with GDPR
     *
     * @return array
     */
    private function getRecords()
    {
        $records = [];

        foreach ($this->getPackages() as $handle => $why) {
            /** @see \Concrete\Core\Package\PackageService */
            $pkg = PackageFacade::getByHandle($handle);

            /** @var Package $pkg */
            if (!$pkg) {
                continue;
            }

            $records[] = [
                'package_icon' => $this->getPackageIcon($pkg),
                'package_name' => $pkg->getPackageName(),
                'package_description' => $pkg->getPackageDescription(),
                'why' =>  $why,
            ];
        }

        return $records;
    }

    private function getPackages()
    {
        $packages = [
            'mailchimp' => t('Because it sends data to %s.', 'MailChimp'),
            'd3_mailchimp' => t('Because it sends data to %s.', 'MailChimp'),
            'mail-service-integration' => t('Because it sends data to various mail services.'),
            'formify' => t('Because forms can contain personal information.'),
            'formidable-lite' => t('Because forms can contain personal information.'),
            'formidable-full-version' => t('Because forms can contain personal information.'),
            'form-plus1' => t('Because it sends data to %s.', 'MailChimp'),
            'wufoo-forms1' => t('Because it sends data to %s.', 'Wufoo'),
            'live-chat' => t('Because it sends data to %s.', 'Tawk.to'),
            'ecommerce-with-snipcart' => t('Because data is sent to %s.', 'snipcart.com'),
            'like-this' => t('Because it uses the Facebook plugin to share and like.'),
            'share-me' => t('Because it uses social media plugins to share and like.'),
            'community_store' => t('Because it stores orders and customer data.'),
            'bitter-shop-system' => t('Because it stores orders and customer data.'),
            'contact-form-no-links1' => t('Because it sends the email to an email address.'),
            'centry' => t('Because it might send logs to another server. (configurable)'),
            'image-optimizer' => t('Because it might send images to %s. (configurable)', 'TinyPNG'),
            'storage-for-amazon-s3' => t('Because it might send files to %s.', 'Amazon'),
            'likes-this-block' => t('Because it stores the user id.'),
            'chatwee-chat' => t('Because it sends data to %s.', 'Chatwee'),
            'zopim-chat-3rd-party' => t('Because it sends data to %s.', 'Zopim'),
            'slife' => t('Because it sends data to %s.', 'Slack'),
            'mautic' => t('Because it sends data to %s.', 'Mautic'),
        ];

        $config = $this->app->make(Repository::class);

        foreach ($config->get('gdpr.scan.packages.custom', []) as $handle) {
            $packages[$handle] = '-';
        }

        return $packages;
    }


    /**
     * @param $pkg
     *
     * @return string
     */
    private function getPackageIcon($pkg)
    {
        /** @var \Concrete\Core\Application\Service\Urls $service */
        $service = $this->app->make('helper/concrete/urls');

        return $service->getPackageIconURL($pkg);
    }
}
