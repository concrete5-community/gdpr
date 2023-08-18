<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();
?>

<div class="ccm-dashboard-content-inner">
    <table class="table" id="tbl-requests">
        <thead>
            <tr>
                <th><?php echo tc('Describes a date value', 'Requested at') ?></th>
                <th><?php echo t('User') ?></th>
                <th><?php echo t('Mailed at') ?></th>
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
        columns: [
            {
                data: "requested_at"
            },
            {
                data: "user_name"
            },
            {
                data: "mailed_at"
            }
        ],
        language: {
            emptyTable: '<?php echo t('There are no data transfer requests.') ?>'
        }
    });
});
</script>
