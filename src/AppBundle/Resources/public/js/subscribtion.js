var subscribtions = {
    init: function (App, core) {
        subscribtions.setup(App,core);
        subscribtions.bindEvents();
        subscribtions.getUpdates();
    },
    
    setup: function (App, core) {
        
        this.App = App;
        this.core = core;
        
        this.dom = {
            notif_pager: $('#notifications-pagination-conatainer'),
            notif_tbody: $('#notifications table tbody'),
            notif_noData: $('#notification-no-data'),
            notif_table: $('#notifications table'),
            subs_pager: $('#subscribtion-pagination-conatainer'),
            subs_tbody: $('#subscribtions table tbody')
        };
        
        this.route = {
            getUpadates: Routing.generate('composition-update-list'),
            getSubscribtions: Routing.generate('subscribtion_list'),
            delete_notif: '',
            get_subs: '',
            delete_subs: ''
        };
        
        this.path = {
            default_comp_img: '/media/composition/default/no_book_cover.gif',
            comp_thumbs: '/media/composition/default/composition_thumb.png'
        };
        
        this.text = {
            deleteItem: Translator.trans('common.delete'),
            viewed: Translator.trans('common.viewed')
        };
        
    },
    
    bindEvents: function () {
        /**
        * Events handlers
        */
       $('#update-tab').click(function (e) {
      
       });
       
       $('#subscribtion-tab').click(function (e) {
            subscribtions.core.hideElement($('#subscribtion-no-data'));
            subscribtions.getSubscribtions();
       });

       $('body').on('click', '.delete-update-btn', function(e){
           e.preventDefault();

           subscribtions.deleteEntity(
                    e.target, 
                    Routing.generate('composition_update_delete', {'id': $(e.target).data('id')} )
            );
       });

       $('body').on('click', '.delete-subscribtion-btn', function(e){
           e.preventDefault();

           subscribtions.deleteEntity(
                    e.target, 
                    Routing.generate('subscribtion_delete', {'id': $(e.target).data('id')} )
            );
       });

       $('body').on('click', 'ul.pagination.notifications a', function(e){
           e.preventDefault();
           subscribtions.getUpdates($(this).attr('href'));
       });

       $('body').on('click', 'ul.pagination.subscribtions a', function(e){
           e.preventDefault();
           subscribtions.getSubscribtions($(this).attr('href'));
       });
    },
    
    getUpdates: function (url) {
        send({
            method: "GET",
            url: url ? url : subscribtions.route.getUpadates,
        }, function (data){
            subscribtions.core.emptyElement(subscribtions.dom.notif_tbody);
            subscribtions.core.emptyElement(subscribtions.dom.notif_pager);
            
            subscribtions.buildUpdatesDom(data.entities);
            subscribtions.buildPaginationContainer(subscribtions.dom.notif_pager, data.pager, 'notifications');
        });
    },
    
    getSubscribtions: function (url) {

        send({
            method: "GET",
            url: url ? url :  subscribtions.route.getSubscribtions,
        }, function (data){
            subscribtions.core.emptyElement(subscribtions.dom.subs_tbody);
            subscribtions.core.emptyElement(subscribtions.dom.subs_pager);
            
            subscribtions.buildSubscribtionsDom(data.entities);
            subscribtions.buildPaginationContainer(subscribtions.dom.subs_pager, data.pager, 'subscribtions');
        });
    },
    
    buildUpdatesDom: function (collection) {
          
        if(collection.length < 1) {
            subscribtions.core.showElement(subscribtions.dom.notif_noData);
            return;
        }
        
        var table = $(subscribtions.dom.notif_table),
            tbody = $('tbody', table),    
            row = subscribtions.getUpdateRow();
        
        $.each(collection, function(index, entity){
            
            var clonned_row = $(row).clone();
            
            $('.author_name', clonned_row).text(entity.author_name);
            $('.composition_name a', clonned_row).text(entity.composition_name);
            $('.message', clonned_row).text(entity.message);
            $('.composition_name a', clonned_row).attr('href', Routing.generate('composition_show', {'id': entity.composition_id}));
            $('.delete-update-btn', clonned_row).data('id', entity.id);
            //Optimize
            tbody.append(clonned_row);
        });
        
        subscribtions.core.showElement(table);
    },
    
    buildSubscribtionsDom: function (collection) {
        
        if(collection.length < 1) {
            subscribtions.core.showElement($('#subscribtion-no-data'));
            return;
        }
        
        var table = $('#subscribtions table'),
            tbody = $('tbody', table),    
            row = subscribtions.getSubscribtionRow();
        
        $.each(collection, function(index, entity){

            var clonned_row = $(row).clone(),
                popover_img = entity.composition_img ? entity.composition_img : subscribtions.path.default_comp_img;
            
            $('.title_link', clonned_row).text(entity.composition_title);
            $('.popover-link', clonned_row).attr('data-content', '<img class="composition-image-popover" src="' + popover_img + '"></img>');
            $('.title_link', clonned_row).attr('href', Routing.generate('composition_show', {'id': entity.composition_id}));
            $('.comment', clonned_row).text(entity.comment ? entity.comment : '');
            $('.delete-subscribtion-btn', clonned_row).data('id', entity.id);
            $('.composition-thumb', clonned_row).attr('src',  subscribtions.path.comp_thumbs);
            //TODO: Optimize
            tbody.append(clonned_row);
        });
        
        subscribtions.core.showElement(table);
    },
    
    getUpdateRow: function () {
               return $.parseHTML('<tr>' +
                                '<td class="composition_name">' +
                                    '<a href=""></a>' +
                                '</td>' +
                                '<td class="author_name"></td>' +
                                '<td class="message"></td>' +
                                '<td class="action">' 
                                    +'<button type="button" class="btn btn-default btn-xs delete-update-btn">'  + subscribtions.text.viewed + '</button>' +
                                '</td>' +
                            '</tr>');
    },
    
    getSubscribtionRow: function () {
         return $.parseHTML('<tr>' +
                                '<td class="composition_title">' +
                                    '<a href="" class="title_link"></a>' + 
                                    '<a href="#" data-toggle="popover" data-trigger="hover" data-html="true" data-content="" class="popover-link">' +
                                        '<img class="composition-thumb" src=""></img>' +
                                    '</a>' +
                                '</td>' +
                                '<td class="action">' +
                                    '<button type="button" class="btn btn-default btn-xs delete-subscribtion-btn">' + subscribtions.text.deleteItem +'</button>' +
                                '</td>' +
                            '</tr>');
    },
    
    deleteEntity: function (container, url) {
        send({
            method: "DELETE",
            type: 'delete',
            url:  url
        }, function (response){
            subscribtions.successDelete(container);
        });
    },
    
    successDelete: function (container) {
        var row = container.closest('tr'),
            table = container.closest('table');
        
        row.remove();
        
        if($('tbody tr', table).length < 1) {
            $(table).addClass('hidden');
            $(table).next().addClass('hidden');
            $(table).prev('#subscribtion-no-data').removeClass('hidden');
        }
        
    },
    
    buildPaginationContainer: function (container, html_pager, element_class) {
        
        if(!html_pager) {
            return;
        }
        
        var pager = $.parseHTML(html_pager);
        
        $('ul', pager).addClass('pagination');
        $('ul', pager).addClass(element_class);
        
        container.append(pager);
    }
};

$(function(){subscribtions.init(App, core, {})});