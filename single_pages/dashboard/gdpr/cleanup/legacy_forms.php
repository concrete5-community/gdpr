<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

/** @var \A3020\Gdpr\Html\FontAwesomeIcon $iconHelper */
$iconHelper = $app->make(\A3020\Gdpr\Html\FontAwesomeIcon::class);
$isVersion9 = $isVersion9 ?? false;
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.gdpr.cleanup.legacy_forms.settings');
        ?>

        <section class="settings-section">
            <div class="form-group">
                <label class="control-label launch-tooltip"
                       title="<?php
                       if ($isVersion9) {
                           echo t("Form submissions are always stored. To remove form submissions automatically, you can run and schedule the task via System & Settings > Automation > Tasks. If you uncheck this option, the task will be uninstalled.");
                       } else {
                           echo t("Form submissions are always stored. To remove form submissions automatically, you can run and schedule the job via Automated Tasks. If you uncheck this option, the job will be uninstalled.");
                       }
                       ?>"
                       for="enableJobToRemoveLegacyFormSubmissions">
                    <?php
                    /** @var bool $enableJobToRemoveLegacyFormSubmissions */
                    echo $form->checkbox('enableJobToRemoveLegacyFormSubmissions', 1, $enableJobToRemoveLegacyFormSubmissions);
                    ?>
                    <?php
                    if ($isVersion9) {
                        echo t('Enable an Automated Task that could remove Legacy Form submissions');
                    } else {
                        echo t('Enable an Automated Job that could remove Legacy Form submissions');
                    }
                    ?>
                </label>
            </div>

            <div class="form-group <?php echo $enableJobToRemoveLegacyFormSubmissions ? '' : 'hide' ?>" id="container-legacy-forms-keep-days">
                <label class="control-label launch-tooltip"
                       title="<?php echo t('You can control how long the submissions may be stored before they are deleted.') ?>"
                       for="legacyFormsKeepDays">
                    <?php echo t('Keep form submissions for x-number of days'); ?>
                </label>

                <?php
                /** @var int $legacyFormsKeepDays */
                echo $form->number('legacyFormsKeepDays', $legacyFormsKeepDays, [
                    'placeholder' => 0,
                ]);
                ?>
            </div>
        </section>

        <button class="btn btn-primary" type="submit"><?php echo t('Save') ?></button>
    </form>
    <hr>

    <?php
    /** @var int $totalFormSubmissions */
    if ($totalFormSubmissions === 0) {
        ?>
        <p>
            <?php
            echo t("No legacy form submissions have been found.");
            ?>
        </p>
        <?php
    } else {
        ?>
        <p>
            <?php
            echo t('Legacy form submissions can be deleted via the %sForm Results%s page.',
                '<a target="_blank" href="'.Url::to('/dashboard/reports/forms/legacy').'">',
                ' ' . $iconHelper->externalLink() . '</a>'
            );
            ?>
        </p>

        <p>
            <?php
            echo t('Number of submissions found: %d.', $totalFormSubmissions);
            ?>
        </p>
        <?php
    }
    ?>
</div>

<script>
    $(document).ready(function() {
        $('.btn-delete-submissions').click(function(e) {
            e.preventDefault();

            if (confirm('<?php echo t('Are you sure you want to remove these form submissions? There is no way to restore this data.') ?>')) {
                window.location.href = $(this).attr('href');
            }
        });

        function toggleFormSubmissions()
        {
            $('#container-legacy-forms-keep-days').toggleClass('hide',
                !$('#enableJobToRemoveLegacyFormSubmissions').is(':checked')
            );
        }

        toggleFormSubmissions();

        $('#enableJobToRemoveLegacyFormSubmissions').click(function() {
            toggleFormSubmissions();
        });
    });
</script>
