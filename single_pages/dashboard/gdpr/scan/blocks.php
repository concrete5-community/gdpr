<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

/** @var \A3020\Gdpr\Html\FontAwesomeIcon $iconHelper */
$iconHelper = $app->make(\A3020\Gdpr\Html\FontAwesomeIcon::class);
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a class="btn btn-primary toggle-settings" data-caption-toggled="<?php echo t('Hide settings') ?>"><?php echo t('Show settings'); ?></a>
</div>

<div class="ccm-dashboard-content-inner page-scan-blocks">
    <div class="settings hide">
        <form method="post" action="<?php echo $this->action('save') ?>">
            <?php
            /** @var $token \Concrete\Core\Validation\CSRF\Token */
            echo $token->output('a3020.gdpr.scan.blocks');
            ?>

            <div class="form-group">
                <label class="control-label" for="ignoreFixedBlocks">
                    <?php
                    /** @var $ignoreFixedBlocks bool */
                    echo $form->checkbox('ignoreFixedBlocks', 1, $ignoreFixedBlocks);
                    ?>

                    <?php echo t("Ignore blocks that are marked as compliant"); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label" for="ignoreCoreBlocks">
                    <?php
                    /** @var $ignoreCoreBlocks bool */
                    echo $form->checkbox('ignoreCoreBlocks', 1, $ignoreCoreBlocks);
                    ?>

                    <?php echo t('Ignore core blocks'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php echo t("Here you can specify custom block types to scan for. Use one block type handle per line."); ?>"
                       for="customBlockTypes">
                    <?php echo t('Custom block types'); ?>
                </label>

                <?php
                /** @var string $customBlockTypes */
                echo $form->textarea('customBlockTypes', $customBlockTypes, [
                    'placeholder' => t('Leave blank to only use the default block types'),
                    'style' => 'min-height: 100px; max-width: 400px',
                ]);
                ?>
            </div>

            <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
        </form>
    </div>

    <table class="table table-striped table-bordered" id="tbl-blocks">
        <thead>
            <tr>
                <th><?php echo t('Icon') ?></th>
                <th><?php echo t('Name'); ?></th>
                <th>
                    <?php echo t('Where') ?>
                    <?php echo $iconHelper->question(t("The block has been found on this page. As it may process personal data, make sure it complies to GDPR.")) ?>
                </th>
                <th><?php echo t('Why') ?></th>
                <th>
                    <?php
                    echo t('Status');
                    ?>
                </th>
                <th>
                    <?php
                    echo t('Core');
                    ?>
                    <?php echo $iconHelper->question(t("Whether this is part of the concrete5 core.")) ?>
                </th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function() {
    var DataTableElement = $('#tbl-blocks');

    var DataTable = DataTableElement.DataTable({
        ajax: '<?php echo Url::to('/ccm/system/gdpr/scan/blocks') ?>',
        lengthMenu: [[15, 40, 80, -1], [15, 40, 80, '<?php echo t('All') ?>']],
        columns: [
            {
                data: function(row, type, val) {
                    return '<img class="block-icon" style="width: 50px;" src="'+row.block_icon +'">';
                }
            },
            {
                data: function(row, type, val) {
                    return '<a target="_blank" class="block-type" ' +
                        'href="<?php echo Url::to('/dashboard/blocks/types/inspect/') ?>/' + row.block_type_id+'">' +
                        row.block_type +
                    '</a><br><small class="text-muted">' + row.block_type_handle + '</small>';

                }
            },
            {
                data: function(row, type, val) {
                    return '<a target="_blank" href="'+row.page_url+'">' + row.page_name + '</a>';
                }
            },
            {
                data: "why"
            },
            {
                data: function(row, type, val) {
                    var html = '<a href="#"  data-dialog="status" data-page-id="'+row.page_id+'" data-block-type="'+row.block_type_handle+'" class="btn btn-default">';

                    if (row.fixed) {
                        html += '<?php echo $iconHelper->check() ?>';
                    } else {
                        html += '<?php echo $iconHelper->warning() ?>';
                    }

                    html += '</i></a>';

                    return html;
                }
            },
            {
                data: function(row, type, val) {
                    return row.is_core_block === true ? '<?php echo t('Yes') ?>' : '<?php echo t('No') ?>';
                }
            }
        ],
        order: [[ 4, "asc" ]],
        language: {
            emptyTable: '<?php echo t('No blocks have been found that meet the current search criteria.') ?>'
        }
    });

    DataTableElement.on('click', '[data-dialog]', function() {
        var blockType = $(this).data('block-type');
        var pageId = $(this).data('page-id');

        jQuery.fn.dialog.open({
            href: '<?php echo Url::to('/ccm/system/gdpr/scan/block') ?>/'+blockType+'/'+pageId,
            modal: true,
            width: 650,
            height: 350,
            title: '<?php echo t('Change block status') ?>',
            onClose: function() {
                DataTable.ajax.reload();
            }
        });
    });
});
</script>
