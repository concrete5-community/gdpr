<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();

/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = $app->make('helper/date');

$app->make('help')->display(
    t("Orphaned files don't have an author (anymore). The original author / uploader probably has been removed. " .
        "However, by default files remain in the file manager when a user is deleted."
    )
    . ' <br><br>' .
    t("The reason this is GDPR related is because files could contain sensitive information. " .
        "On this page you can reassign or delete the orphaned files."
    )
);
?>

<div class="ccm-dashboard-content-inner">
    <?php
    if (empty($orphanedFiles)) {
        ?>
        <p>
            <?php
            echo t("No orphaned files have been found.");
            ?>
        </p>
        <?php
    } else {
        ?>
        <form method="post" action="<?php echo $this->action('bulk'); ?>">
            <?php
            echo $token->output('gdpr.cleanup.orphaned_files.bulk');

            echo $form->label('action', t('Bulk action'));
            echo $form->select('action', [
                '' => t('-- Please select --'),
                'delete' => t('Delete'),
                'reassign' => t('Assign to super user'),
            ]);
            ?>
            <hr>

            <?php
            /** @var int $maxResults */
            if (count($orphanedFiles) === $maxResults) {
                echo '<p class="alert alert-warning">'.t("Only the first %s files are shown.", $maxResults).'</p>';
            }
            ?>

            <table class="table orphaned-files-table">
                <thead>
                    <tr>
                        <th style="width: 20px;">&nbsp;</th>
                        <th><?php echo t('Name'); ?></th>
                        <th><?php echo t('Type'); ?></th>
                        <th><?php echo t('Size'); ?></th>
                        <th><?php echo t('Modified at'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($orphanedFiles as $file) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo $form->checkbox('files[]', $file['id']);
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo $file['url'] ?>" target="_blank">
                                    <?php echo e($file['name']); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo $file['type'] ?>
                            </td>
                            <td>
                                <?php echo $file['size'] ?>
                            </td>
                            <td>
                                <?php echo $dh->formatDateTime($file['modified_at']) ?>
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
    $('.orphaned-files-table tbody tr').click(function(e) {
        if (e.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    if ($('#action').change(function() {
        if ($(this).val() === 'delete') {
            if (confirm('<?php echo t("Are you sure you want to delete these files? Please note that orphaned files may still be in use.") ?>')) {
                $(this).closest('form').submit();
            }
        } else {
            if (confirm('<?php echo t("Are you sure you want to reassign these files?") ?>')) {
                $(this).closest('form').submit();
            }
        }

        $(this).val('');
    }));
});
</script>