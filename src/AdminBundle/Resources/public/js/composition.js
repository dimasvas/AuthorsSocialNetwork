"use strict"

var adminComposition = {
    init: function(App){
        adminComposition.setup(App);
        
        adminComposition.bindToCheckbox();
        adminComposition.bindEventsTab();
    },
    
    setup: function(App){
        adminComposition['App'] = App;
        adminComposition['Routing'] = App.Routing;
        adminComposition['checkBox'] = $('#myonoffswitch');
        adminComposition['tabs'] = $('a[data-toggle="tab"]');
    },
    
    bindToCheckbox: function(){
        adminComposition.checkBox.change(function(e){
            adminComposition.sendData($(this).is(':checked'), $(this).data("composition"));
        });
    },
    
    bindEventsTab: function(){
        adminComposition.tabs.on('shown.bs.tab', function (e) {    

        });  
    },
    
    sendData: function(checked, user_id){
        $.ajax({
            type: "POST",
            url: adminComposition.Routing.composition_block.replace('%23', user_id),
            data: {blocked: checked},
            success: function(data){
                 //adminUsers.buildSearchTable(data);
            },
            error: function(msg){
                
            }
        });
    }
};

$(function(){adminComposition.init(AppAdmin)});

