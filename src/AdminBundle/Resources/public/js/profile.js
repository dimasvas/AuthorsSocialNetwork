"use strict"

var adminUserProfile = {
    init: function(App){
        adminUserProfile.setup(App);
        
        adminUserProfile.bindToCheckbox();
        adminUserProfile.bindEventsTab();
    },
    
    setup: function(App){
        adminUserProfile['App'] = App;
        adminUserProfile['Routing'] = App.Routing;
        adminUserProfile['checkBox'] = $('#myonoffswitch');
        adminUserProfile['tabs'] = $('a[data-toggle="tab"]');
        adminUserProfile['compositions_table'] = $('#compositions-table');
    },
    
    bindToCheckbox: function(){
        adminUserProfile.checkBox.change(function(e){
            adminUserProfile.sendData($(this).is(':checked'), $(this).data("user"));
        });
    },
    
    bindEventsTab: function(){
        adminUserProfile.tabs.on('shown.bs.tab', function (e) {    
            if($(this).attr('id') === 'compositions-tab'){
                if (! $.fn.DataTable.isDataTable( '#compositions-table') ) {
                    adminUserProfile.compositionTabHandler(); 
                }
            }
        });  
    },
    
    compositionTabHandler: function(){
        adminUserProfile.loadCompositionTable();
    },
    
    loadCompositionTable: function(){
        adminUserProfile.compositions_table.DataTable( {
            "processing": true,
            "sAjaxDataProp" : "",
            "ajax": adminUserProfile.Routing.get_author_compositions.replace('%23', adminUserProfile.App.Data.author_id),
            columns: [
                { "data": "id" },
                { 
                    "data": "title",
                    render: function(data, type, row){
                        return '<a href="' + adminUserProfile.Routing.show_author_composition.replace('%23', row.id) +'" target="_blank">'+ row.title +'</a>';
                    }
                }
            ]
        });
    },
    
    sendData: function(checked, user_id){
        $.ajax({
            type: "POST",
            url: adminUserProfile.Routing.user_block.replace('%23', user_id),
            data: {blocked: checked},
            success: function(data){
                 //adminUsers.buildSearchTable(data);
            },
            error: function(msg){
                
            }
        });
    }
};

$(function(){adminUserProfile.init(AppAdmin)});

