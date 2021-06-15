$(function () {
    $('#default-image-selector').on('click', function (e) {
        window.open('media.php?plugin_id=grayscale&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });
});