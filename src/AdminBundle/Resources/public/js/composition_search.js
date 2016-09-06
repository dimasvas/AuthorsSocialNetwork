var compositionSearch = {
    init: function(App) {
        compositionSearch.setup(App);
    },
        
    setup: function(App){        
        compositionSearch['App'] = App;
        compositionSearch['Routing'] = App.Routing;
        compositionSearch['search_id'] = $('#search-by-id');
        compositionSearch['search_data'] = $('#search-by-data');
        compositionSearch['data_min_length'] = 3;
        compositionSearch.searchTable = $('#search');
        
        compositionSearch.bindSearchIdEvent();
        compositionSearch.bindSearchDataEvent();
    },
    
    bindSearchIdEvent: function(){
        compositionSearch.search_id.keyup(function(e){
            compositionSearch.clearSearchInput('Id');
            compositionSearch.clearDatatable();
            compositionSearch.checkData('id', $(this).val());
        });
    },
    
    bindSearchDataEvent: function(){
        compositionSearch.search_data.keyup(function(e){
            compositionSearch.clearSearchInput('Data');
            compositionSearch.clearDatatable();
            compositionSearch.checkData('data', $(this).val());
        });
    },
    
    checkData: function(type, string){
        
        if(string.length < 1){return;}
        
        if(type === 'data' && string.length < compositionSearch.data_min_length){
            return;
        }
        
        if(type === 'id' && isNaN(string)){
            return;
        }

        compositionSearch.getData(type, string);
    },
    
    getData: function(type, string){
        $.ajax({
            type: "POST",
            url: compositionSearch.Routing.composition_search.replace('%23', type),
            data: {data:string},
            success: function(data){
                compositionSearch.buildSearchTable(data);
            },
        });

    },
    
    clearDatatable: function(){
        if ($.fn.DataTable.isDataTable('#search')) {
            compositionSearch.searchTable.dataTable().fnClearTable();
            compositionSearch.searchTable.dataTable().fnDestroy();
        }
        
    },
    
    buildSearchTable: function(dataSet){
            compositionSearch.searchTable.DataTable( {
                data: dataSet.data,
                destroy: true,
                "sAjaxDataProp" : "",
                columns: [
                    { "data": "id" },
                    { 
                        "data": "title",
                        render: function(data, type, row){
                            return '<a href="' + compositionSearch.Routing.show_author_composition.replace('%23', row.id) +'">'+ row.title +'</a>';
                        }
                    },
                    { 
                        "data": "user.name",
                        render: function(data, type, row){
                            return row.user.name + ' ' + row.user.surname;
                        }
                    },
//                    { "data": "genre.name" },
                    { "data": "published"},
                    { 
                        "data": "blocked",
                        render: function(data, type, row){
                            return !data ? "" : "<i class='fa fa-ban text-red'></i>";
                        }
                    }
                ]
            });
    },
    
    clearSearchInput: function(activeSearch){
        activeSearch === 'Id' ? compositionSearch.search_data.val("") : compositionSearch.search_id.val("")
    },
    
    prepareUrl: function(type, page){
        var url = compositionSearch.Routing.getUsers.replace('%23type', type);
        
        return page ? url + '/' + page : url;
    }
};

$(function(){compositionSearch.init(AppAdmin)});

