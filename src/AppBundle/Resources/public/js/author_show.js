/**************************
 * Show Author page script
 **************************/
 var author_show = {
    init: function (App, core) {
        this.setup(App, core);
        this.bindEvent();
    },

    setup: function (App, core) {
        this.App = App;
        this.core = core;

        this.dom = {
            hidden_area: $('#new-comp-hidden-area')
        };
    },
     
    bindEvent: function () {
       $("#new-composition-btn" ).click(this.toggleArea);
       
       $("#block-user-btn").click(author_show.blockUser);
    },
    
    toggleArea: function () {
         $(author_show.dom.hidden_area).toggle("slow");
    },
    
    blockUser: function (e) {
        $blocked_user = $(this).data('user-id');
        
         send({
            method: "POST",
            url: Routing.generate('message_acl_create', {blocked: $blocked_user}),
            data: {}
        }, function (response){
            author_show.hideBlockedModal();
        });
    },
    
    hideBlockedModal: function () {
        $('#userBlockModal').modal('hide')
    }
 };
 
 $(function(){author_show.init(App, core, {})});