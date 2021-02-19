<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<label for="acceptTerms" style="margin-bottom: 30px;">
    <input type="checkbox" value="1" id="acceptTerms" name="acceptTerms" required="required">

    <?php
    echo t("I declare that I have the right to use this add-on");
    ?>
</label>

<div class="alert alert-warning" id="consent">
    <i class="fa fa-warning"></i>
    <strong>
        <?php
        echo "Only use this add-on if you've been given a free license or if you've bought a license. ";
        ?>
    </strong>
    <?php

    echo sprintf("Please note that the %sconcrete5 add-on license%s is per website.",
        '<a style="color: inherit; text-decoration: underline;" href="https://www.concrete5.org/help/legal/commercial_add-on_license/" target="_blank">',
        '</a>'
    ).' ';
    echo "We can't continue developing high-quality add-ons if add-ons are redistributed without permission.".' ';
    echo sprintf("Thanks for your comprehension, we hope you enjoy the %s add-on!", "GDPR");
    ?>
</div>

<div class="alert alert-success hide" id="thanks">
    <i class="fa fa-check"></i>

    <?php
    echo 'Thanks! You can now install the add-on.';
    ?>
</div>

<script>
function toggleDivs() {
    var isChecked = $('#acceptTerms').is(':checked');
    $('#consent').toggleClass('hide', isChecked);
    $('#thanks').toggleClass('hide', !isChecked);
}
toggleDivs();

$('#acceptTerms').click(function() {
    toggleDivs();
});
</script>
