<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();

$app->make('help')->display(
    t("In case of a data breach, you may want to notify your users.")
);


/** @var bool $sent */
/** @var string $fromName */
/** @var string $fromEmail */
/** @var array $userGroups */
/** @var string $defaultMessage */

if ($sent) {
    return;
}
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('send') ?>" id="frm-notify-users">
        <div class="form-group">
            <?php
            echo $form->label('user_group', t('User group').' *');
            echo $form->select('user_group', $userGroups, [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('fromName', t('From Name').' *');
            echo $form->text('fromName', $fromName, [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('fromEmail', t('From Email Address').' *');
            echo $form->email('fromEmail', $fromEmail, [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('subject', t('Subject').' *');
            echo $form->text('subject', t('Data breach'), [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('message', t('Message').' *');

            $editor = $app->make('editor');
            echo $editor->outputStandardEditor('message', $defaultMessage);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php
                echo $form->submit('submit', t('Send Email'), [
                    'class' => 'btn-primary pull-right'
                ]);
                ?>
            </div>
        </div
    </form>
</div>

<script>
$(document).ready(function() {
   $('#frm-notify-users').on('submit', function() {
        if (!confirm('<?php echo t('Are you sure you want to send an email to this user group?'); ?>')) {
            return false;
        }
   });
});
</script>
