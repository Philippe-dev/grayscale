'use strict';
$(function () {
    $('#media-select-cancel').on('click', function () {
        window.close();
    });
    $('#media-select-ok').on('click', function () {
        const main = window.opener;
        const href = $('input[name="url"]').val();
        let buttonId = main.$('input[name="change-button-id"]').val();
        
        if (buttonId == 'default-image-selector') {
            main.$('#default-image-url').prop('value', href).trigger('change');
        } else {
            for (let i = 0; i < 6; i++) {
                if (buttonId == 'random-image-' + i + '-selector') {
                    main.$('#random-image-' + i + '-url').prop('value', href).trigger('change');
                }
            }
        }
        window.close();
    });
});