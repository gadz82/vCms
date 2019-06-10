// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    var password = document.getElementById("register-form-password"), confirm_password = document.getElementById("register-form-repassword"), email= document.getElementById("register-form-email");

    function validatePassword(){
        if(password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Le password non corrispondono");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    function validateEmail(){
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if(!re.test(String(email.value).toLowerCase())){
            email.setCustomValidity("Email in formato non valido");
        } else {
            email.setCustomValidity("");
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;
    email.onchange = validateEmail;
    email.onkeyup = validateEmail;

    var spinner = $('<div class="col-xs-12 text-center"><i class="fa fa-circle-o-notch fa-spin f-20"></div>');
    $('#register-form').submit(function(e){
        e.preventDefault();
        var form = $('#register-form'), submit = $('#register-form-submit');
        submit.after(spinner);

        $.ajax({
            url: '/user/registration',
            type: 'POST',
            data: form.serialize(),
            cache: false,
            success: function (data){
                if(data.success){
                    if(data.content !== undefined){
                        spinner.fadeOut('fast', function(){
                            submit.after('<div class="row m-t-lg-15">' +
                                '<div class="alert alert-success alert-dismissible" role="alert">' +
                                '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                                '<span class="m-l-lg-10">'+data.content+'</span>' +
                                '</div></div>').fadeIn('fast');
                        });
                    } else {
                        spinner.fadeOut('fast');
                    }
                } else {
                    spinner.fadeOut('fast', function(){
                        submit.after('<div class="row m-t-lg-15">' +
                            '<div class="alert alert-danger alert-dismissible" role="alert">' +
                            '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                            '<span class="m-l-lg-10">'+data.content+'</span>' +
                            '</div></div>').fadeIn('fast');
                    });
                }
            },
            error: function(){
                spinner.fadeOut('fast', function(){
                    submit.after('<div class="row m-t-lg-15">' +
                        '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<span class="fa fa-exclamation-circle" aria-hidden="true"></span>' +
                        '<span class="m-l-lg-10">'+data.content+'</span>' +
                        '</div></div>').fadeIn('fast');
                });
            }
        })
    });

}));