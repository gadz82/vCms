// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    var password = document.getElementById("new-password"),
        confirm_password = document.getElementById("new-repassword"),
        email= document.getElementById("register-form-email");

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

    $('#data_di_nascita').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd"
    });

    $('#user-delete').on('click', function(e){
        e.preventDefault();
        if(confirm('Attenzione, sei sicuro di voler eliminare definitivamente il tuo account?')){
            window.location.href = $('#user-delete').attr('href');
        }
    });

}));