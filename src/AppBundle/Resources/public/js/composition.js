/**************************
 * Composition Pages Script
 **************************/

var showComposition = {
    
    init: function(App, core, settings) {
        this.setup(App, core);
        this.setDefaultEditMode();
        this.initInlineEditor();
        this.bindEvents();
    },
    
    setup: function(App, core){
        showComposition.core = core;
        
        showComposition.isUserAuth = core.getBoolean(App.isUserAuth);
        
        showComposition.isVisible = core.getBoolean(App.entityVisible);
        
        showComposition.dom = {
            titleInput: $('#edit-title'),
            descriptionTextarea: $('#edit-description'),
            editLanguage: $('#edit-language'),
            statusSelect: $('#edit-status'),
            editGenres: $('#edit-genres'),
            visibilityInput: $('#edit-visibility'),
            videoContentInput: $('#edit-video-content'),
            
            title: $('#composition-title'),
            description: $('#description-field'),
            genre: $('#genre-field'),
            
            imageContainer: $('#image-wrapper'),
            
            favouriteBtn: $('#to-favourite'),
            subscribeBtn: $('#subscribe')
        };
        
        showComposition.rules = {
            maxTitle: 100,
            minTitle: 2,
            maxDescription: 1000,
            maxVideo: 100,
        };
        
        showComposition.errorsMsg = {

        };
        
        showComposition.text = {
            empty: Translator.trans('common.empty'),
            rate: Translator.trans('common.rate')
        };
        
        showComposition.route = {
            edit: Routing.generate('composition_update', {id : App.data.composition}),
            add_to_favourite: Routing.generate('favourite_create', {id : App.data.composition}),
            add_to_subscribe: Routing.generate('subscribtion_create', {id : App.data.composition}),
            rating_create: Routing.generate('rating_create', {id : App.data.composition}),
            send_update: Routing.generate('comp_subscriptions_update', {id : App.data.composition})
        };
        
        showComposition.paramName = {
            title: 'title',
            description: 'description',
            language: 'language',
            genres: 'genres',
            status: 'status',
            published: 'published',
            videoContent: 'video_content'
        };
        
        showComposition.errorsMsg = {
            server: Translator.trans('system.errors.imposible_action'),
            bigTitle: Translator.trans('system.errors.big_title'),
            smallTitle: Translator.trans('system.errors.small_title'),
            bigText: Translator.trans('system.errors.big_text'),
            wrongValue: Translator.trans('system.errors.wrong_value'),
            genreRequired: Translator.trans('system.errors.genre_required'),
            lengthExceeded: Translator.trans('system.errors.length_exceeded'),
        };
        
        showComposition.statuslist = [
            'finished', 'in_process', 'freezed'  
        ];
    },
    
    bindEvents: function () {
        // To Favourite click event
       // $(showComposition.dom.favouriteBtn).click(showComposition.addToFavourite);
        //Subscribe click event
        $(showComposition.dom.subscribeBtn).click(showComposition.addSubscribtion);
        
        //Prevent default on click    
        $('body').on('click', '.in-favourite, .subscribed', function(e){
            e.preventDefault();
        });
        
        showComposition.raitingInit();
        
        $('.favourite-link').popover({
            html: true
        });
        
        $('body').on('click', '.pop-cancel-favourite-btn', showComposition.dismissPopover);
        
        $('body').on('click', '.pop-send-favourite-btn', showComposition.addToFavourite);
    },
    
    setDefaultEditMode: function(){
        $.fn.editable.defaults.mode = 'inline';
    },
    
    raitingInit: function () {
        $('.rating-tooltip').rating({
            extendSymbol: function (rate) {
            $(this).tooltip({
                container: 'body',
                placement: 'bottom',
                title: showComposition.text.rate + ' ' + rate
            });
          }
        });
    
        $('.rating-tooltip').on('change', function () {
            var rate = $(this).val();
            showComposition.sendRated(rate, showComposition.ratingSuccess);
        });
    },
    
    dismissPopover: function (e) {
        var popover = $(e.target).closest('.popover');
        
        rv.clearTextarea(popover);
        //Fix bug in boostrap 3.3.5 , 3.3.6. With double click to show after hide
        popover.data("bs.popover").inState.click = false;
        
        $(popover).popover('hide');
    },
    
    sendRated: function(rate, callback) {
        send({
            method: "POST",
            url: showComposition.route.rating_create,
            data: {rate: rate}
        }, callback);
    },
    
    ratingSuccess: function (data) {
        $('.user-rate').text(data.rate);
        
        $('.rate-users').text(data.rate_data.hits);
        $('.rate-number').text(data.rate_data.total_rating);
    },
    
    initInlineEditor: function(){
        showComposition.dom.titleInput.editable({
            type: 'text',
            send : 'always',
            name: showComposition.paramName.title,
            url : showComposition.route.edit,
            validate: function(value){
                if(value.length > showComposition.rules.maxTitle) {
                    return showComposition.errorsMsg.bigTitle;
                }
                
                if(value.length < showComposition.rules.minTitle) {
                    console.log(value.length);
                    return showComposition.errorsMsg.smallTitle;
                }
            },
            error: function (response, newValue){
                return showComposition.errorsMsg.server;
            },
            success: function(response, newValue) {
                if(response.status === 'success') {
                   showComposition.updateTitle(newValue);
                }
            }
        });
        
        showComposition.dom.descriptionTextarea.editable({
            type: 'textarea',
            emptytext: showComposition.text.empty,
            url : showComposition.route.edit,
            send : 'always',
            name: showComposition.paramName.description,
            validate: function(value){
                if(value.length > showComposition.rules.maxDescription) {
                    return showComposition.errorsMsg.bigText;
                }
            },
            error: function (response, newValue){
                return showComposition.errorsMsg.server;
            },
            success: function(response, newValue) {
                if(response.status === 'success') {
                   showComposition.updateDescription(newValue);
                }
            }
        }); 
        
        showComposition.dom.editLanguage.editable({
            url : showComposition.route.edit,
            send : 'always',
            name: showComposition.paramName.language,
            error: function (response, newValue) {
                return showComposition.errorsMsg.server;
            }
        });
        
        showComposition.dom.visibilityInput.editable({
            url : showComposition.route.edit,
            send : 'always',
            name: showComposition.paramName.published,
            error: function (response, newValue) {
                return showComposition.errorsMsg.server;
            }
        });
        
        showComposition.dom.statusSelect.editable({
            url : showComposition.route.edit,
            send : 'always',
            name: showComposition.paramName.status,
            validate: function(value){
                if($.inArray(value, showComposition.statuslist) == -1){
                    return showComposition.errorsMsg.wrongValue;
                }
            },
            error: function (response, newValue) {
                return showComposition.errorsMsg.server;
            }
        });
        
        showComposition.dom.editGenres.editable({
            url : showComposition.route.edit,
            send : 'always',
            name: showComposition.paramName.genres,
            validate: function(value){
                if(value.length < 1) {
                    return showComposition.errorsMsg.genreRequired;
                }
                
            },
            error: function (response, newValue) {
                return showComposition.errorsMsg.server;
            },
            success: function(response, newValue) {
                if(response.status === 'success') {
                   showComposition.updateGenres(newValue);
                }
            }
        });
        
        showComposition.dom.videoContentInput.editable({
            type: 'textarea',
            send : 'always',
            name: showComposition.paramName.videoContent,
            url : showComposition.route.edit,
            validate: function(value){
                if(value.length > showComposition.rules.maxVideo) {
                    return showComposition.errorsMsg.bigVideoContent;
                }
                
                var embededUrl = showComposition.getYoutubeId(value);
                
                if(!embededUrl) {
                    return 'Неверная ссылка'
                }
                
                return {newValue: 'http://www.youtube.com/embed/' + embededUrl}

            },
            error: function (response, newValue){
                return showComposition.errorsMsg.server;
            },
            success: function(response, newValue) {
                if(response.status === 'success') {
                   showComposition.updateEmbed(newValue);
                }
            }
        });
    },
    
    updateTitle: function(value){
        $(showComposition.dom.title).text('"' + value + '"');
    },
    
    updateDescription: function(value){
        $(showComposition.dom.description).text(value);
    },
    
    updateGenres: function(values){
        var text = "",
            genres = $(showComposition.dom.editGenres).data('source'),
            genresLen = genres.length,
            newValuesLen = values.length;
        
        //Transform array valus from string to Integer
        for(var i=0; i<newValuesLen; i++) {
            values[i] = parseInt(values[i], 10);
        }
        
        $.each(genres, function(index, obj){
            if($.inArray(obj.value, values) !== -1){
                text = text.concat(obj.text);
                
                if (index !== genresLen - 1) {
                    text = text.concat(', ');
                }
            }
        });
        
        $(showComposition.dom.genre).text(text);
    },
    
    updateEmbed: function (value) {
      $('#video-iframe').attr('src', value);  
    },
    
    addToFavourite: function (e) {
        var popover = $(e.target).closest('.popover'),
            text = popover.find('textarea').val().trim(),
            id = $(e.target).data('id');
    
        e.preventDefault();
        
        send({
            method: "POST",
            url: showComposition.route.add_to_favourite,
            data: {text: text}
        }, showComposition.favouriteSuccess);
        
        popover.data("bs.popover").inState.click = false;
        
        $(popover).popover('hide');
    },
    
    favouriteSuccess: function (response) {
        $(showComposition.dom.favouriteBtn)
                .unbind( "click" ).addClass('in-favourite');
    },
    
    addSubscribtion: function (e) {
        e.preventDefault();
        
        send({
            method: "POST",
            url: showComposition.route.add_to_subscribe
        }, showComposition.subscribtionSuccess);
    },
    
    subscribtionSuccess: function () {
        $(showComposition.dom.subscribeBtn)
                .unbind( "click" ).addClass('in-favourite');
    },
    
    getYoutubeId: function(url) {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = url.match(regExp);

        if (match && match[2].length === 11) {
            return match[2];
        } else {
            return false;
        }
    }
};

$(function(){
    showComposition.init(App, core, {});
    
    croppieHandler.init(
        App, core, 
        {
            url: Routing.generate('img_update_composition', {id : App.data.composition}),
            settings: {
                viewport: { width: 200, height: 302 },
                boundary: { width: 230, height: 332},
                exif: true
            }
        }
    );
});

$(function(){
    /***************************
     * Send Composition Update
     ***************************/
    
    $('#sendUpdateBtn').click(function(e){
        e.preventDefault();
       
         send({
            method: "POST",
            url: showComposition.route.send_update,
            data: {message: $('#updateMessage').val()}
        }, function(msg){
            $('#updateMessage').val('');
            $('#sendUpdateModal').modal('hide');
            $('#updateCharNum').text(100);
        });
        
    });
    
    $('#updateMessage').keyup(function () {
        var maxLength = 100,
            currentLength = $(this).val().length,
            availableCharNum = maxLength - currentLength;
            
        if (currentLength > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
        } else {
            $('#updateCharNum').text(availableCharNum );
        }
    });
    
    /*******************************
     *  Common function
     ******************************/
    
    function send(params, callback) {
        $.ajax(params).done(function( msg ) {
            if(typeof callback  === "function") {
                callback(msg);
            }
        });
    }
});