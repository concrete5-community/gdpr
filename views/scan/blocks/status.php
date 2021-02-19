<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var string $action */
/** @var string $blockTypeHandle */
/** @var int $pageId */
/** @var \A3020\Gdpr\Entity\BlockScanStatus $status */
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="block-status" action="<?php echo $action ?>">
        <?php
        /** @var \Concrete\Core\Validation\CSRF\Token $token */
        echo $token->output('gdpr.scan.blocks.status');

        echo $form->hidden('id', $status->getId());
        echo $form->hidden('blockTypeHandle', $blockTypeHandle);
        echo $form->hidden('pageId', $pageId);
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
            <label for="fixedOnAllPages" class="control-label">
                <?php
                echo $form->checkbox('fixedOnAllPages', 1, $status->isFixedOnAllPages());
                echo ' '.t('Mark as fixed on all pages');
                ?>
            </label>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('comments', t('Comments'));
            echo $form->textarea('comments', $status->getComments(), [
                'autofocus' => 'autofocus',
                'rows' => 5,
            ]);
            ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?php echo t('Cancel')?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?php echo t('Save')?></button>
        </div>
    </form>
</div>
