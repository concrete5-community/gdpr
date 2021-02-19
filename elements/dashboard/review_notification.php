<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$pkgName = t('GDPR');
$pkgHandle = 'gdpr';

$app = Application::getFacadeApplication();

if (!$app->make(\A3020\Gdpr\Foundation\Review::class)->shouldShowNotification()) {
    return;
}
?>

<div class="panel panel-default alert alert-dismissable">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <p>
        <?php
        echo t("Do you like the %s add-on and would you like to post a review? We will grant you a <strong>free license</strong> as a thank-you!",
            $pkgName
        );
        ?>
    </p><br>

    <a class="btn btn-success btn-post-review" href="https://www.concrete5.org/marketplace/addons/<?php echo $pkgHandle ?>/reviews" target="_blank">
        <i class="fa fa-check" style="color: white;"></i>
        <?php echo t('Yes, add a review'); ?>
    </a>

    <button class="btn btn-default btn-dismiss-review" data-dismiss="alert">
        <?php
        echo t("Don't show anymore");
        ?>
    </button>
</div>

<script>
$(document).ready(function() {
    $('.btn-post-review').click(function(e) {
        e.preventDefault();

        var tab = window.open($(this).attr('href'), '_blank');
        if (tab) {
            tab.focus();
            $('.btn-dismiss-review').click();
        }
    });

    $('.btn-dismiss-review').click(function(e) {
        $.post('/ccm/system/<?php echo $pkgHandle ?>/foundation/dismiss_review');
    });
});
</script>
