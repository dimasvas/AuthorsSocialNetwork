var confirm_account = {
    init: function(App, core) {
        this.setup(App, core);
        this.bindEvent();
    },
    
    setup: function(App, core) {
        this.redirectSec = 7;
        
        this.route = {
            indexPage: Routing.generate('home_page')
        };
    },
    
    bindEvent: function(){
        this.startCounter();
    },
    
    startCounter: function () {
        var seconds = this.redirectSec;
        
        $("#dvCountDown").show();
        
        $("#lblCount").html(seconds);
        
        setInterval(function () {
            seconds--;
            $("#lblCount").html(seconds);
            if (seconds === 0) {
                $("#dvCountDown").hide();
                window.location = confirm_account.route.indexPage;
            }
        }, 1000);
    }
};

$(function(){confirm_account.init(App, core, {})});
