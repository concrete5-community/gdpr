<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();

/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = $app->make('helper/date');
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a class="btn btn-primary toggle-settings" data-caption-toggled="<?php echo empty($logs) ? t('Show search settings') : t('Hide search settings') ?>">
        <?php echo empty($logs) ? t('Hide search settings') : t('Show search settings') ?>
    </a>
</div>

<div class="ccm-dashboard-content-inner">
    <div class="settings <?php echo empty($logs) ? '' : 'hide' ?>">
        <form method="post" action="<?php echo $this->action('save') ?>">
            <?php
            /** @var $token \Concrete\Core\Validation\CSRF\Token */
            echo $token->output('a3020.gdpr.cleanup.logs');
            ?>

            <div class="form-group">
                <label class="control-label" for="blockExpressFormSubmissions">
                    <?php
                    /** @var $blockExpressFormSubmissions bool */
                    echo $form->checkbox('blockExpressFormSubmissions', 1, $blockExpressFormSubmissions);
                    ?>

                    <?php echo t('Include Block Express Form Submissions'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t(/*i18n: %s is the name of a channel*/"The channel '%s' will be used to search for emails.", "LOG_TYPE_EMAILS") ?>"
                       for="sentEmails">
                    <?php
                    /** @var $sentEmails bool */
                    echo $form->checkbox('sentEmails', 1, $sentEmails);
                    ?>

                    <?php echo t("Include sent emails"); ?>
                </label><br>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("In concrete5 versions before 8.4.0 logs where not automatically deleted when a user was deleted.") ?>"
                       for="deletedUsers">
                    <?php
                    /** @var $deletedUsers bool */
                    echo $form->checkbox('deletedUsers', 1, $deletedUsers);
                    ?>

                    <?php echo t("Include log entries from deleted users"); ?>
                </label><br>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("This could return some false positives in the results.") ?>"
                       for="atSymbol">
                    <?php
                    /** @var $atSymbol bool */
                    echo $form->checkbox('atSymbol', 1, $atSymbol);
                    ?>

                    <?php echo t('Include log entries that contain an @-symbol'); ?>
                </label><br>
            </div>

            <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
        </form>
    </div>

    <?php
    /** @var \Concrete\Core\Logging\LogEntry[] $logs */
    if (empty($logs)) {
        ?>
        <p>
            <?php
            echo t("No logs have been found that match the search criteria.");
            ?>
        </p>
        <?php
    } else {
        ?>
        <form method="post" action="<?php echo $this->action('bulk'); ?>">
            <?php
            echo $token->output('gdpr.cleanup.logs.bulk');

            echo $form->label('action', t('Bulk action'));
            echo $form->select('action', ['' => t('-- Please select --'), 'delete' => t('Delete')]);
            ?>
            <hr>

            <?php
            echo '<p class="text-muted">'.t2('%s result found that match the search criteria', '%s results found that match the search criteria', count($logs));

            /** @var int $maxResults */
            if (count($logs) === $maxResults) {
                echo ' ('.t('result was limited').')';
            }

            echo '.</p>';
            ?>

            <table class="table logs-table">
                <thead>
                    <tr>
                        <th style="width: 20px;"><input type="checkbox" id="inp-check-all-logs"></th>
                        <th style="width: 200px;"><?php echo t('Created at'); ?></th>
                        <th style="width: 180px;"><?php echo t('Username'); ?></th>
                        <th><?php echo tc('Message of the log', 'Log message'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($logs as $log) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo $form->checkbox('logs[]', $log->getID());
                                ?>
                            </td>
                            <td>
                                <?php
                                echo e($log->getDisplayTimestamp());
                                ?>
                            </td>
                            <td>
                                <?php
                                $u  = $log->getUserObject();
                                echo $u ? $u->getUserName() : t('Deleted User');
                                ?>
                            </td>
                            <td>
                                <?php
                                echo nl2br(e($log->getMessage()));
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <?php
    }
    ?>
</div>

<script>
    $(document).ready(function() {
        $('.logs-table tbody tr').click(function(e) {
            if (e.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        if ($('#action').change(function() {
            if ($(this).val() === 'delete') {
                if (confirm('<?php echo t("Are you sure you want to delete these logs?") ?>')) {
                    $(this).closest('form').submit();
                }
            }

            $(this).val('');
        }));

        $('#inp-check-all-logs').change(function() {
            var checkedStatus = this.checked;

            $('.logs-table tbody tr').find('td:first :checkbox').each(function() {
                $(this).prop('checked', checkedStatus);
            });
        });
    });
</script>
