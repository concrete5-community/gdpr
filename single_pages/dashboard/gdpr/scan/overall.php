<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

$app->make('help')->display(
   t('This page will perform various checks on your current configuration.')
);

/** @var \Concrete\Core\Config\Repository\Repository $config */
?>

<div class="ccm-dashboard-content-inner page-scan-overall">
    <?php
    // SECURE CONNECTION ACTIVE?
    /** @var bool $isConnectionSecure */
    if (!$isConnectionSecure) {
        ?>
        <div class="alert alert-danger">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("The connection to the website is insecure. Please configure an SSL-certificate to make sure all data between the browser and the website is encrypted.").'<br>';
                echo t("A secure connection is a necessity if your website contains forms because form data can be easily intercepted and read if the data is not encrypted.");
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php echo t('Your connection is secure.'); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // ARE EMAILS LOGGED?
    $emailsLogged = $config->get('concrete.log.emails');
    if ($emailsLogged) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("Emails are logged in concrete5. In case an email is sent, e.g. if someone submits a form, a copy of the email will be stored in %sLogs%s.",
                        '<a href="'.Url::to('/dashboard/reports/logs').'">', '</a>').' '.
                    t("Because emails often contain personal data, you might consider disabling this behavior via the %sLogging Settings%s.",
                        '<a href="'.Url::to('/dashboard/system/environment/logging').'">', '</a>'
                    );
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php echo t("Emails won't be logged in concrete5"); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // CONCRETE5 BACKGROUND ON LOGIN PAGE
    $concreteBackgroundDisabled = (bool) $config->get('concrete.white_label.background_url');
    if (!$concreteBackgroundDisabled) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("The login page loads an external image from the concrete5 server. This could leak the visitor's IP address.").' ';
                echo t('Disable the background via the %sSettings page%s.', '<a href="'.Url::to('/dashboard/gdpr/settings').'">', '</a>');
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php echo t("The login page doesn't load an image from the concrete5 servers."); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // PUBLIC REGISTRATION ENABLED?
    $publicRegistrationEnabled = $config->get('concrete.user.registration.enabled');
    if ($publicRegistrationEnabled) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("Public registration is currently enabled. This enables visitors to create a profile and storing personal information.").' ';
                echo t("Make sure that e.g. users can delete their profile, download their data, and that they have given consent to let you store data.").' ';
                echo t("To change the public registration settings, go to %sPublic Registration%s.", '<a href="'.Url::to('/dashboard/system/registration/open').'">', '</a>');
                ?>
            </p>
            <br>

            <p>
                <?php
                echo t('To add a checkbox to the registration form:').' ';

                echo t("Go to %s/dashboard/users/attributes%s, choose 'Add Attribute' and choose 'Checkbox'. Use 'accept_terms' as attribute handle and add a description like 'I have read and understood the terms of use and agree to them' and make it shown and required on the Registration Form.",
                    '<a href="'.Url::to('/dashboard/users/attributes').'">', '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php echo t('Public registration is disabled.'); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // TRACKING CODE CONFIGURED?
    /** @var bool $hasTrackingCode */
    $trackingCodeDisabled = (bool) $config->get('gdpr.settings.tracking.disabled', false);
    if ($hasTrackingCode && !$trackingCodeDisabled) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("A tracking code is configured. You might need user consent before using tools like %s.", "Google Analytics").' ';
                echo t("To view the tracking code(s), go to %sTracking Codes%s.", '<a href="'.Url::to('/dashboard/system/seo/codes').'">', '</a>').'<br>';
                echo t("To temporarily disable tracking codes, go to %sSettings%s.", '<a href="'.Url::to('/dashboard/gdpr/settings').'">', '</a>').' ';
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php
            if ($hasTrackingCode === false) {
                echo t('No tracking code is configured.');
            } else {
                echo t('The tracking code is disabled.');
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
