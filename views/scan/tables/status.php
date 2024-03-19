<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var string $action */
/** @var string $tableName */
/** @var \A3020\Gdpr\Entity\TableScanStatus $status */
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="table-status" action="<?php echo $action ?>">
        <?php
        /** @var \Concrete\Core\Validation\CSRF\Token $token */
        echo $token->output('gdpr.scan.tables.status');

        echo $form->hidden('id', $status->getId());
        echo $form->hidden('tableName', $tableName);
        ?>

        <div class="form-group">
            <label for="fixed" class="control-label">
                <?php
                echo $form->checkbox('fixed', 1, $status->isFixed());
                echo ' '.t('Mark as GDPR compliant');
                ?>
            </label>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('notes', t('Notes'));
            echo $form->textarea('notes', $status->getNotes(), [
                'autofocus' => 'autofocus',
                'rows' => 5,
            ]);
            ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-default btn-secondary pull-left" data-dialog-action="cancel"><?php echo t('Cancel')?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?php echo t('Save')?></button>
        </div>
    </form>
</div>
