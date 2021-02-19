<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

$app->make('help')->display(
    t("A user should have the right to request his/her data. This page shows all data requests.")
);
?>

<div class="ccm-dashboard-content-inner">
    <table class="table" id="tbl-requests">
        <thead>
            <tr>
                <th><?php echo t('Date') ?></th>
                <th><?php echo t('User') ?></th>
                <th><?php echo t('Sent') ?></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>


<script>
$(document).ready(function() {
    var DataTableElement = $('#tbl-requests');

    var DataTable = DataTableElement.DataTable({
        ajax: '<?php echo Url::to('/ccm/system/gdpr/data_transfer/requests') ?>',
        lengthMenu: [[15, 40, 80, -1], [15, 40, 80, '<?php echo t('All') ?>']],
        language: {
            emptyTable: '<?php echo t('There are no data transfer requests.') ?>'
        }
    });
});
</script>
