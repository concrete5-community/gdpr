<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

/** @var array $searchFor */

$app->make('help')->display(
    t("This page shows a list of database tables that contain columns that might contain user data.") .'<br><br>'.
    t("If you are using custom code / packages, you may have to deal with personal data that is stored in those tables.").'<br><br>'.

    t('To find table columns the following search terms are used: %s',
        implode(', ', $searchFor)
    ).'<br><br>'.

    t('The search columns can be extended via the settings on this page.')
);
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a class="btn btn-primary toggle-settings" data-caption-toggled="<?php echo t('Hide settings') ?>"><?php echo t('Show settings'); ?></a>
</div>

<div class="ccm-dashboard-content-inner page-scan-tables">
    <div class="settings hide">
        <form method="post" action="<?php echo $this->action('save') ?>">
            <?php
            /** @var $token \Concrete\Core\Validation\CSRF\Token */
            echo $token->output('a3020.gdpr.scan.tables');
            ?>

            <div class="form-group">
                <label class="control-label" for="ignoreEmptyTables">
                    <?php
                    /** @var $ignoreEmptyTables bool */
                    echo $form->checkbox('ignoreEmptyTables', 1, $ignoreEmptyTables);
                    ?>

                    <?php echo t('Ignore empty tables'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label" for="ignoreCoreTables">
                    <?php
                    /** @var $ignoreCoreTables bool */
                    echo $form->checkbox('ignoreCoreTables', 1, $ignoreCoreTables);
                    ?>

                    <?php echo t('Ignore core tables'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("You can specify custom column names here, e.g. %s. Tables that contain columns with that name, will be reported in the Table scan. Use one column name per line. The names are case insensitive and regexes may be used.", 'vat_nr'); ?>"
                       for="customTableColumns">
                    <?php echo t('Custom table columns'); ?>
                </label>

                <?php
                /** @var string $customTableColumns */
                echo $form->textarea('customTableColumns', $customTableColumns, [
                    'placeholder' => t('Leave blank to only use the default column names'),
                    'style' => 'min-height: 50px; max-width: 400px',
                ]);
                ?>
            </div>

            <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
        </form>
    </div>

    <table class="table table-striped table-bordered" id="tbl-tables">
        <thead>
            <tr>
                <th><?php echo t('Table') ?></th>
                <th>
                    <?php
                    echo t('Columns');
                    ?>

                    <i class="text-muted launch-tooltip fa fa-question-circle"
                       title="<?php echo t("These columns may contain personal information. If a user is deleted, you may have to remove or anonymize this data."); ?>">
                    </i>
                </th>
                <th>
                    <?php
                    echo t('Core');
                    ?>

                    <i class="text-muted launch-tooltip fa fa-question-circle"
                       title="<?php echo t("Whether this is part of the concrete5 core."); ?>">
                    </i>
                </th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    var DataTable = $('#tbl-tables');
    DataTable.DataTable({
        ajax: '<?php echo Url::to('/ccm/system/gdpr/scan/tables') ?>',
        lengthMenu: [[15, 40, 80, -1], [15, 40, 80, '<?php echo t('All') ?>']],
        columns: [
            {
                data: function(row, type, val) {
                    var html = '';
                    html += '<a href="#" title="<?php echo t('Open table preview') ?>" data-dialog="table-preview" data-table-name="'+row.table_name+'">';
                    html += row.table_name + ' ('+row.table_row_total+')';
                    html += '</a>';

                    return html;
                }
            },
            {
                data: function(row, type, val) {
                    return row.columns.join(', ');
                }
            },
            {
                data: function(row, type, val) {
                    return row.is_core_table === true ? '<?php echo t('Yes') ?>' : '<?php echo t('No') ?>';
                }
            }
        ],
        order: [[ 2, "asc" ]]
    });

    DataTable.on('click', '[data-dialog]', function() {
        var tableName = $(this).data('table-name');

        jQuery.fn.dialog.open({
            href: '<?php echo Url::to('/ccm/system/gdpr/scan/table') ?>/'+tableName,
            modal: true,
            width: 960,
            height: 600,
            title: '<?php echo t('Table preview:') ?> '+ tableName
        });
    });
});
</script>
