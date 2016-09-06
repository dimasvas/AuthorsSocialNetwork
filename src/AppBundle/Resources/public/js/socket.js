var socket = {
    init: function (App, core, options) {
        socket.setup(App, core, options);
        socket.bindEvents();
    },
    
    setup: function (App, core, options) {
        
        socket.app = App;
        socket.core =  core;
        socket.options = options;
        
        if(options.isMessagePage) {
            socket.message = message;
        }
        
        socket.ws = "ws://" + socket.app.domain +":8080";
        socket.websocket;
        
        socket.dom = {
          msgBadge: $('#message-badge')  
        };
    },
    
    bindEvents: function () {
      socket.initConnetcion();
      
      socket.isConnected(socket.succesConnection);
      
      socket.isDisconnected(socket. errorConection);
      
    },
    
    initConnetcion: function () {
        socket.websocket = WS.connect(socket.ws);
    },
    
    isConnected: function (callback) {
        socket.websocket.on("socket/connect", function(session){
            callback(session);
        });  
    },
    
    isDisconnected: function (callback) {
        socket.websocket.on("socket/disconnect", function(error){
            callback(error);
        });
    },
    
    succesConnection: function (session) {
        
        console.log('Client is connected!');
        
        socket.subscribeToMessage(session);
    },
    
    subscribeToMessage: function (session) {
        session.subscribe("message/channel", function(uri, payload){
            socket.handleMessage(payload.msg);
        });
    },
    
//    unsubscribeMesssage: function (session) {
//        session.unsubscribe("message/channel", function(uri, payload){
//            console.log("unsubscribed");
//        });
//    },
    
    errorConection: function (error) {
        //scheduled reconnect to occur in 5 second(s) - 10 attempts
        console.log("Disconnected for " + error.reason + " with code " + error.code);
    },
    
    handleMessage: function (msg) {
        if(socket.options.isMessage) {
            
        } else {  
            socket.increaseMsgCounter();
        }
    },
    
    increaseMsgCounter: function () {
        var currentCounter = parseInt($(socket.dom.msgBadge).text(), 10),
            newCounter = (isNaN(currentCounter) ? 1 : currentCounter + 1);    
        console.log(newCounter);
        $(socket.dom.msgBadge).text(newCounter);
    },
    
    addMessage: function (msg) {
        socket.message.onLoadMessageResponse();
    }
};

$(function(){
    var isMessagePage = (typeof message === 'undefined') ? false : true;
    
    socket.init(App, core, {isMessage: isMessagePage})}
);


