$(function () {
    // default image
    $('#default-image-selector').on('click', function (e) {
        window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });
    $('#default-image-selector-reset').on('click', function (e) {
        var url = $('input[name="theme-url"]').val() + '/img/intro-bg.jpg';
        var thumb = $('input[name="theme-url"]').val() + '/img/.intro-bg_s.jpg';
        $('#default-image-url').val(url);
        $('#default-image-thumb-url').attr('src',thumb);
    });

    // random images
    for (let i = 0; i < 6; i++) {
        $('#random-image-' + i + '-selector').on('click', function (e) {
            window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
            e.preventDefault();
            return false;
        });
    }
    for (let i = 0; i < 6; i++) {
        $('#random-image-' + i + '-selector-reset').on('click', function (e) {
            var url = $('input[name="theme-url"]').val() + '/img/bg-intro-' + i + '.jpg';
            var thumb = $('input[name="theme-url"]').val() + '/img/.bg-intro-' + i + '_s.jpg';
            $('#random-image-' + i + '-url').val(url);
            $('#custom-image-' + i + '-thumb-url').attr('src',thumb);
        });
    }
});