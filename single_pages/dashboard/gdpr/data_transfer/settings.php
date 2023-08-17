<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.gdpr.data_transfer.settings');
        ?>

        <div class="form-group">
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

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("This will install a '%s' block. You can add it on the profile page, for example. The logged in user can click on the button which will then create a data transfer request. If you uncheck this option, the block will be removed and uninstalled.", t('GDPR - Data Transfer Request')) ?>"
                   for="enableInstallBlock">
                <?php
                /** @var bool $enableInstallBlock */
                echo $form->checkbox('enableInstallBlock', 1, $enableInstallBlock);
                ?>
                <?php echo t('Install a block that can create a data transfer request for the current user'); ?>
            </label>
        </div>

        <div class="form-group">
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

        <div class="form-group">
            <label class="control-label" for="dataTransferDownloadDaysValid">
                <?php echo t('Number of days the download should be valid'); ?>
                <?php
                /** @var int $dataTransferDownloadDaysValid */
                echo $form->number('dataTransferDownloadDaysValid', $dataTransferDownloadDaysValid);
                ?>
            </label>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary float-end" type="submit"><?php echo t('Save') ?></button>
            </div>
        </div>
    </form>
</div>
