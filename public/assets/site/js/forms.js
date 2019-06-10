// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function ($, window, document) {

    $('document').ready(function () {

        if ($('.selectpicker').length > 0) {
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
                $('.selectpicker').selectpicker('mobile');
            } else {
                $('.selectpicker').selectpicker();
            }
        }

        if ($('input[type="checkbox"][class="input-checkbox"], input[type="radio"][class="input-checkbox"]').length > 0) {

            $('input[type="checkbox"][class="input-checkbox"], input[type="radio"][class="input-checkbox"]').on('ifCreated', function (e) {
                if ($(this).hasClass('icheck-group-addon')) {
                    if (!$(this).is(':checked')) {
                        $('.icheck-state:input[type="text"]', $(this).closest('.input-group.icheck')).val('').attr('disabled', 'disabled');
                    }
                }
            }).iCheck({
                checkboxClass: 'icheckbox_square-red',
                radioClass: 'iradio_square-red',
                tap: true,
                inheritClass: true
            });
        }
    });
}));