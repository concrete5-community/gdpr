$(document).ready(function() {
    // Show or hide the settings section on the Scan pages
    $('.toggle-settings').click(function() {
        $('.settings').toggleClass('hide');

        var caption = this.innerText;
        this.innerHTML = $(this).data('caption-toggled');

        $(this).data('caption-toggled', caption);
    });
});
