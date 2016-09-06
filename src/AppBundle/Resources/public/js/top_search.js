var topSearch = {
    init: function (App, core) {
        topSearch.setup(App, core);
        topSearch.bindEvent();
    },
    
    setup: function (App, core) {
        
        topSearch.App = App;
        topSearch.core = core;
        topSearch.timeout = null;
        
        topSearch.dom = {
            resultTable: $('#search-result-table'),
            searchInput: $('#top-search-input'),
            resultContainer: $('#results-container')
        };
        
        topSearch.defaults = {
            minLength: 3,
            delay: 500
        };
    },
    
    bindEvent: function () {
        
        $('body').click(topSearch.hideResultTable);
        
        $(topSearch.dom.resultContainer, topSearch.dom.searchInput).click(function(e){
            e.stopPropagation();
        });
        
        $(topSearch.dom.searchInput).keyup(topSearch.onSearch);
    },
    
    hideResultTable: function (e) {
        topSearch.core.hideElement($(topSearch.dom.resultContainer));
    },
    
    onSearch: function (e) {
        e.preventDefault();
        
        var value = topSearch.validateInput(e, $(e.target).val());

        if(!value) {
            //check if empty
            topSearch.clearResults();
            return;
        }
        
        if(!topSearch.validateKeyCode(e)){
            return;
        }

        topSearch.clearTimeOut();

        topSearch.core.hideElement(topSearch.dom.resultContainer);
        
        topSearch.showLoader();
        
        topSearch.sendRequest(value);
    },  
    
    sendRequest: function (string) {
        /* Some some timeout to prevent dos effect ))*/
        topSearch.timeout = setTimeout(function() {
            topSearch.timeout = null;  

            send({
                method: "GET",
                url:  Routing.generate('top_search', {string: string})
            }, function (response){
                topSearch.hideLoader();
                topSearch.onResponseSuccess(response);
            });

        }, topSearch.defaults.delay);
    },
    
    onResponseSuccess: function(response) {
        
        topSearch.clearResults();
        
        topSearch.buildResponseDom(response.data);
        
        if(response.data.collection.length > 0) {
            topSearch.core.showElement(topSearch.dom.resultContainer);
        }
    },
    
    buildResponseDom: function (data) {
        
        var row = topSearch.getResponseDom(),
            tbody = $('<tbody></tbody>');

        $.each(data.collection, function (index, item) {
           var clonned = $(row).clone(); 
           
           $('a', clonned).text(item.title);
           $('a', clonned).attr('href', Routing.generate('composition_show', {id: item.id}));
          
           $(tbody).append(clonned);
        });
        
        $(topSearch.dom.resultTable).append(tbody);
    },
    
    getResponseDom: function () {
        return $.parseHTML('<tr><td><a href=""></a></td></tr>');
    },
    
    showLoader: function () {
       $(topSearch.dom.searchInput).addClass('search-loader-class');
    },
    
    clearTimeOut: function (e) {
        if (topSearch.timeout !== null) {
                clearTimeout(topSearch.timeout);
        }
    },
    
    clearResults: function () {
        if($('tbody', topSearch.dom.resultTable).length > 0) {
            $('tbody', topSearch.dom.resultTable).remove();
        }
    },
    
    hideLoader: function () {
        $(topSearch.dom.searchInput).removeClass('search-loader-class');
    },
    
    validateKeyCode: function(e) {
        if(!topSearch.isAllowedKeyCode(e.keyCode)) {
            if(topSearch.isNavKeyCode(e.keyCode)){
                topSearch.navigate(e.keyCode);
            }
            return false;
        }
        
        return true;
    },
    
    validateInput: function (e, text) {
        var result = topSearch.validateData(text);
        
        if(!result) {
            return false;
        }
        
        return result;
    },
    
    validateData: function (data) {
        data = data.trim();
        
        if(data.length < topSearch.defaults.minLength) {
            return false;
        }
        
        return data;
    },
    
    isAllowedKeyCode: function(code){
        var allowed_types = [
            8, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67,
            68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 
            82, 83, 84, 85, 86, 87, 88, 89, 90,96, 97, 98, 99, 100, 
            101, 102, 103, 104, 105, 189
        ];
        
        return !($.inArray(code, allowed_types) === -1);
    },
    
    isNavKeyCode: function (code) {
        return !($.inArray(code, [37, 38, 39, 40]) === -1);
    }
};

$(function(){topSearch.init(App, core, {})});
