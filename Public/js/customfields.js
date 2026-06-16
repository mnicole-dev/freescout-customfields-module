(function ($) {
    'use strict';

    $(document).on('click', '.cf-save', function () {
        var $block = $('#cf-block');
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: $block.data('url'),
            method: 'POST',
            data: $block.find('select, input, textarea').serialize() + '&_token=' + encodeURIComponent($block.data('csrf')),
            dataType: 'json'
        }).done(function (resp) {
            if (resp.status === 'success') {
                $block.find('.cf-saved').removeClass('hidden');
                setTimeout(function () { $block.find('.cf-saved').addClass('hidden'); }, 2500);
            } else {
                alert(resp.msg || 'Error');
            }
        }).fail(function () { alert('Error'); }).always(function () { $btn.prop('disabled', false); });
    });
})(jQuery);
