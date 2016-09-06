var register = {
    init: function(App, core, settings){
        register.setup(App, core, settings);
        register.bindValidation();
        register.bindEvent();
    },
    
    setup: function(App, core, settings){
        register.core = core;
        register.data = App.data;
        
        register.form = $('#register-form');
        register.usernameInput = $( "#fos_user_registration_form_username" );
        register.router = {
            checkUsername: Routing.generate('user_check_nickname')
        };
        
        register.dom = {
            submitBtn: $('#submit-btn'),
            ruleChekbox: $('#rule_agree'),
            userTypeSelect: $('#fos_user_registration_form_isAuthor')
        };
        
        register.addNewValidationRule();
    },
    
    addNewValidationRule: function() {
        $.validator.addMethod("lettersonlyRuEn", function(value, element) {
            return this.optional(element) || /^[a-zа-я]+$/i.test(value);
        }, "Letters only please");
    },
    
    bindEvent: function () {
        $(register.dom.ruleChekbox).click(register.ruleHandler);
        
        $(register.dom.submitBtn).click(register.submitHandler)
        
        $(register.dom.userTypeSelect).change(register.userTypeChange);
    },
    
    bindValidation: function(){
        register.form.validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                'fos_user_registration_form[username]' : {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                    remote: {
                        url: register.router.checkUsername,
                        type: 'post',
                        data: {
                           username: function() {
                               return register.usernameInput.val();
                           } 
                        }    
                    }
                },
                'fos_user_registration_form[plainPassword][first]': {
                    required: true,
                    minlength: 7
                },
                'fos_user_registration_form[plainPassword][second]': {
                    required: true,
                    minlength: 7,
                    equalTo: "#fos_user_registration_form_plainPassword_first"
                },

                'fos_user_registration_form[name]': {
                    required: true,
                    minlength: 3,
                    maxlength: 15,
                    lettersonlyRuEn: true
                },
                'fos_user_registration_form[surname]' : {
                    required: true,
                    minlength: 3,
                    maxlength: 15,
                    lettersonlyRuEn: true
                },
                'fos_user_registration_form[middleName]' : {
                    lettersonlyRuEn: true
                }
            },
        
            messages: {
                'fos_user_registration_form[username]' : {
                    remote: 'Это имя уже занято.'
                },
                'fos_user_registration_form[name]': {
                    lettersonlyRuEn: 'Разрешается только буквы.'
                },
                'fos_user_registration_form[surname]' : {
                    lettersonlyRuEn: 'Разрешается только буквы.'
                },
                'fos_user_registration_form[middleName]' : {
                    lettersonlyRuEn: 'Разрешается только буквы.'
                }
            }
        });

    },
    
    userTypeChange: function(e) {
        
        if($(this).val() === 'yes') {
            register.core.showElement($('#author-warning'));
        } else {
            register.core.hideElement($('#author-warning'));
        }
    },
    
    ruleHandler: function (e) {
        if(!$(this).is(':checked') ) {
              $(register.dom.submitBtn).attr("disabled", true);
        } else {
            $(register.dom.submitBtn).attr("disabled", false);
        };
    },
    
    submitHandler: function (e) {
        if(typeof grecaptcha !== 'undefined') {
            if(grecaptcha.getResponse() == "") {
               // e.preventDefault();
            }
        }
    }
};

$(function(){register.init(App, core, {})});
