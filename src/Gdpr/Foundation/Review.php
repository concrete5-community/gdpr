<?php

namespace A3020\Gdpr\Foundation;

/** @see \Concrete\Core\Package\PackageService */
use Concrete\Core\Entity\Package;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Concrete\Core\Config\Repository\Repository;
use DateInterval;
use DateTime;
use Throwable;

class Review
{
    const PACKAGE_HANDLE = 'gdpr';

    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function shouldShowNotification()
    {
        try {
            if ($this->isDismissed()) {
                return false;
            }

            if (!$this->isInstalledForDays()) {
                return false;
            }

            if (!$this->isConnected()) {
                return false;
            }
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * Return true if the review popup has been dismissed
     *
     * @return bool
     */
    private function isDismissed()
    {
        return (bool) $this->config->get('gdpr.foundation.review.is_dismissed', false);
    }

    /**
     * Return true if the package is installed for at least x days
     *
     * @param int $days
     *
     * @return bool
     */
    private function isInstalledForDays($days = 5)
    {
        /** @var Package $pkg */
        $pkg = PackageFacade::getByHandle(self::PACKAGE_HANDLE);
        $today = new DateTime();

        return $pkg->getPackageDateInstalled()->add(
            new DateInterval("P".$days."D")
        ) < $today;
    }

    /**
     * Return true if site is connected with the marketplace
     *
     * @return bool
     */
    private function isConnected()
    {
        /** @var \Concrete\Core\Marketplace\Marketplace $marketplace */
        $marketplace = Marketplace::getInstance();

        return $marketplace->isConnected();
    }
}
