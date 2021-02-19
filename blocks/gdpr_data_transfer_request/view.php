<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var bool $hasPendingRequest */
/** @var Concrete\Core\User\User $user */
/** @var Concrete\Core\Validation\CSRF\Token $token */
/** @var Concrete\Core\Error\ErrorList\ErrorList $errors */

if (!$user->isLoggedIn()) {
    return;
}
?>

<div class="gdpr-data-transfer-request">
	<?php
    View::element(
        'system_errors',
        [
            'format' => 'block',
            'error' => isset($errors) ? $errors : null,
            'success' => isset($sent) ? t("Your data transfer request has been submitted.") : null,
        ]
    );

    // Hide the form if request has been submitted
    if (!isset($sent) && !$hasPendingRequest) {
        ?>
        <form method="post" action="<?php echo $this->action('submit') ?>">
            <?php $token->output('gdpr.data_transfer_request'); ?>

            <?php
            echo $form->submit('submit', t('Request Data Transfer'));
            ?>
        </form>
        <?php
    }
    ?>
</div>
