var contact = {
    init: function (App, core, options) {
        contact.setup(App, core, options);
        contact.bindEvent();
    },
    
    setup: function (App, core, options) {
        contact.core = core;
    },
    
    bindEvent: function () {
        $('#contact-form').submit(contact.contactSubmit);
        
        $('#name, #email, #message').focus(contact.onInputFocus);
    },
    
    contactSubmit: function (e) {
        e.preventDefault();

           var self = $(this),
                name = $('#name', self).val(),
                email = $('#email', self).val(),
                message = $('#message', self).val(),
                token = $('#token', self).val();

           if(contact.isValid(self)){
               
               contact.core.send({
                    method: "POST",
                    url: Routing.generate('feedback_create'),
                    data: {name: name, email: email, message: message, 'token': token}
                }, function (response){
                     contact.submitResponse(response, self);
                });
           } else {
               $('.required-text').css({color: '#D04442'});
           }
    },
    
    submitResponse: function (response, target) {
        if (response.status === 'success') {
            $('#name, #email, #message', target).val('');
            $('#success-submit').removeClass('hidden');
            $('.required-text').css({color: '#242322'});
        }
    },
    
    isValid: function (form){
        var noError = true;
        
        if(!$('#name', form).val()){
            $('#name', form).addClass('error');
            noError = false;
        }
        
        if(!$('#email', form).val()){
            $('#email', form).addClass('error');
            noError = false;
        }
        
        if(!contact.isEmail($('#email', form).val())){
            $('#email', form).addClass('error');
            noError = false;
        }
        
        if(!$('#message', form).val()){
            $('#message', form).addClass('error');
            noError = false;
        }
        
        return noError;
    },
    
    isEmail: function(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    },
    
    onInputFocus: function (e) {
        $(this).removeClass('error');
        $('#success-submit').addClass('hidden');
    }
};

$(function(){contact.init(App, core, {})});