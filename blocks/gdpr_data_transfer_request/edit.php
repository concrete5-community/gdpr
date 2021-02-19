<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<div class="form-group">
    <label>
        <?php
        /** @var bool $includeFiles */
        echo $form->checkbox('includeFiles', 1, $includeFiles);
        echo ' '.t('Include files in the data transfer file');
        ?>
    </label>
</div>
