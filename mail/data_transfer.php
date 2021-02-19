<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Important: Override / customize this file by copying it to /application/mail
 */

// These variables are available:
/** @var \Concrete\Core\Localization\Service\Date $dateHelper */
/** @var \A3020\Gdpr\Entity\DataTransferFile $dataTransferFile */
/** @var \Concrete\Core\Entity\User\User $user */
/** @var string $hash */
/** @var string $downloadLink */


$subject = t('Data transfer download ready');

// HTML BODY
ob_start();
?>
<h2><?php echo t('Your data transfer is ready to be downloaded.') ?></h2>
<p><?php echo t('The download will be available until %s.', $dateHelper->formatDateTime($dataTransferFile->getValidUntil(), true)); ?></p>
<a href="<?php echo $downloadLink ?>"><?php echo $downloadLink; ?></a>
<?php
$bodyHTML = ob_get_clean();


// PLAIN TEXT BODY
ob_start();
?>
<?php echo t('Your data transfer is ready to be downloaded.') ?>
<?php echo t('The download will be available until %s.', $dateHelper->formatDateTime($dataTransferFile->getValidUntil(), true)); ?>
<?php echo $downloadLink; ?>
<?php
$body = ob_get_clean();
ob_end_clean();
