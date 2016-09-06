var adminUsers = {
    init: function(App) {
        adminUsers.setup(App);
    },
        
    setup: function(App){        
        adminUsers['App'] = App;
        adminUsers['Routing'] = App.Routing;
        adminUsers['search_id'] = $('#search-by-id');
        adminUsers['search_data'] = $('#search-by-data');
        adminUsers['data_min_length'] = 3;
        adminUsers.searchTable = $('#search');

        adminUsers.bindSearchIdEvent();
        adminUsers.bindSearchDataEvent();
    },
    
    bindSearchIdEvent: function(){
        adminUsers.search_id.keyup(function(e){
            adminUsers.clearSearchInput('Id');
            adminUsers.clearDatatable();
            adminUsers.checkData('id', $(this).val());
        });
    },
    
    bindSearchDataEvent: function(){
        adminUsers.search_data.keyup(function(e){
            adminUsers.clearSearchInput('Data');
            adminUsers.clearDatatable();
            adminUsers.checkData('data', $(this).val());
        });
    },
    
    checkData: function(type, string){
        
        if(string.length < 1){return;}
        
        if(type === 'data' && string.length < adminUsers.data_min_length){
            return;
        }
        
        if(type === 'id' && isNaN(string)){
            return;
        }

        adminUsers.getData(type, string);
    },
    
    getData: function(type, string){
        $.ajax({
            type: "POST",
            url: adminUsers.prepareUrl(adminUsers.Routing.user_search, type),
            data: {data:string},
            success: function(data){
                adminUsers.buildSearchTable(data);
            },
        });
    },
    
    clearDatatable: function(){
        if ($.fn.DataTable.isDataTable('#search')) {
            adminUsers.searchTable.dataTable().fnClearTable();
            adminUsers.searchTable.dataTable().fnDestroy();
        }
    },
    
    buildSearchTable: function(dataSet){
            adminUsers.searchTable.DataTable( {
                data: dataSet.data,
                destroy: true,
                columns: [
                    { "data": "id" },
                    { "data": "username" },
                    { "data": "surname" },
                    { "data": "name" },
                    { "data": "isAuthor" },
                    { "data": "locked" }
                ],
            });
    },
    
    clearSearchInput: function(activeSearch){
        activeSearch === 'Id' ? adminUsers.search_data.val("") : adminUsers.search_id.val("")
    },
    
    prepareUrl: function(url, type, page){
        var url = url.replace('%23', type);
        return page ? url + '/' + page : url;
    }
};

$(function(){adminUsers.init(AppAdmin)});

