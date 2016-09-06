var comment = {
    init: function (App, core) {
        
        if(App.isUserAuth !== 'true') {
            return;
        }
        
        if(!App.data.isBlocked) {
            this.setup(App, core);
            this.getComments(this.routing.get_comments);
            this.bindEvent();
        }
    },

    setup: function (App, core) {
        this.App = App;
        this.core = core;
        
        this.isUserAuth = (App.isUserAuth === 'true');
        this.isCompositionVisible = core.getBoolean(App.data.entityVisible);
                
        this.data = {
            composition: App.data.composition,
            greenColor: '#3c763d', 
            redColor: '#a94442',
            greyColor: '#333'
        };    
        
        this.dom = {
            comment_loader: $('#comment-loader'),
            comment_template: $('#comment-template'),
            no_comments: $('#no-comments')
        };
        
        this.text = {
            more_comments: Translator.trans('common.more_comments'),
            cancel: Translator.trans('common.cancel'),
            respond: Translator.trans('common.respond')        
        };
        
        this.routing = {
            get_comments: Routing.generate('comments_list', {composition: comment.data.composition})
        };
    },

    bindEvent: function () {
        /** Comment submit  **/
        $('#comment-form').submit(comment.onCommentSubmit);

        /** Subcomment submit  **/
        $('body').on('submit', '.subcomment-form', comment.onSubcommentSubmit);
       
        /** Response to comment  **/
        $('body').on('click', '.response-btn', comment.onCommentResponse);
        
        /** Response to subcomment  **/
        $('body').on('click', '.response-subcomment-btn', comment.onSubcommentResponse);
       
        /** Subcomments load list **/
        $('body').on('click', '.reply-count-container', comment.getSubcomments);
        
        /* Comment pagination */
        $('body').on('click', '.pagination li', comment.getNewPage);
       
        /** Get more subcomments  **/
        $('body').on('click', '.more-comments', comment.getMoreSubcomments);
 

        /** Report rule violation  **/
        $('body').on('click', '.rule-violation', function(e){
            e.preventDefault();

        });
    },
    
    getNewPage: function (e) {
        e.preventDefault();
        var li = $(e.currentTarget);

        if(li.hasClass('disabled') || li.hasClass('active')){
            return;
        }

        comment.rollToTop();
        comment.getComments($('a', this).attr('href'));    
    },

    getComments: function(url){
        comment.get(url, function(data){
            comment.resetCommentsDom();
            comment.resetPaging();
            
            if(data.entities.length > 0) {
                comment.pagerBuilder(data.pager);   
                comment.setCommentsContainerBorder();
                comment.showNoComments();
            }
            
            comment.domBuilder(data.entities, true);
        });
    },
    
    getSubcomments: function (e) {
        e.preventDefault();

        comment.getAndBuildResponseList($(this));
    },
    
    getMoreSubcomments: function (e) {
        e.preventDefault();
        
        comment.buildMoreSubComments($(this).attr('href'), $(this));
    },
    
    onCommentSubmit: function (e) {
        e.preventDefault();

        var self = $(this),
            textarea = $('.main-comment-textarea', this),
            text = textarea.val();

        if(!comment.validateText(text)) {
            return;
        }

        comment.core.showElement(comment.dom.comment_loader);
        
        comment.disableSubmit(self, true);

        comment.post(
            Routing.generate('comment_create', {id: comment.data.composition}), 
            {text: text}, 
            function(data){
                if(data.status === 'success') {
                    comment.buildList(data.entities, false);
                    
                    comment.clearTextarea(textarea);
                    
                    comment.core.hideElement(comment.dom.comment_loader);
                    
                    comment.disableSubmit(self, false);
                    
                    if(!$(comment.dom.no_comments).hasClass('hidden')) {
                        $(comment.dom.no_comments).addClass('hidden')
                    }
                }
            }
        );
    },
    
    onSubcommentSubmit: function (e) {
        e.preventDefault();

        var self = $(this),
            textarea = $('.sub-comment-textarea', self),
            loader = $('.subcomment-loader', self),
            text = textarea.val(),
            parent = self.closest('.active-comment'),
            comment_id = parent.data('id'),
            subcomment_container =  $('.sucomment-container', parent);


        if(!comment.validateText(text)) {return;}

        comment.core.showElement(loader);

        comment.disableSubmit(self, true);

        comment.post(
            Routing.generate('subcomment_create', {composition: comment.data.composition, parent_id: comment_id}), 
            {text: text}, 
            function(data){
                if(data.status === 'success') {
                    comment.buildSubCommentList(data.entities, subcomment_container, false);

                    comment.clearTextarea(textarea);

                    comment.core.hideElement(loader);

                    comment.disableSubmit(self, false);
                }
            }
        );
    },
    
    onCommentResponse: function (e) {
        e.preventDefault();

        var btn = $(this),
            parent = btn.closest('.active-comment'),
            is_cancel = (btn.data('action') === 'cancel');


        if($('.reply-info-panel', parent).hasClass('hidden')){
            // hideResponseInputs();
            comment.clearResponseTextarea(btn);
            comment.changeResponseButtonText(btn, is_cancel);
            comment.changeResponseButtonState(btn, is_cancel);
        }else {
            var link = $('.reply-count-container', parent);

            if (link.length === 0) {
                return;
            }

            comment.getAndBuildResponseList(link);
        }
    },
    
    onSubcommentResponse: function (e) {
        e.preventDefault();

        var self = $(this),
            parent_container = self.closest('.comments-top-top'),
            textarea = $('.sub-comment-textarea', parent_container),
            parent = self.closest('.subcomment'),
            respondee_name = $('.user-name', parent).text();

        textarea.val(respondee_name + ', '); 
        textarea.focus();
    },
    
    clearTextarea: function (textarea) {
        textarea.val('');
    },
    
    disableSubmit: function (container, isDisabled) {
        $(':submit', container).prop('disabled', isDisabled);
    },
    
    getPreparedUrl: function(url, value) {
        return url.replace('%23', value);
    },
    
    get: function(url, callback){
        send({
            method: "GET",
            url: url,
        }, function (data){
            if(typeof callback === 'function') {
                callback(data);
            }
        });
    },
    
    post: function(url, data, callback) {
         send({
            method: "POST",
            url: url,
            data: data
        }, function (data){
            if(typeof callback === 'function') {
                callback(data);
            }
        });
    },
    
    send: function(params, callback) {
        $.ajax(params).done(function( msg ) {
            if(typeof callback  === "function") {
                callback(msg);
            }
        });
    },
    
    validateText: function(text) {
        if(text.length < 2) {
            return false;
        }
        
        return true;
    },
    
    domBuilder: function(collection, append) {
        if(collection.length > 0) {
            $('#no-comments').remove();
            comment.buildList(collection, append);
        }
    },
    
    resetCommentsDom: function() {
        $('.active-comment').remove();
        $('.active-page').remove();
    },
    
    resetPaging: function(){
        $('.comment-pagination-container').empty();
    },
    
    rollToTop: function() {
        var offset = $('#comment-grid').offset();
        $("html, body").animate({ scrollTop: offset.top }, "fast");
    },
    
    buildList: function(collection, append){
        var container = ($('#comments-container'));
        
        $.each(collection, function(key, entity) {
            var clonned = comment.dom.comment_template.clone();
            
            clonned.removeAttr('id');
            clonned.removeClass('hidden');
            $(clonned).addClass('active-comment');
            $(clonned).data('id', entity.id);
            
            $('.comment-id', clonned).text('#'+ entity.id);
            $('.user-name', clonned).text(entity.user.username);       
            $('.comment-date', clonned).text(dateAdapter(entity.created.date));               
            $('.comment-content', clonned).text(entity.content);              
            $('.comment-user-img', clonned).attr("src", comment.getProfileImg(entity.user.photo));            
            $('.user-profile-link', clonned).attr("href", Routing.generate('profile_show', {id: entity.user.id}));
            $('.vote-counter', clonned).text(comment.getSignNumber(entity.totalVote));
            $('.vote-counter', clonned).css('color', comment.getCounterColor(entity.totalVote));
            
            if(entity.has_response){
                $('.reply-info-panel', clonned).removeClass('hidden');
                $('.response-count', clonned).text(entity.responses_count);
                $('.reply-count-container', clonned).attr('href', entity.get_responses_url)
            }

            //TODO Dimas Вытащить отсюда эту проверку. В цикле каждый раз проверяется то что меняться не будет.
            append ? comment.appendToContainer(container, clonned): comment.prependToContainer(container, clonned);
        });
    },
    
    buildSubCommentList: function(collection, container, append){
        var template = $('.subcomment-template');

        $.each(collection, function(key, entity ) {
            var clonned = template.clone();

            $(clonned).addClass('active-comment');
            $(clonned).data('id', entity.id);
            clonned.removeClass('hidden subcomment-template');
            
            $('.comment-id', clonned).text('#'+ entity.id);
            $('.user-name', clonned).text(entity.user.username);       
            $('.comment-date', clonned).text(dateAdapter(entity.created.date));               
            $('.comment-content', clonned).text(entity.content);              
            $('.comment-user-img', clonned).attr("src", comment.getProfileImg(entity.user.photo));            
            $('.user-profile-link', clonned).attr("href", Routing.generate('profile_show', {id: entity.user.id}));
            $('.vote-counter', clonned).text(comment.getSignNumber(entity.totalVote));
            $('.vote-counter', clonned).css('color', comment.getCounterColor(entity.totalVote));

            //TODO Dimas Вытащить отсюда эту проверку. В цикле каждый раз проверяется то что меняться не будет.
            append ? comment.appendToContainer(container, clonned): comment.prependToContainer(container, clonned);
        });
    },
    
    getProfileImg: function (img) {
        return img ? comment.core.path.user_img + img : comment.core.path.no_user_img;
    },
    
    getAndBuildResponseList: function(button) {
        comment.get($(button).attr('href'), function(data){
            var parent = button.closest('.active-comment'),
            subcomment_container =  $('.sucomment-container', parent),
            resonse_input = $('.response-to-comment', parent);
    
            resonse_input.removeClass('hidden');
           
            comment.buildSubCommentList(data.entities, subcomment_container, true);
            
            comment.setFocus(parent);
                      
            button.remove();
            
            if(data.pager.hasNextPage === true){
                comment.addMoreLink(subcomment_container, data);
            }
           
        });
    },
    
    addMoreLink: function(subcomment_container, data){
        var url = comment.getPreparedUrl(data.url, data.pager.nextPage);
        subcomment_container.append('</div><div class="text-center more-comments-container subcomment"><a class="more-comments" href="' + url + '">'+ comment.text.more_comments +' ... </a></div>');
    },
    
    buildMoreSubComments: function(url, container){
        var parent = container.closest('.active-comment'),
            subcomment_container =  $('.sucomment-container', parent);
    
        comment.get(url, function(data){
           $(container).closest('.more-comments-container').remove();

            // Build Subcomments and append
            comment.buildSubCommentList(data.entities, subcomment_container, true);
            
            if(data.pager.hasNextPage === true){
                comment.addMoreLink(subcomment_container, data);
            }
        });
    },

    dateAdapter: function(php_date) {
        var t = php_date.split(/[- :]/),
            date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]),
            minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        
        return (date.getMonth()+1) + '.'
                + date.getDate() + '.'
                + date.getFullYear() + ' '
                + date.getHours() + ':'
                + minutes; 
    },

    appendToContainer: function(container, comment) {
        container.append(comment);
    },
    
    prependToContainer: function(container, comment) {
        container.prepend(comment);
    },
    
    hideResponseInputs: function(){
        $('.response-to-comment').addClass('hidden');
    },
    
    clearResponseTextarea: function(btn) {
        var parent = btn.closest('.active-comment');
        $('.sub-comment-textarea', parent).val('');
    },
    
    changeResponseButtonText: function(btn, is_cancel) {
        if(is_cancel === true) {
            btn.text(comment.text.respond);
        }else {
            btn.text(comment.text.cancel);
        }
    },
  
    changeResponseButtonState: function(btn, is_cancel) {
        var parent = btn.closest('.active-comment'),
            resonse_input = $('.response-to-comment', parent);
    
       if(is_cancel === true) {
            btn.data('action', 'response');
            resonse_input.addClass('hidden');
       }else{
            btn.data('action', 'cancel');
            resonse_input.removeClass('hidden');
       }
    },
    
    setFocus: function(parent){
        $('.sub-comment-textarea', parent).focus();
    },
  
    pagerBuilder: function (pager_data, url) {

        var pager_dom = $.parseHTML(pager_data);
        
        $('ul', pager_dom).addClass('pagination pagination-sm');
        
        $('#comment-pagination-container').append(pager_dom);   
    },
    
    getSignNumber: function (value) {
        return (value > 0) ? "+" + value : value.toString();
    },
    
    getCounterColor: function (value) {
        var color = comment.data.greyColor;
        
        if(value < 0) {
            color = comment.data.redColor;
        }else if(value > 0){
            color = comment.data.greenColor;
        }
        
        return color;
    },
    
    setCommentsContainerBorder: function(){
        $('#comments-container').addClass('comment-bottom-border');
    },
    
    showNoComments: function () {
        $('#no-comments').removeClass('hidden');
    }
  
};

$(function(){comment.init(App, core, {})});