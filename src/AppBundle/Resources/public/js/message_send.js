var message_send = {
    init: function (App, core) {
        this.setup(App, core);
        this.bindEvent();
    },
    
    setup: function (App, core) {
        this.core = core;
        this.recipient = App.data.recipient;
        
        this.dom = {
            textarea: $('.message-textarea'),
            modal: $('#authorMessageModal')
        };
        
        this.route = {
            send: Routing.generate('message_create', {recipient: this.recipient})
        };
    },
    
    bindEvent: function () {
        $('.send-btn').click(message_send.sendMessage);
    },
    
    sendMessage: function (e) {
        e.preventDefault();
            
        var message = message_send.trimMessage($(message_send.dom.textarea).val());
           
        if(message.length < 1) { 
            return;
        }
           
        send({
            method: "POST",
            url:  message_send.route.send,
            data: {body: message, rpc: true}
        }, function (response){
            message_send.onResponse();
        });
    },
    
    trimMessage: function(message){
       return message.trim();
    },
   
    toggleModalTitle: function(){
       $('.message-new-text', message_send.dom.modal).toggleClass('hidden');
       $('.message-sending-text', message_send.dom.modal).toggleClass('hidden');
    },
    
    onResponse: function () {
        message_send.clearTextarea();
        message_send.closeModal();
    },

    clearTextarea: function () {
        $(message_send.dom.textarea).val('');
    },
    
    closeModal: function () {
        message_send.dom.modal.modal('hide');
    }
};

$(function(){message_send.init(App, core, {})});