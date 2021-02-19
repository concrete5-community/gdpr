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
            <?php echo t("Sent emails are not logged."); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // ARE DATABASE QUERIES LOGGED?
    $queriesLogged = $config->get('concrete.log.queries.log');
    if ($queriesLogged) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("Database queries are logged in concrete5. You probably want this to be disabled on production as it slows down each request and because the queries might contain personal data.").' '.
                    t("Consider disabling this the logging via the %sLogging Settings%s.",
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
            <?php echo t("Database queries are not logged."); ?>
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

                echo t("Go to %sUser Attributes%s, choose 'Add Attribute' and choose 'Checkbox'. Use 'accept_terms' as attribute handle and add a description like 'I have read and understood the terms of use and agree to them' and make it shown and required on the Registration Form.",
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
    $mailMethod = $config->get('concrete.mail.method');
    if ($mailMethod === 'smtp') {
        $smtpServer = $config->get('concrete.mail.methods.smtp.server');
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("A non-default SMTP server is detected: (%s). Please check if data is sent to e.g. %s, %s, or %s. You might need an agreement with this service.",
                    $smtpServer, "Mailgun", "SparkPost", "Sendgrid").'<br>'.
                    t("To view the current settings, go to %sSMTP method%s.",
                    '<a href="'.Url::to('/dashboard/system/mail/method').'">', '</a>');
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php
            echo t('The default PHP Mail Function is used.');
            ?>
        </div>
        <?php
    }
    ?>

    <?php
    /** @var bool|null $geoPluginStatus */
    if ($geoPluginStatus === true) {
        ?>
        <div class="alert alert-warning">
            <p>
                <i class="fa fa-warning"></i>
                <?php
                echo t("It seems that the %s geo locator is active and may be used. Please note that this service can send IP addresses to the %s server.",
                        t('geoPlugin'), "MaxMind"
                    ).' '.

                    t("A good alternative might be to download the %s database using the free %sGeolocation with MaxMind GeoIP2%s add-on.",
                        'MaxMind', '<a href="https://www.concrete5.org/marketplace/addons/maxmind-geoip2-geolocator" target="_blank">', '</a>'
                    ).' '.

                    t("To review the settings, go to the %sGeolocation page%s.",
                        '<a href="'.Url::to('/dashboard/system/environment/geolocation').'">', '</a>'
                    );
                ?>
            </p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            <?php
            echo ($geoPluginStatus === null) ? t('No geolocation service seems to be active.') : t('The geoPlugin is not active.');
            ?>
        </div>
        <?php
    }
    ?>
</div>
