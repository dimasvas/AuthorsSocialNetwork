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
//        adminUsers.activeTable = $('#example');
        
//        adminUsers.activeDataTable = adminUsers.initActiveTable();
//        adminUsers.bindPageEvent();
        
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
            url: adminUsers.Routing.user_search.replace('%23', type),
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
                    { 
                        "data": "username",
                        render: function(data, type, row){
                            return '<a href="' + adminUsers.Routing.user_profile.replace('%23', row.id) +'">'+ row.username +'</a>';
                        }
                    },
                    { "data": "surname" },
                    { "data": "name" },
                    { 
                        "data": "isAuthor" ,
                        render: function(data, type, row){
                            return data ? "<i class='fa fa-check text-green'></i>" : "";
                        }
                    },
                    { 
                        "data": "locked",
                        render: function(data, type, row){
                            return !data ? "" : "<i class='fa fa-ban text-red'></i>";
                        }
                    }
                ]
            });
    },
    
    clearSearchInput: function(activeSearch){
        activeSearch === 'Id' ? adminUsers.search_data.val("") : adminUsers.search_id.val("")
    },
    
    bindPageEvent: function() {
        adminUsers.activeTable.on( 'page.dt', function () {
            var info = adminUsers.activeDataTable.page.info();
            console.log(info);
            adminUsers.activeDataTable.ajax.url(adminUsers.prepareUrl('active', parseInt(info.page + 1))).load();
        });
        
        adminUsers.activeTable.on('xhr.dt', function ( e, settings, json, xhr ) {
            //adminUsers.activeTable()
        });
    },
    
    initActiveTable: function(){
        return adminUsers.activeTable.DataTable( {
                processing : true,
                serverSide: true,
                pageLength: 2,
             
                oLanguage: {
                        "sSearch": "Search",
                        "sZeroRecords": "No records found",
                        "sProcessing": "Processing..."
                },
                ajax: adminUsers.prepareUrl('active'),
                columns: [
                    { "data": "id" },
                    { "data": "surname" },
                    { "data": "name" },
                    { "data": "isAuthor" },
                    { "data": "locked" }
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function(data, type, full, meta){
                            return '<a href="">'+data+'</a>';
                        }
                    }
                ]
            });
        
        
    },
    
    prepareUrl: function(type, page){
        var url = adminUsers.Routing.getUsers.replace('%23type', type);
        
        return page ? url + '/' + page : url;
    }
};

$(function(){adminUsers.init(AppAdmin)});

