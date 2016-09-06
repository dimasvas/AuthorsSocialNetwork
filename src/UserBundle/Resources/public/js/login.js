var login = {
    init: function(App, core, extra){
        login.setup();
        login.bindValidation();
    },
    
    setup: function(){
        login.form = $('#login-form');
    },
    
    bindValidation: function(){
        login.form.validate({
            lang: 'ru',
            errorPlacement: function(error, element) {         
                error.insertBefore(element);
            },
            rules: {
                _username: {
                    required: true,
                },
                _password: {
                    required: true,
                }
            }
        });
    }
};

$(function(){login.init(App, core, {})});
