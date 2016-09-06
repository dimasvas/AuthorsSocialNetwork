var userSettings = {
    init: function(App, core){
        userSettings.setup(App, core);
        
        userSettings.initInlineEditor();
        userSettings.bindValidation();
        userSettings.bindInputFocus();
    },
    
    setup: function(App, core){
        
        userSettings.core = core;
        
        userSettings.dom = {
            emailForm: $('#changeEmailForm'),
            emailEmailInput: ('#changeEmailInput'),
            emailPasswordInput: ('#inputEmailPassword'),
            emailMsgContainer: $('#email-msg'),
            emailLoadIndicator: $('#email-load-inidcator'),
            
            passwordForm: $('#changePasswordForm'),
            currentPasswordInput: $('#currentPasswordInput'),
            newPasswordInput: $('#newPasswordInput'),
            retypePasswordInput: $('#retypePasswordInput'),
            passwordLoadIndicator: $('#password-load-indicator'),
            passwordMsgContainer: $('#password-msg'),
            
            editCity: $('#edit-city'),
            editAbout: $('#edit-about'),
            editLocale: $('#edit-locale'),
            editCountry: $('#edit-country'),
            editShowAlias: $('#edit-show-alias'),
            editHideBirthday: $('#edit-hide-birthday')
        };
        
        userSettings.rules = {
            cityLength: 20,
            aboutLength: 199
        };
        
        userSettings.errorsMsg = {
            server: Translator.trans('system.errors.imposible_action'),
            bigTitle: Translator.trans('system.errors.big_title'),
            bigText: Translator.trans('system.errors.big_text'),
            wrongValue: Translator.trans('system.errors.wrong_value'),
            wrongEmail: Translator.trans('system.errors.bad_email')
        };
        
        userSettings.text = {
            empty: Translator.trans('common.empty')
        };
        
        userSettings.route = {
            edit: Routing.generate('user_edit_profile'),
            editEmail: Routing.generate('user_edit_email'),
            editPassword: Routing.generate('user_edit_password')
        };
        
        userSettings.paramName = {
            city: 'edit_city',
            about: 'about_me',
            showAlias: 'show_alias',
            hideBirthday: 'hide_birthday',
            locale: 'edit_locale',
            country: 'edit_country'
        };

        userSettings.setDefaultEditMode();
    },
    
    bindInputFocus: function(){
        $('#changeEmailInput, #inputEmailPassword').focus(function(){
            if($(userSettings.dom.emailMsgContainer).is(':visible')){
                userSettings.focusEmailInputs('email');
            }
        });
        
        $('#currentPasswordInput, #newPasswordInput, #retypePasswordInput').focus(function(){
            if($(userSettings.dom.passwordMsgContainer).is(':visible')){
                userSettings.focusEmailInputs('password');
            }
        });
    },
    
    setDefaultEditMode: function(){
        $.fn.editable.defaults.mode = 'inline';
    },
    
    bindValidation: function(){
        userSettings.dom.emailForm.validate({
            lang: 'ru',
            rules: {
                changeEmailInput: {
                    required: true,
                    email: true
                },
                inputEmailPassword: {
                    required: true,
                    minlength: 5
                }
            },
            submitHandler: userSettings.emailSubmitHandler
        });
        
        userSettings.dom.passwordForm.validate({
            lang: 'ru',
            rules: {
                currentPasswordInput: {
                    required: true,
                    minlength: 5
                },
                newPasswordInput: {
                    required: true,
                    minlength: 5
                },
                retypePasswordInput: {
                    required: true,
                    minlength: 5,
                    equalTo: "#newPasswordInput"
                },
            },
            submitHandler: userSettings.passwordSubmitHandler
        });
    },
    
    emailSubmitHandler: function(form, e){
        e.preventDefault();
        
        /* Reset Message container*/
        userSettings.resetResponseMsg(userSettings.dom.emailMsgContainer);
        /* Show loading indicator*/
        userSettings.toggleLoadIndicator(userSettings.dom.emailLoadIndicator, 'show');
        
        /* Send request to server*/
        userSettings.core.send({
            method: "PUT",
            url:  userSettings.route.editEmail,
            data: userSettings.getEmailFormValues(),
            success: userSettings.emailResponseHandler
        }); 
    },
    
    passwordSubmitHandler: function(form, e){
        e.preventDefault();

        /* Reset Message container*/
        userSettings.resetResponseMsg(userSettings.dom.passwordMsgContainer);
        /* Show loading indicator*/
        userSettings.toggleLoadIndicator(userSettings.dom.passwordLoadIndicator, 'show');

        /* Send request to server*/
        userSettings.core.send({
            method: "PUT",
            url:  userSettings.route.editPassword,
            data: userSettings.getPasswordFormValues(),
            success: userSettings.passwordResponseHandler
        }), userSettings.emailSubmitResponse;
    },
    
    getEmailFormValues: function(){
        return {
            email: $(userSettings.dom.emailEmailInput).val(),
            password: $(userSettings.dom.emailPasswordInput).val()       
        };
    },
    
    getPasswordFormValues: function(){
        return {
            password: $(userSettings.dom.currentPasswordInput).val(),
            newPassword: $(userSettings.dom.newPasswordInput).val(),
            retypePassword: $(userSettings.dom.retypePasswordInput).val()  
        };
    },
    
    emailResponseHandler: function(data) {
        var container = userSettings.dom.emailMsgContainer;
        
        /* Hide Indicator */        
        userSettings.toggleLoadIndicator(userSettings.dom.emailLoadIndicator, 'hide');
        
        /* Show message */
        data.error ? 
            userSettings.showResponseMsg('error', container) : 
            userSettings.showResponseMsg('success', container);
    },
    
    passwordResponseHandler: function(data) {
        var container = userSettings.dom.passwordMsgContainer;
        
        /* Hide Indicator */         
        userSettings.toggleLoadIndicator(userSettings.dom.passwordLoadIndicator, 'hide');
        
        /* Show message */
        data.error ? 
            userSettings.showResponseMsg('error', container) : 
            userSettings.showResponseMsg('success', container);
    },
    
    
    showResponseMsg: function(type, container){
        var classType = (type === 'success') ? 'text-success' : 'text-danger',
            msgText = (type === 'success') ? 
                        '<span class="glyphicon glyphicon-ok"></span>' + '  ' + 'Изменения сохранены' :
                        '<span class="glyphicon glyphicon-remove"></span>' + '  ' + 'Неверный пароль';
        
        $(container).addClass(classType)
                    .html(msgText)
                    .show();
    },
    
    toggleLoadIndicator: function(container, action){
        action === 'show' ? $(container).css('display', 'inline-block') : $(container).hide();
    },
    
    resetResponseMsg: function(container){
        $(container).removeClass('text-success text-danger')
            .text('')
            .hide();
    },
    
    focusEmailInputs: function(type){
        var container = (type === 'email') ? 
                        userSettings.dom.emailMsgContainer : 
                        userSettings.dom.passwordMsgContainer;
                
        userSettings.resetResponseMsg(container);
    },
    
    initInlineEditor: function(){
        userSettings.dom.editCity.editable({
            type: 'text',
            send : 'always',
            name: userSettings.paramName.city,
            url : userSettings.route.edit,
            validate: function(value){
                if(value.length > userSettings.rules.cityLength) {
                    return userSettings.errorsMsg.bigTitle;
                }
            },
            error: function (response, newValue){
                return userSettings.errorsMsg.server;
            }
        });
        
        userSettings.dom.editAbout.editable({
            type: 'textarea',
            emptytext: userSettings.text.empty,
            url : userSettings.route.edit,
            send : 'always',
            name: userSettings.paramName.about,
            validate: function(value){
                if(value.length > userSettings.rules.aboutLength) {
                    return userSettings.errorsMsg.bigText;
                }
            },
            error: function (response, newValue){
                return userSettings.errorsMsg.server;
            }
        }); 
        
        userSettings.dom.editLocale.editable({
            url : userSettings.route.edit,
            send : 'always',
            name: userSettings.paramName.locale,
            error: function (response, newValue) {
                return userSettings.errorsMsg.server;
            }
        });
        
        userSettings.dom.editCountry.editable({
            url : userSettings.route.edit,
            send : 'always',
            name: userSettings.paramName.country,
            error: function (response, newValue){
                return userSettings.errorsMsg.server;
            }
        });
        
        userSettings.dom.editShowAlias.editable({ 
            url : userSettings.route.edit,
            send : 'always',
            name: userSettings.paramName.showAlias,
            validate: function(value){
                value = parseInt(value, 10);
                
                if(value < 0 && value > 1) {
                    return userSettings.errorsMsg.wrongValue;
                }
            },
            error: function (response, newValue){
                return userSettings.errorsMsg.server;
            }
        });
        
        userSettings.dom.editHideBirthday.editable({
            url : userSettings.route.edit,
            send : 'always',
            name: userSettings.paramName.hideBirthday,
            validate: function(value) {
                value = parseInt(value, 10);
                
                if(value < 0 && value > 1) {
                    return userSettings.errorsMsg.wrongValue;
                }
            },
            error: function (response, newValue) {
                return userSettings.errorsMsg.server;
            }
        });
    }
};

$(function(){
    userSettings.init(App, core, {});
    
    croppieHandler.init(
        App, core, 
        {
            url: Routing.generate('img_update_profile'),
            settings: {
                viewport: { width: 200, height: 200 },
                boundary: {width: 230, height: 230 },
                exif: true
            },
            page: 'profile'
        }
    );
});