var reset_pass = {
    init: function(App, core, extra){
        this.setup();
        this.bindValidation();
    },
    
    setup: function(){
        this.dom = {
            form: $('#reset-pass-form')
        };
    },
    
    bindValidation: function(){
        reset_pass.dom.form.validate({
    
            rules: {
                'fos_user_resetting_form[plainPassword][first]': {
                    required: true,
                    minlength: 5
                },
                'fos_user_resetting_form[plainPassword][second]': {
                    required: true,
                    minlength: 5,
                    equalTo: "#fos_user_resetting_form_plainPassword_first"
                }
            }
        });
    }
};

$(function(){reset_pass.init(App, core, {})});