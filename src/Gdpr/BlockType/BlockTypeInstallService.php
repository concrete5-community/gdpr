<?php

namespace A3020\Gdpr\BlockType;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Package as PackageFacade;

class BlockTypeInstallService
{
    const PACKAGE_HANDLE = 'gdpr';

    /**
     * @param string $handle
     *
     * @return bool
     */
    public function isInstalled($handle)
    {
        return (bool) BlockType::getByHandle($handle);
    }

    /**
     * @param string $handle
     * @param bool $enable
     *
     * @return bool
     */
    public function installOrDeinstall($handle, $enable)
    {
        if ($enable) {
            return $this->install($handle);
        }

        return $this->deinstall($handle);
    }

    /**
     * @param string $handle
     *
     * @return bool
     */
    protected function install($handle)
    {
        if ($this->isInstalled($handle)) {
            return true;
        }

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle(self::PACKAGE_HANDLE);

        BlockType::installBlockType($handle, $pkg);

        return true;
    }

    /**
     * @param string $handle
     *
     * @return bool
     */
    protected function deinstall($handle)
    {
        /** @var \Concrete\Core\Entity\Block\BlockType\BlockType $bt */
        $bt = BlockType::getByHandle($handle);
        if ($bt) {
            $bt->delete();
        }

        return true;
    }
}
