<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.gdpr.settings');
        ?>

        <section class="settings-section">
            <header><?php echo t('User logs'); ?></header>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('This will automatically remove associated user logs when a user is deleted. This is done based on the uID column in the Logs table.'); ?>"
                       for="removeBasedOnUserId">
                    <?php
                    /** @var bool $removeBasedOnUserId */
                    echo $form->checkbox('removeBasedOnUserId', 1, $removeBasedOnUserId);
                    ?>
                    <?php echo t('Enable removing associated user logs when a user is deleted'); ?>
                </label>
            </div>

            <div class="form-group alert alert-success">
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

            <div class="form-group alert alert-warning">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('Say the username of the deleted user is %s, then all Logs that contain %s will be deleted. This option is probably not recommended if you allow users with a short username.', 'john.doe', 'john.doe'); ?>"
                       for="removeBasedOnUsername">
                    <?php
                    /** @var bool $removeBasedOnUsername */
                    echo $form->checkbox('removeBasedOnUsername', 1, $removeBasedOnUsername);
                    ?>
                    <?php echo t('Enable removing user logs in which the username is present'); ?>
                </label><br>

                <small>
                    <?php
                    echo t("Make sure you know what you're doing when changing this option.");
                    ?>
                </small>
            </div>
        </section>

        <section class="settings-section">
            <header><?php echo t('Tracking'); ?></header>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("concrete5 might pull background images from an external server for the /login page. This could leak the visitor's IP address.") ?>"
                       for="disableConcreteBackground">
                    <?php
                    /** @var bool $disableConcreteBackground */
                    echo $form->checkbox('disableConcreteBackground', 1, $disableConcreteBackground);
                    ?>
                    <?php echo t('Disable concrete5 background on Login page'); ?>
                </label>
            </div>

            <div class="form-group alert alert-success">
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
                    echo '<small>'.t('There is a tracking code configured.').'</small>';
                } else {
                    echo '<small>'.t('No tracking code is currently installed.').'</small>';
                }
                ?>
            </div>
        </section>

        <section class="settings-section">
            <header><?php echo t('Express Forms'); ?></header>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("Form submissions are always stored. To remove form submissions automatically, you can run and schedule the job via Automated Tasks. If you uncheck this option, the job will be uninstalled.") ?>"
                       for="enableJobToRemoveFormSubmissions">
                    <?php
                    /** @var bool $enableJobToRemoveFormSubmissions */
                    echo $form->checkbox('enableJobToRemoveFormSubmissions', 1, $enableJobToRemoveFormSubmissions);
                    ?>
                    <?php echo t('Enable an Automated Job that could remove Express Form submissions'); ?>
                </label>
            </div>
        </section>

        <section class="settings-section">
            <header><?php echo t('Data Transfer'); ?></header>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("This will install an Automated Job. The job will build a ZIP file and sends the download link to the user.") ?>"
                       for="enableJobToProcessDataTransferRequests">
                    <?php
                    /** @var bool $enableJobToProcessDataTransferRequests */
                    echo $form->checkbox('enableJobToProcessDataTransferRequests', 1, $enableJobToProcessDataTransferRequests);
                    ?>
                    <?php echo t('Enable an Automated Job that can process Data Transfer Requests'); ?>
                </label>
            </div>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("Disable this option if you or other add-ons take care of what data should be included in the zip file.") ?>"
                       for="enableSubmittingDefaultData">
                    <?php
                    /** @var bool $enableSubmittingDefaultData */
                    echo $form->checkbox('enableSubmittingDefaultData', 1, $enableSubmittingDefaultData);
                    ?>
                    <?php echo t('Submit default data to .zip file'); ?>
                </label>
            </div>

            <div class="form-group alert alert-success">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("") ?>"
                       for="dataTransferDownloadDaysValid">
                    <?php echo t('Number of days the download should be valid'); ?>
                    <?php
                    /** @var int $dataTransferDownloadDaysValid */
                    echo $form->number('dataTransferDownloadDaysValid', $dataTransferDownloadDaysValid);
                    ?>
                </label>
            </div>
        </section>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit"><?php echo t('Save') ?></button>
            </div>
        </div>
    </form>
</div>
