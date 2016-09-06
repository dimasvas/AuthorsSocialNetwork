var searchPage = {
    
    init: function (App, core) {
       searchPage.setup(App, core); 
       searchPage.bindEvent();
    },
    
    setup: function(App, core) {
        
        searchPage.App = App;
        searchPage.core = core;
        
        searchPage.dom = {
            categorySelect: $('#search_category'),
            genreWrapper: $('#genre-wrapper'),
            genreSelect: $('#genre-select-field'),
            tagContainer: $('#tags-container'),
            languageSelect: $('#language_language'),
            statusSelect: $('#composition_status_language'),
            submitBtn: $('#search-submit'),
            searchInput: $('#search-text'),
            noResults: $('#no-results'),
            resultTemplate: $('#result-template'),
            paginationWrapper: $('.pagination-wrapper')
        };
        
        searchPage.pagination = paginationHandler;
        searchPage.pagination.init({
            wrapper: searchPage.dom.paginationWrapper
        });
    },
    
    bindEvent: function () {
        //Category select input
        $(searchPage.dom.categorySelect).change(searchPage.onCategorySelect);
        
        $(searchPage.dom.languageSelect).change(searchPage.onLanguageSelect);
        
        $(searchPage.dom.statusSelect).change(searchPage.onStatusSelect);
        
        $(searchPage.dom.genreSelect).change(searchPage.onGenreSelect);
        
        $('#tags-container').on('click', 'a.tag-i', function(e) {
            searchPage.removeTag(e);
        });
        
        // Search on click and submit
        $(searchPage.dom.submitBtn).click(searchPage.submitForm);
        $(searchPage.dom.searchInput).keypress(function (e) {
            if (e.which === 13) {
                searchPage.submitForm(e);
            }
        });
    },
    
    submitForm: function (e, page) {
        e.preventDefault();
        
        var text = $(searchPage.dom.searchInput).val();
       
        if(!text) {
            //TODO: Add validation
            return;
        }
        
        searchPage.core.hideElement(searchPage.dom.noResults);
        
        searchPage.hideExtended();
        
        searchPage.core.showElement($('.loading-indicator'));
        
        send({
                method: "GET",
                url:  searchPage.buildSearchUrl(page)
            }, function (response){
                searchPage.core.hideElement($('.loading-indicator'));
                searchPage.onSubmitSuccess(response.data, page);
            });
    },
    
    onSubmitSuccess: function (data, page) {
        
        searchPage.clearSearchResults();
        
        if(data.collection.length < 1) {
            searchPage.core.showElement(searchPage.dom.noResults);
            
            return;
        }
       
        searchPage.buildResultsDom(data.collection);
       
       /* Rebuild only on new search criteria.*/
       if(!page) {
            searchPage.pagination.buildPager(data.pagination, searchPage.onPageClick);
       }
    },
    
    onPageClick: function (event, page) {
        searchPage.submitForm(event, page);
    },
    
    onCategorySelect: function (e) {
        
        var value = $(e.target).find(":selected").val(),
            text = $(e.target).find(":selected").text();        
        
        searchPage.resetTag('category');
        searchPage.resetTag('genre');
        
        if(!value) {
            searchPage.hideGenreContainer();
            searchPage.emptyGenreOptions();
             
            return;
        }
    
        searchPage.setTag('category', value, text);
        
        //TODO: Make cache. Store all genre data
        send({
                method: "GET",
                url:  Routing.generate('list_composition_genres', {id: value})
            }, function (response){
                searchPage.emptyGenreOptions();
                searchPage.onCategorySuccess(response.data);
            });
    },
    
    onCategorySuccess: function(data) {
        $.each(data, function (index, item){
            var option = $('<option/>');
            
            if(index === 0) {
                 $(option).attr({ 'value': '' }).text('Все');
            } else {              
                $(option).attr({ 'value': item.id }).text(item.name);
            }
            
            $(searchPage.dom.genreSelect).append(option);
        });
        
        $(searchPage.dom.genreWrapper).removeClass('hidden');
    },
    
    onGenreSelect: function (e) {
        var value = $(e.target).find(":selected").val(),
            text = $(e.target).find(":selected").text();
    
        searchPage.resetTag('genre');
        
        if (!value) {
            return;
        }
        
        searchPage.setTag('genre', value, text);
    },
    
    onLanguageSelect: function (e) {
        var value = $(e.target).find(":selected").val(),
            text = $(e.target).find(":selected").text();  
    
        searchPage.resetTag('language');
        
        if (!value) {
            return;
        }
        
        searchPage.setTag('language', value, text);
    },
    
    onStatusSelect: function (e) {
         var value = $(e.target).find(":selected").val(),
            text = $(e.target).find(":selected").text();  
    
        searchPage.resetTag('status');
        
        if (!value) {
            return;
        }
        
        searchPage.setTag('status', value, text);
    },
    
    emptyGenreOptions: function () {
        $(searchPage.dom.genreSelect).empty();
    },
    
    hideGenreContainer: function () {
         $(searchPage.dom.genreWrapper).addClass('hidden');
    },
    
    getTagDom: function () {
        return $.parseHTML('<div class="tag"><span class="asterisk">#</span><span class="text">sdssdas</span><a role="button" class="tag-i">×</a></div>');
    },
    
    setTag: function (type, value, text) {
        var tag = searchPage.getTagDom();
        
        $(tag).data('type', type);
        $(tag).data('id', value);
        $('.text', tag).text(text);
        
        $('#'+type+'-tag', searchPage.dom.tagContainer).append(tag);
    },
    
    resetTag: function (type) {
        $('#'+type+'-tag', searchPage.dom.tagContainer).empty();
    },
    
    removeTag: function (e) {
        var tag = $(e.target).closest('.tag');
        
        searchPage.resetSelect(tag);
        $(tag).remove();
    },
    
    rebuildSelect: function (tag) {
        var type = $(tag).data('type'),
            value =  $(tag).data('id'); 
    },
    
    resetSelect: function (tag) {
        var type = $(tag).data('type');
        
        switch (type) {
            case 'category':
                searchPage.resetCategory();
                searchPage.resetGenre();
                
                searchPage.hideGenreContainer();
                searchPage.emptyGenreOptions();
                break;
            case 'language':
                searchPage.resetLanguage();
                break;
            case 'status':
                searchPage.resetStatus();
                break; 
            case 'genre':
                searchPage.resetGenre();
                break;  
            default:
                throw new Error('Wrong Type set to Tag.');
        }
    
    },
    
    resetCategory: function () {
        $(searchPage.dom.categorySelect)
                .val($("option:first", searchPage.dom.categorySelect).val());
    },
    
    resetGenre: function () {
         $(searchPage.dom.genreSelect)
                .val($("option:first", searchPage.dom.genreSelect).val());
    },
    
    resetLanguage: function () {
        $(searchPage.dom.languageSelect)
                .val($("option:first", searchPage.dom.languageSelect).val());
    },
    
    resetStatus: function () {
        $(searchPage.dom.statusSelect)
                .val($("option:first", searchPage.dom.statusSelect).val());
    },
    
    buildRequestParams: function () {
        
        var category = $(searchPage.dom.categorySelect).val(),
            genre = $(searchPage.dom.genreSelect).val(),
            language = $(searchPage.dom.languageSelect).val(),
            status = $(searchPage.dom.statusSelect).val(),
            text = $(searchPage.dom.searchInput).val(),
            param_array = [];
    
        if(text) {
            param_array.push({name: 'text', value: text});
        }
        
        if(category) {
            param_array.push({name: 'category', value: category });
            
            if(genre) {
                param_array.push({name: 'genre', value: genre });
            }
        }
        
        if(language) {
            param_array.push({name: 'language', value: language});
        }
        
        if(status) {
            param_array.push({name: 'status', value: status });
        }
        
        return $.param(param_array);
    },
    
    buildSearchUrl: function (page) {
        page = page ? page : 1;
        
        var url = Routing.generate('search_results', {page: page});
         
        return url + '?' + searchPage.buildRequestParams();
    },
    
    buildResultsDom: function (data) {
        var template = searchPage.getResultsDom(),
            wrapper = $('<div/>');
    
        $.each(data, function(index, item){
            var clonned = $(template).clone(),
                composition_url = Routing.generate('composition_show', {id: item.id}),
                author_url = Routing.generate('author_show', {id: item.user.id});

            $('.media-left a', clonned).attr("href", composition_url);
            $('.media-heading a', clonned).attr("href", composition_url);
            $('.media-heading a', clonned).text(item.title);
             
            $('#author a', clonned).text(item.user.name + ' ' + item.user.surname);
            $('#author a', clonned).attr("href", author_url );
             
            $('#rate', clonned).text(item.total_rating_num + '/' + item.num_users_rate);
            
            $('#type', clonned).text(item.category.name);
            
            $('#status', clonned).text(Translator.trans('composition_status.' + item.status));
             
            if(item.status === 'in_process') {
                $('#status', clonned).addClass('in-process');
            } else if (item.status === 'finished') {
                 $('#status', clonned).addClass('finished');
            }
             
            if(item.image_name) {
                 $('.media-object', clonned).attr('src', searchPage.core.path.composition_cover + item.image_name);
            }

            $(wrapper).append(clonned);
        });
        
        $('#result-wrapper').append(wrapper);
        
    },
    
    getResultsDom: function () {
        var template = $(searchPage.dom.resultTemplate).clone();
        
        return $(template).removeClass('hidden').removeAttr('id');
    },
    
    buildPager: function (pagination) {
        
        searchPage.resetPagination();
        
        if(!pagination.haveToPaginate) { 
            return;
        }
        
        $(searchPage.dom.paginationWrapper).twbsPagination({
            totalPages: pagination.totalPages,
            visiblePages: pagination.visiblePages,
            first: 'Первый',
            prev: 'Пред.',
            next: 'След.',
            last: 'Последний',
            onPageClick: function (event, page) {
                //$('#page-content').text('Page ' + page);
            }
        });
    },
    
    clearSearchResults: function () {
        $('#result-wrapper').empty();
    },
    
    resetPagination: function () {
        if($(searchPage.dom.paginationWrapper).data("twbs-pagination")) {
            $(searchPage.dom.paginationWrapper).twbsPagination('destroy');
        }

        $(searchPage.dom.paginationWrapper).empty();
    },
    
    hideExtended: function () {
        $('#collapse-extended').removeClass('in');
    }
};

$(function(){searchPage.init(App, core, {})});