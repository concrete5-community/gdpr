<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Package;
use Concrete\Core\Support\Facade\Url;

/** @var \Concrete\Core\Entity\Package $package */
$package = Package::getByHandle('gdpr');
?>
<p><?php echo t('Congratulations, the add-on has been installed!'); ?></p>
<br>

<p>
    <strong><?php echo t('You can find the add-on here:'); ?></strong><br>
    <a class="btn btn-default" href="<?php echo Url::to('/dashboard/gdpr') ?>">
        <?php
        echo t('Dashboard / GDPR');
        ?>
    </a>
</p>

<div style="padding: 20px; border: 1px solid #eee; margin-top: 45px;">
    <div style="
        width: 40px;
        height: 40px;
        float: right;
        background-image: url('<?php echo $package->getController()->getRelativePath() ?>/images/help-launcher-core.png');
    ">
    </div>

    <?php
    echo t('Tip: all pages use the internal concrete5 help system.');
    echo t("You can open the help by clicking on the help icon.");
    ?>
</div>
