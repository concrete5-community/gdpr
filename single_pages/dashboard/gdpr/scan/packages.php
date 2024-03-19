<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
$app = Application::getFacadeApplication();
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a class="btn btn-primary toggle-settings" data-caption-toggled="<?php echo t('Hide settings') ?>"><?php echo t('Show settings'); ?></a>
</div>

<div class="ccm-dashboard-content-inner page-scan-packages">
    <div class="settings hide">
        <form method="post" action="<?php echo $this->action('save') ?>">
            <?php
            /** @var $token \Concrete\Core\Validation\CSRF\Token */
            echo $token->output('a3020.gdpr.scan.packages');
            ?>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("Here you can specify custom add-ons to scan for. Use one package handle per line."); ?>"
                       for="customPackages">
                    <?php echo t('Custom add-ons'); ?>
                </label>

                <?php
                /** @var string $customPackages */
                echo $form->textarea('customPackages', $customPackages, [
                    'placeholder' => t('Leave blank to only use the default add-ons'),
                    'style' => 'min-height: 100px; max-width: 400px',
                ]);
                ?>
            </div>

            <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
        </form>
    </div>

    <table class="table table-striped table-bordered" id="tbl-packages">
        <thead>
            <tr>
                <th style="width: 50px;"><?php echo t('Icon') ?></th>
                <th><?php echo t('Name') ?></th>
                <th><?php echo t('Description') ?></th>
                <th><?php echo t('Why') ?></th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#tbl-packages').DataTable({
        ajax: '<?php echo Url::to('/ccm/system/gdpr/scan/packages') ?>',
        lengthMenu: [[15, 40, 80, -1], [15, 40, 80, '<?php echo t('All') ?>']],
        columns: [
            {
                data: function(row, type, val) {
                    return '<img class="package-icon" style="width: 50px;" src="'+row.package_icon +'">';
                }
            },
            {
                data: "package_name"
            },
            {
                data: "package_description"
            },
            {
                data: "why"
            }
        ],
        order: [[ 1, "asc" ]],
        language: {
            emptyTable: '<?php echo t('No add-ons have been found that meet the current search criteria.') ?>'
        }
    });
});
</script>
