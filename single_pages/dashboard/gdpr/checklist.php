<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
?>

<div class="ccm-dashboard-content-inner">
    <?php
    $category = '';

    /** @var \A3020\Gdpr\Entity\Check[] $checks */
    foreach ($checks as $check) {
        if ($check->getCategory() !== $category) {
            $category = $check->getCategory();

            echo '<h3>'.t($category).'</h3>';
        }
        ?>
        <div class="check">
            <label>
                <?php
                echo $form->checkbox('check[]', 1, $check->isChecked(), [
                    'data-id' => $check->getId(),
                ]);
                echo ' '.t($check->getName());
                ?>
            </label>
        </div>
        <?php
    }
    ?>
</div>

<script>
$(document).ready(function() {
    $('.check :checkbox').change(function() {
        var data = {
            'id': $(this).data('id'),
            'checked': this.checked ? 1 : 0
        };

        $.post('<?php echo $this->action('check') ?>', data);
    });
});
</script>