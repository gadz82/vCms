// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function ($, window, document) {
    var password = document.getElementById("reset-form-password"), confirm_password = document.getElementById("reset-form-repassword");

    function validatePassword() {
        if (password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Le password non corrispondono");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;

    var spinner = $('<div class="col-xs-12 text-center"><i class="fa fa-circle-o-notch fa-spin f-20"></div>');
    $('#password-reset-form').submit(function (e) {
        e.preventDefault();
        var form = $('#password-reset-form'), submit = $('#password-reset-submit');
        submit.after(spinner);

        $.ajax({
            url: '/user/resetPassword',
            type: 'POST',
            data: form.serialize(),
            cache: false,
            success: function (data) {
                if (data.success) {
                    if (data.content !== undefined) {
                        spinner.fadeOut('fast', function () {
                            submit.after('<div class="m-t-lg-15">' +
                                '<div class="alert alert-success alert-dismissible" role="alert">' +
                                '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                                '<span class="m-l-lg-10">' + data.content + '</span>' +
                                '</div></div>').fadeIn('fast');
                        });
                        setTimeout(function () {
                            window.location.href = '/user';
                        }, 3000);
                    } else {
                        spinner.fadeOut('fast');
                    }
                } else {
                    spinner.fadeOut('fast', function () {
                        submit.after('<div class=" m-t-lg-15">' +
                            '<div class="alert alert-danger alert-dismissible" role="alert">' +
                            '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                            '<span class="m-l-lg-10">' + data.content + '</span>' +
                            '</div></div>').fadeIn('fast');
                    });
                }
            },
            error: function () {
                spinner.fadeOut('fast', function () {
                    submit.after('<div class="m-t-lg-15">' +
                        '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                        '<span class="m-l-lg-10">' + data.content + '</span>' +
                        '</div></div>').fadeIn('fast');
                });
            }
        })
    });

}));