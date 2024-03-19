<?php
defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
/** @var \A3020\Gdpr\Html\FontAwesomeIcon $iconHelper */
$iconHelper = $app->make(\A3020\Gdpr\Html\FontAwesomeIcon::class);
$removeBasedOnUserId = $removeBasedOnUserId ?? false;
$removeBasedOnEmailAddress = $removeBasedOnEmailAddress ?? false;
$removeBasedOnUsername = $removeBasedOnUsername ?? false;
$disableTrackingCode = $disableTrackingCode ?? false;
$trackingCodeFound = $trackingCodeFound ?? false;
$redirectWelcomePage = $redirectWelcomePage ?? false;
$disableMarketplaceIntegration = $disableMarketplaceIntegration ?? false;
$disableMarketplaceIntelligentSearch = $disableMarketplaceIntelligentSearch ?? false;
$disableExternalIntelligentSearchHelp = $disableExternalIntelligentSearchHelp ?? false;
$disableExternalNews = $disableExternalNews ?? false;
$disableConcreteBackground = $disableConcreteBackground ?? false;
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.gdpr.settings');
        ?>

        <section class="settings-section">
            <header><?php echo t('User logs'); ?></header>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('This will automatically remove associated user logs when a user is deleted. This is done based on the %1$s column in the %2$s table.', 'uID', 'Logs'); ?>"
                       for="removeBasedOnUserId">
                    <?php
                    /** @var bool $removeBasedOnUserId */
                    echo $form->checkbox('removeBasedOnUserId', 1, $removeBasedOnUserId);
                    ?>
                    <?php echo t('Enable removing associated user logs when a user is deleted'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('Say the email address of the deleted user is %s, then all Logs that contain %s will be deleted.', 'john.doe@gmail.com', 'john.doe@gmail.com'); ?>"
                       for="removeBasedOnEmailAddress">
                    <?php
                    /** @var bool $removeBasedOnEmailAddress */
                    echo $form->checkbox('removeBasedOnEmailAddress', 1, $removeBasedOnEmailAddress);
                    ?>
                    <?php echo t('Enable removing user logs in which the email address is present'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('Say the username of the deleted user is %s, then all Logs that contain %s will be deleted. This option is probably not recommended if you allow users with a short username.', 'john.doe', 'john.doe'); ?>"
                       for="removeBasedOnUsername">
                    <?php
                    /** @var bool $removeBasedOnUsername */
                    echo $form->checkbox('removeBasedOnUsername', 1, $removeBasedOnUsername);
                    ?>
                    <?php echo t('Enable removing user logs in which the username is present'); ?>
                </label><br>

                <small class="help-block">
                    <?php
                    echo t("Make sure you know what you're doing when changing this option.");
                    ?>
                </small>
            </div>
        </section>

        <section class="settings-section">
            <header><?php echo t('Tracking'); ?></header>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("This will disable tracking code(s) defined on %s. If you uninstall the add-on or uncheck this option, the tracking code will become active again.",
                           t('System & Settings > SEO & Statistics > Tracking Codes')
                       ); ?>"
                       for="disableTrackingCode">
                    <?php
                    /** @var bool $disableTrackingCode */
                    echo $form->checkbox('disableTrackingCode', 1, $disableTrackingCode);
                    ?>
                    <?php echo t('Disable tracking codes'); ?>
                </label><br>

                <?php
                /** @var bool $trackingCodeFound */
                if ($trackingCodeFound) {
                    echo '<small class="help-block">'.t('There is a tracking code configured.').'</small>';
                } else {
                    echo '<small class="help-block">'.t('No tracking code is currently installed.').'</small>';
                }
                ?>
            </div>
        </section>

        <section class="settings-section">
            <header><?php echo t('Connections with concretecms.org'); ?></header>
            <small class="help-block">
                <?php echo t('It may be needed to logout / login in order to see the effect of the settings below.'); ?>
            </small>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("The welcome page aggregates various remote data. Not requesting it would improve privacy. By enabling this setting, the user is redirected to the '%s' page.", t('Waiting for Me')) ?>"
                       for="redirectWelcomePage">
                    <?php
                    /** @var bool $redirectWelcomePage */
                    echo $form->checkbox('redirectWelcomePage', 1, $redirectWelcomePage);
                    ?>
                    <?php echo t('Redirect Welcome page'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("When disabled, the site won't try to connect to the external marketplace and won't be able to '%s'.", t('Connect to the Community')) ?>"
                       for="disableMarketplaceIntegration">
                    <?php
                    /** @var bool $disableMarketplaceIntegration */
                    echo $form->checkbox('disableMarketplaceIntegration', 1, $disableMarketplaceIntegration);
                    ?>
                    <?php echo t('Disable marketplace integration'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("When disabled, themes and add-ons that match the search criteria won't be shown in intelligent search.") ?>"
                       for="disableMarketplaceIntelligentSearch">
                    <?php
                    /** @var bool $disableMarketplaceIntelligentSearch */
                    echo $form->checkbox('disableMarketplaceIntelligentSearch', 1, $disableMarketplaceIntelligentSearch);
                    ?>
                    <?php echo t('Disable marketplace intelligent search'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("When disabled, the documentation site won't be queried.") ?>"
                       for="disableExternalIntelligentSearchHelp">
                    <?php
                    /** @var bool $disableExternalIntelligentSearchHelp */
                    echo $form->checkbox('disableExternalIntelligentSearchHelp', 1, $disableExternalIntelligentSearchHelp);
                    ?>
                    <?php echo t('Disable external intelligent search help'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("When disabled, external news and updates won't be shown in the news flow.") ?>"
                       for="disableExternalNews">
                    <?php
                    /** @var bool $disableExternalNews */
                    echo $form->checkbox('disableExternalNews', 1, $disableExternalNews);
                    ?>
                    <?php echo t('Disable external news'); ?>
                </label>
                <small class="help-block"><?php
                    echo t('Note: there is %sa bug%s in Concrete CMS that ignores this setting.',
                        '<a target="_blank" href="https://github.com/concretecms/concretecms/issues/6933">',
                        ' ' . $iconHelper->externalLink() . '</a>'
                    ); ?>
                </small>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("Concrete CMS might pull background images from an external server for the login page. This could leak the visitor's IP address.") ?>"
                       for="disableConcreteBackground">
                    <?php
                    /** @var bool $disableConcreteBackground */
                    echo $form->checkbox('disableConcreteBackground', 1, $disableConcreteBackground);
                    ?>
                    <?php echo t('Disable Concrete CMS background on login page'); ?>
                </label>
            </div>
        </section>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary float-end" type="submit"><?php echo t('Save') ?></button>
            </div>
        </div>
    </form>
</div>

