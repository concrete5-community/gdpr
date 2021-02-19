<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();

$app->make('help')->display(
    t("The cookie consent solution is as-is. It's technically very challenging to come up with a solution that is GDPR compliant, built within acceptable time, and easy to use.")
);

/** @var \Concrete\Core\Form\Service\Widget\Color $colorPicker */
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save') ?>">
        <?php echo $token->output('gdpr.cookies.consent'); ?>

        <div class="form-group">
            <label>
                <?php
                echo $form->checkbox('enabled', 1, $config->get('gdpr.cookies.consent.enabled', false));
                echo ' '.t('Enable cookie consent');
                ?>
            </label>
        </div>

        <div class="form-group">
            <?php
            /** @var array $complianceTypeOptions */
            echo $form->label('type', t('Compliance type').' *');
            echo $form->select('type', $complianceTypeOptions, $config->get('gdpr.cookies.consent.type', 'notice'));
            ?>
        </div>

        <div class="form-group">
            <?php
            /** @var array $positionOptions */
            echo $form->label('position', t('Position').' *');
            echo $form->select('position', $positionOptions, $config->get('gdpr.cookies.consent.position', 'bottom'));
            ?>
        </div>

        <div class="form-group">
            <?php
            /** @var array $themeOptions */
            echo $form->label('theme', t('Theme').' *');
            echo $form->select('theme', $themeOptions, $config->get('gdpr.cookies.consent.theme', 'block'));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $colorPicker->output("bannerBackgroundColor", $config->get('gdpr.cookies.consent.banner_background_color', '#000')).'&nbsp;&nbsp;';
            echo $form->label("bannerBackground", t("Banner background"));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $colorPicker->output("bannerTextColor", $config->get('gdpr.cookies.consent.banner_text_color', '#fff')).'&nbsp;&nbsp;';
            echo $form->label("bannerTextColor", t("Banner text"));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $colorPicker->output("buttonBackgroundColor", $config->get('gdpr.cookies.consent.button_background_color', '#f1d600')).'&nbsp;&nbsp;';
            echo $form->label("buttonBackgroundColor", t("Button background"));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $colorPicker->output("buttonTextColor", $config->get('gdpr.cookies.consent.button_text_color', '#000')).'&nbsp;&nbsp;';
            echo $form->label("buttonTextColor", t("Button text"));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('readMorePage', t('Read more page'));
            echo $pageSelector->selectPage('readMorePage', $config->get('gdpr.cookies.consent.read_more_page'));
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('message', t('Message'));
            echo $form->textarea('message', $config->get('gdpr.cookies.consent.message'), [
                'placeholder' => t('Leave blank to use the default message'),
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('dismissButtonText', t('Dismiss button text'));
            echo $form->text('dismissButtonText', $config->get('gdpr.cookies.consent.dismiss_button_text'), [
                'placeholder' => t('Leave blank to use the default text'),
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('allowButtonText', t('Allow button text'));
            echo $form->text('allowButtonText', $config->get('gdpr.cookies.consent.allow_button_text'), [
                'placeholder' => t('Leave blank to use the default text'),
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('denyButtonText', t('Deny button text'));
            echo $form->text('denyButtonText', $config->get('gdpr.cookies.consent.deny_button_text'), [
                'placeholder' => t('Leave blank to use the default text'),
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('policyLinkText', t('Policy link text'));
            echo $form->text('policyLinkText', $config->get('gdpr.cookies.consent.policy_link_text'), [
                'placeholder' => t('Leave blank to use the default text'),
            ]);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php
                echo $form->submit('submit', t('Save'), [
                    'class' => 'btn-primary pull-right'
                ]);
                ?>
            </div>
        </div>
    </form>
</div>
