var reset = {
    init: function(App, core, extra){
        reset.setup();
        reset.bindValidation();
    },
    
    setup: function(){
        reset.form = $('#reset-form');
    },
    
    bindValidation: function(){
        reset.form.validate({
            lang: 'ru',
            errorPlacement: function(error, element) {         
                error.insertBefore(element);
            },
            rules: {
                username: {
                    required: true
                }
            }
        });
    }
};

$(function(){reset.init(App, core, {})});