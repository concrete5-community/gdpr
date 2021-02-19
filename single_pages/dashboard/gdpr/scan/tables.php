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
                <label class="control-label" for="ignoreFixedTables">
                    <?php
                    /** @var $ignoreFixedTables bool */
                    echo $form->checkbox('ignoreFixedTables', 1, $ignoreFixedTables);
                    ?>

                    <?php echo t("Ignore tables that are marked as compliant"); ?>
                </label>
            </div>

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
                    echo t('Status');
                    ?>

                    <i class="text-muted launch-tooltip fa fa-question-circle"
                       title="<?php echo t("Mark your own custom tables once they are GDPR compliant. Some default tables might already be checked, because the related user data is deleted when a user is deleted."); ?>">
                    </i>
                </th>
                <th>
                    <?php
                    echo t('Default table');
                    ?>

                    <i class="text-muted launch-tooltip fa fa-question-circle"
                       title="<?php echo t("A default table is present on a fresh installation."); ?>">
                    </i>
                </th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    var DataTableElement = $('#tbl-tables');
    var DataTable = DataTableElement.DataTable({
        ajax: '<?php echo Url::to('/ccm/system/gdpr/scan/tables') ?>',
        lengthMenu: [[15, 40, 80, -1], [15, 40, 80, '<?php echo t('All') ?>']],
        columns: [
            {
                data: function(row, type, val) {
                    var html = '';
                    html += '<a href="#" title="<?php echo t('Open table preview') ?>" data-dialog="preview" data-table-name="'+row.table_name+'">';
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
                    var html = '<a href="#"  data-dialog="status" data-table-name="'+row.table_name+'" class="btn btn-default">';

                    if (row.status.fixed) {
                        html += '<i class="fa fa-check">';
                    } else {
                        html += '<i class="fa fa-warning">';
                    }

                    html += '</i></a>';

                    return html;
                }
            },
            {
                data: function(row, type, val) {
                    return row.is_core_table === true ? '<?php echo t('Yes') ?>' : '<?php echo t('No') ?>';
                }
            }
        ],
        order: [[ 3, "asc" ]]
    });

    DataTableElement.on('click', '[data-dialog="preview"]', function() {
        var tableName = $(this).data('table-name');

        jQuery.fn.dialog.open({
            href: '<?php echo Url::to('/ccm/system/gdpr/scan/table/preview') ?>/'+tableName,
            modal: true,
            width: 960,
            height: 600,
            title: '<?php echo t('Table preview') ?>: '+ tableName
        });
    });

    DataTableElement.on('click', '[data-dialog="status"]', function() {
        var tableName = $(this).data('table-name');

        jQuery.fn.dialog.open({
            href: '<?php echo Url::to('/ccm/system/gdpr/scan/table/status') ?>/'+tableName,
            modal: true,
            width: 650,
            height: 350,
            title: '<?php echo t('Change table status') ?>',
            onClose: function() {
                DataTable.ajax.reload();
            }
        });
    });
});
</script>
