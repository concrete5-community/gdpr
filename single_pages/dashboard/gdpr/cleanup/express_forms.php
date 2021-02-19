<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

$app->make('help')->display(
    t("Form submissions often contain personal data. Most likely you've been sending the submissions to an email address. However, the data might still be in concrete5.")
    . ' ' .
    t("Because not all concrete5 versions support bulk deletion of Express Form Results, you can do that here.")
);
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.gdpr.cleanup.express_forms.settings');
        ?>

        <section class="settings-section">
            <div class="form-group alert alert-warning">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("In case your form has an upload field, you may want to delete the associated files as well. Please note that these files might be used elsewhere in the system.") ?>"
                       for="deleteAssociatedFiles">
                    <?php
                    /** @var bool $deleteAssociatedFiles */
                    echo $form->checkbox('deleteAssociatedFiles', 1, $deleteAssociatedFiles);
                    ?>
                    <?php echo t('When deleting form submissions, also delete associated files'); ?>
                </label>
            </div>

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

            <div class="form-group alert alert-success <?php echo $enableJobToRemoveFormSubmissions ? '' : 'hide' ?> express-form-toggle">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('You can control how long the submissions may be stored before they are deleted.') ?>"
                       for="expressFormsKeepDays">
                    <?php echo t('Keep form submissions for x-number of days'); ?>
                </label>

                <?php
                /** @var int $expressFormsKeepDays */
                echo $form->number('expressFormsKeepDays', $expressFormsKeepDays, [
                    'placeholder' => 0,
                ]);
                ?>
            </div>
        </section>

        <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
    </form>
    <hr>

    <?php
    /** @var bool|array $formInformation */
    if (is_array($formInformation) && !empty($formInformation)) {
        ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?php echo t('Name') ?></th>
                    <th><?php echo t('Stored submissions') ?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($formInformation as $form) {
                    ?>
                    <tr>
                        <td>
                            <a title="<?php echo t('View Entries') ?>" href="<?php echo Url::to('/dashboard/reports/forms/view/'.$form['id']) ?>">
                                <?php
                                echo e($form['name']);
                                ?>
                            </a>
                        </td>
                        <td><?php echo $form['entries'] ?></td>
                        <td class="text-right">
                            <?php
                            if ($form['entries']) {
                                /** @var $token \Concrete\Core\Validation\CSRF\Token */
                                ?>
                                <a href="<?php echo $this->action('deleteEntries', $form['id'], $token->generate('gdpr.cleanup.express_forms.delete')) ?>"
                                   class="btn-delete-submissions btn btn-danger"><?php echo t('Delete all submissions'); ?></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    } elseif (is_array($formInformation) && empty($formInformation)) {
        echo t("No Express Forms have been found.");
    } else {
        echo t("Form entries couldn't be retrieved. See %sLogs%s for details.", '<a href="'.Url::to('/dashboard/reports/logs').'">', '</a>');
    }
    ?>
</div>

<script>
$(document).ready(function() {
    $('.btn-delete-submissions').click(function(e) {
        e.preventDefault();

        if (confirm('<?php echo t('Are you sure you want to remove these form submissions? There is no way to restore this data.') ?>')) {
            window.location.href = $(this).attr('href');
        }
    });

    function toggleFormSubmissions()
    {
        $('.express-form-toggle').toggleClass('hide',
            !$('#enableJobToRemoveFormSubmissions').is(':checked')
        );
    }

    toggleFormSubmissions();

    $('#enableJobToRemoveFormSubmissions').click(function() {
        toggleFormSubmissions();
    });
});
</script>
