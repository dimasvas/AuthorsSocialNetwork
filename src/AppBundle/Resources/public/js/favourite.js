var favourite = {
    init: function(App, core) {
        favourite.setup(App, core);
        favourite.bindEvent();
    },
    
    setup: function(App, core) {
        favourite.App =App;
        favourite.core = core;
        
        favourite.dom = {
            deleteBtn: $(".remove-item")
        };
        
        favourite.route = {
            deleteItem: ''
        };
    },
    
    bindEvent: function() {
        //Remove    
        $(favourite.dom.deleteBtn).click(favourite.removeItem);
    },    
    
    removeItem: function(e) {
        e.preventDefault();
        
        var favourite = parseInt($(this).attr('data-comp-id'), 10),
                self = e.target;
        
         send({
                method: "DELETE",
                url: Routing.generate('favourite_delete', {id: favourite})
            }, function (msg){
                if (msg.status === 'success') {
                    $(self).closest('tr').remove();
                }
            });
    },
    
    removeSuccess: function (e) {}
};

$(function(){favourite.init(App, core, {})});
