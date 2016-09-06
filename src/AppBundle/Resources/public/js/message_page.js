var message = {
    init: function (App, core, options) {
        message.setup(App, core, options);
        message.initScrollBar();
        message.bindEvent();
    },
    
    setup: function (App, core, options) {
        
        message.core = core;
       
        message.dom = {
            threadTab: $('#thread-tab'),
            threadItem: $('.thread-item'),
            blockedTab: $('#blocked-tab'),
            messageLoader: $('.messages-loader'),
            blockedLoader: $('.blocked-loader'),
            messageWrapper:  $('.message-list-wrapper'),
            sendMessageForm: $('.send-form-wrapper'),
            messageSubmitForm: $('#mesage-send-form'),
            messageTextarea: $('#message-textarea'),
            messageGrid: $('#message-grid'),
            blockedGrid: $('#blocked-grid'),
            messageOuterWrapper: $('#msg-wrapper-2')
        };
        
        message.text = {
            restore: Translator.trans('common.restore'),
            more: Translator.trans('common.more')
        };
        
        message.threadHeight = $(message.dom.threadTab).height();
        message.threadsScrollHeight = $(message.dom.threadTab).prop('scrollHeight');
                
        message.messageHeight = 0;
        message.messageScrollHeight = 0;
        
        message.loadPosition = 200;
        
        message.hasMoreThreads = true;
        message.hasMoreBlocked = true;
        
        message.threadsPage = 2;
        
        message.lockedThreadRequest = false;
        message.lockedBlockedRequest = false;
    },
    
    initScrollBar: function () {
        $(message.dom.threadTab).perfectScrollbar({
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });
        
        $(message.dom.messageOuterWrapper).perfectScrollbar({
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });
    },
    
    bindEvent: function () {
        // Thread scroll
        $(message.dom.threadTab).scroll(message.onThreadsScroll);
        
        // Get more messages
        $('body').on('click', '.next-msg-page-link', message.loadMoreMessages);
        
        //Get blocked
        $(message.dom.blockedGrid).click(message.onBlockedClick);
        
        //Restore from Blocked
        $('body').on('click', '.restore-btn', message.restoreBlocked);
        
        //Restore from Blocked
        $('body').on('click', '#more-blocked', message.getMoreBlocked);
        
        $(message.dom.threadItem).click(message.onThreadItemCLick);
        
        $(message.dom.messageSubmitForm).submit(message.onMessageSubmit);
        
        $(message.dom.messageGrid).click(message.onMessageTabClick);
    },
    
    getMoreBlocked: function (e) {
        e.preventDefault();
        
        var page = $(this).data('page');
        //TODO: Get more blocked
        //  message.loadBlocked(page);
    },
    
    restoreBlocked: function (e) {
        var blocked = $(this).data('blocked-id');
        
        send({
            type: 'POST',
            data: {_method: 'delete'},
            url: Routing.generate('message_acl_delete', {blocked: blocked})
        }, function (response){
            message.restoreSuccess(response, e.target);
        });
    },
    
    restoreSuccess: function (response, target) {
        
        if(response.success === true) {
            $(target).closest('tr').remove();
            
            if($('#blocked-table tbody').is(':empty')) {
                message.core.showElement($('#no-blocked-notif'));
            }
        }
    },
    
    onBlockedClick: function (e) {
        if($('#blocked-table').is(':empty')) {
            message.core.showElement($(message.dom.blockedLoader));
            message.loadBlocked(1);
        }
    },
    
    loadBlocked: function (page) {
        
        send({
            method: "GET",
            url: Routing.generate('message_acl', {page: page})
        }, function (response){
            message.core.hideElement($(message.dom.blockedLoader));
            message.loadBlockedResponse(response);
        });
    },
    
    loadBlockedResponse: function (response) {
        var collection = jQuery.parseJSON(response.collection);
        
        if (collection.length > 0) {
            message.buildBlockedDom(collection);
            message.buildBlockedPagination(response.pagination);
        }else {
            message.core.showElement($('#no-blocked-notif'));
        }
    },
    
    buildBlockedDom: function (collection) {
        
        var row = message.getBlockedDom(),
            tbody = $('<tbody></tbody>');
    
          $.each(collection, function(index, entity){
            var clonned_row = $(row).clone(),
                date = message.core.dateParser(entity.created),
                userLink = entity.blocked.isAuthor ? 
                        Routing.generate('author_show', {id: entity.blocked.id}) :  Routing.generate('profile_show', {id: entity.blocked.id});
            
            $('.user-name a', clonned_row).text(entity.blocked.username);
            $('.user-name a', clonned_row).attr('href', userLink);
            $('.date', clonned_row).text(date);
            $('button', clonned_row).data('blocked-id', entity.blocked.id);
           
            $(tbody).append(clonned_row);
        });

        $('#blocked-table').append(tbody);     
    },
    
    buildBlockedPagination: function (pagination) {
        if(pagination.hasNextPage) {
            var dom =   '<tr>' + 
                            '<td colspan=3 class="text-center">' +
                                '<a href="" id="more-blocked" data-page="' + pagination.nextPage +'">' +
                                    ' <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>   ' + 
                                    message.text.more +'</a>' +
                            '</td>' +
                        '</tr>';
                
            $('#blocked-table tbody').prepend(dom); 
        }
    },
    
    getBlockedDom: function () {
        var dom =   '<tr>' +
                        '<td class="user-name"><a href="" target="_blank"></a></td>' +
                        '<td class="date"></td>' +
                        '<td><button type="button" class="btn btn-default btn-sm restore-btn">' + message.text.restore +'</button></td>' 
                    +'</tr>';
            
        return $.parseHTML(dom);
    },
    
    onThreadsScroll: function (e) {
        if(!message.hasMoreThreads) {
            message.unbindScroll(message.dom.threadTab);
        }
        
        if(message.lockedThreadRequest) {
           return; 
        }
        
        if(message.allowThreadLoading($(this).scrollTop())) {
            message.loadMoreThreads();
        }
    },
    
    setMessageHeight: function () {
         message.messageHeight = $(message.dom.messageOuterWrapper).height();
         message.messageScrollHeight = $(message.dom.messageOuterWrapper).prop('scrollHeight');
    },
    
    updateThreadScroll: function () {
        $(message.dom.threadTab).perfectScrollbar('update');
    },
    
    setMessageScrollToBottom: function () {
        $(message.dom.messageOuterWrapper)
                .scrollTop($(message.dom.messageOuterWrapper)
                .prop('scrollHeight'));
    },
    
    updateThreadScrollHeight: function () {
        message.threadsScrollHeight = $(message.dom.threadTab).prop('scrollHeight');
    },
    
    updateMessageScroll: function () {
        $(message.dom.messageOuterWrapper).perfectScrollbar('update');
    },
    
    onThreadItemCLick: function (e) {
        
        var item = $(e.target).closest('.facts');
                
        message.removeThreadSelection();

        $(item).addClass('selected-thread');
        
        message.showMessageContainer();
        
        message.emptyMessageWrapper();
        
        message.loadMessages($(item).data('thread-id'), 1);
        
    },
    
    onMessageTabClick: function (e) {
        var thread = $('.selected-thread').data('thread-id');
        
        message.emptyMessageWrapper();
        
        message.loadMessages(thread, 1);
    },
        
    loadMoreThreads: function () {
        
        message.lockRequest('threads');
        
        send({
            method: "GET",
            url:  Routing.generate('thread_list', {page: message.threadsPage})
            }, 
            message.onThreadsLoad
        );
    },
    
    loadMoreMessages: function (e) {
        e.preventDefault();
        
        var thread = $('.selected-thread').data('thread-id'),
            page = $(e.target).data('nextPage');
    
        message.deleteNextPage();    
        
        message.loadMessages(thread, page);
    },
    
    onThreadsLoad: function(response) {
        var data = jQuery.parseJSON(response.collection);
        
        message.setMoreResults('threads', response.pagination.hasNextPage);
        
        if(data.length > 0) {
            message.buildThreadsDom(data, true);
            message.updateThreadScrollHeight();
            message.updateThreadScroll();
            message.increasePage('threads');
            message.unlockRequest('threads');
        }
    },
    
    allowThreadLoading: function (position) {
        return ((message.threadsScrollHeight - message.threadHeight) - position) < message.loadPosition;
    },
    
    lockRequest: function (type) {
        switch(type) {
            case 'threads':
                message.lockedThreadRequest = true;
                break;
            case 'blocked':
                message.lockedBlckedRequest = true;
                break;
            default:
                throw new Error('Wrong type.');
        }
    },
    
    unlockRequest: function(type) {
        switch(type) {
            case 'threads':
                message.lockedThreadRequest = false;
                break;
            case 'blocked':
                message.lockedBlckedRequest = false;
                break;
            default:
                throw new Error('Wrong type.');
        }
    },
    
    increasePage: function(type) {
        switch(type) {
            case 'threads':
                message.threadsPage ++;
                break;
            case 'blocked':
                break;
            default:
                throw new Error('Wrong type.');
        }
    },
    
    setMoreResults: function (type, value) {
        switch(type) {
            case 'threads':
                message.hasMoreThreads = value;
                break;
            case 'messages':
                message.hasMoreMessages = value;
                break;
            case 'blocked':
                message.hasMoreBlocked = value;
                break;
            default:
                throw new Error('Wrong type.');
        }
    },
    
    unbindScroll: function(element) {
        $(element).unbind( "scroll");
    },
    
    buildThreadsDom: function(data, append){
        var row = message.getMessageItemDOM(),
            container = $('.msg-tab-1'),
            wrapper= $("<div><div/>");
        
        $.each(data, function(index, entity){
            var clonned_row = $(row).clone();
           
            clonned_row.data('thread-id', entity.thread);
            clonned_row.addClass('thread-item');
            
            $('.user-name', clonned_row).text(entity.sender.name);
            $('.msg-date', clonned_row).text(message.core.dateParser(entity.created));
            $('.msg-date', clonned_row).append(' <span class="msg-body">' + message.getThreadBody(entity.body) + '...' +'</span>');
            $('img', clonned_row).attr('src', message.getImage(entity.sender.image));
            
            if(!entity.recipient_viewed){
                message.markAsUnread(clonned_row);
            }

            message.appendPrepend(wrapper, clonned_row, append);
        });
        
        $(container).append(wrapper);
    },
    
    getMessageItemDOM: function (){    
        var dom =   '<div class="facts" data-thread-id="">' +
                        '<div class="tab_list">' +
                                '<img src="" alt=" " width="60px" />' +
                        '</div>'  +
                        '<div class="tab_list1">' +
                              '<a href="#" class="not-active user-name"></a>' +
                              '<p class="msg-date"></p>' +
                        '</div>' +
                        '<div class="clearfix"> </div>'  +
                    '</div>';
            
        return $.parseHTML(dom);
    },
    
    getImage: function (image) {
        return (image ? message.core.path.user_img + image : message.core.path.no_user_img);
    },
    
    getThreadBody: function(text) {
        return text.length > 30 ? text.substring(0,10) : text;
    },
    
    markAsUnread: function(element) {
        element.addClass('new-thread-message');
    },
    
    markAsRed: function (element){
        element.removeClass('new-thread-message');
    },
    
    appendPrepend: function(container, clonned_row, append) {
        append ?container.append(clonned_row) : container.prepend(clonned_row);
    },
    
    removeThreadSelection: function () {
        $('.selected-thread').removeClass('selected-thread');
    },
    
    showMessageContainer: function() {
        var tab_1 = $('.msg-tab-1'),
            tab_2 = $('.msg-tab-2'); 
    
        $('.message-grid1').removeClass('resp-tab-active');
        $('.message-grid2').addClass('resp-tab-active');
        
        tab_1.removeClass('resp-tab-content-active');
        tab_1.css("display", "none");
        
        tab_2.addClass('resp-tab-content-active');
        tab_2.css('display', 'block');
    },
    
    loadMessages: function (thread_id, page) {
         
        message.core.showElement($(message.dom.messageLoader));
        
        send({
            method: "GET",
            url:  Routing.generate('thread_messages', {thread: thread_id, page: page})
            }, function (response){
                message.onLoadMessageResponse(response, thread_id);
            });
    },
    
    onLoadMessageResponse: function (response, threadId) {
                
        var data = jQuery.parseJSON(response.collection);
                
        message.core.hideElement($(message.dom.messageLoader));
        
        message.setMoreResults('messages', response.pagination.hasNextPage);
        
        message.buildMessagesDom(data, true);
        
        message.setRecipientData(response.recipient);
                           
        message.core.showElement($(message.dom.sendMessageForm));
        
        message.setMessageScrollToBottom();
        
        message.setMessageHeight();
                               
        message.manageNextPageLink(response.pagination);
    },
    
    emptyMessageWrapper: function () {
        $(message.dom.messageWrapper).empty();
    },
    
    setRecipientData: function(threadId) {
        $(message.dom.messageSubmitForm).data('recipient', threadId);
    },
    
    buildMessagesDom: function(data, append){
        var row = message.getMessageItemDOM(),
            wrapper= $("<div><div/>");
        
        $.each(data, function(index, entity){
            var clonned_row = $(row).clone();
           
            clonned_row.data('id', entity.id);
            clonned_row.addClass('message-item');
            
            $('.user-name', clonned_row).text(entity.sender.name);
            $('.msg-date', clonned_row).text(message.core.dateParser(entity.created));
            $('.msg-date', clonned_row).append(' <span class="msg-body">' + entity.body + '...' +'</span>');
            $('img', clonned_row).attr('src', message.getImage(entity.sender.image));

            message.appendPrepend(wrapper, clonned_row, append);
        });
        
        $(message.dom.messageWrapper).append(wrapper);
    },
    
    onMessageSubmit: function (e) {
        e.preventDefault();
        
        var text = $('textarea', $(this)).val(),
            recipient = $(message.dom.messageSubmitForm).data('recipient');
            
        if(!message.isValidMessage(text)){
            return;
        }    

        message.sendMessage(text, recipient);
    },
    
    isValidMessage: function(text) {
        return text.trim().length < 1 ? false : text;
    },
    
    sendMessage: function(text, recipient){   
        send({
            method: "POST",
            url: Routing.generate('message_create', {recipient: recipient}),
            data: {body: text}
        }, message.onSendMessageResponse);
    },
    
    onSendMessageResponse: function(response) {
        var data = jQuery.parseJSON(response.collection);
        
        message.buildMessagesDom([data], true);
        
        message.clearTextarea();
        
        message.updateMessageScroll();
        
        message.setMessageScrollToBottom();
    },
    
    clearTextarea: function () {
        $(message.dom.messageTextarea).val('');
    },
    
    manageNextPageLink: function(pagination){
        
        if(!pagination.hasNextPage) {
            return;
        }
        
        var link = message.getNextPageDom();
        
        $('a', link).data('nextPage', pagination.nextPage);
        
        $(message.dom.messageWrapper).prepend(link);
        
    },
    
    getNextPageDom: function(){
        return $.parseHTML('<div class="next-msg text-center"><a href="" class="next-msg-page-link"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> ' + message.text.more +'</a></div>');
    },
    
    deleteNextPage: function () {
        $('.next-msg-page-link').remove();
    }
    
    //TODO: cache Thread Messages
};

$(function(){message.init(App, core, {})});


//$(function(){
//    
//    
//    initWrappersHeight();
//   
//    loadThreadsList();
//    
//    $('body').on('click', '.thread-facts', function(e){
//        
//        var thread_id = $(this).data('id');
//                
//        markAsRed($(this));
//        
//        showMessageContainer();
//        
//        loadThreadMessages(thread_id);
//    });
//    
//    $('#mesage-send-form').submit(function(e){
//        e.preventDefault();
//        
//        var self = $(this),
//            message = $('textarea', self).val(),
//            recipient_id = self.data('recipient-id'); //TODO Set reciptient Id
//            
//        if(!isValidMessage(message)){
//            return;
//        }    
//        
//        sendMessage(message, recipient_id);
//        
//    });
//    
//    
//   $('body').on('click', '.next-msg-page-link', function(e){
//        e.preventDefault();
//        
//        var self = $(this),
//            page = self.data('nextPage');
//            
//        
//        send({
//                method: "GET",
//                url:  getPreparedUrl(app.routing.get_thread_messages, thread_id)
//            }, function (response){
//                console.log('Response', response);
//                hideElement($('.messages-loader'));
//                
//               // buildMessagesDom(response.data);
//                
//                manageNextPageLink(response.data, $('.message-list-wrapper'));
//
//            });
//        
//    });
//    
//    function initWrappersHeight(){
//        $('.resp-tabs-container').css('height', $(window).height());
//        $('.message-list-wrapper').css('height', $(window).height() * 0.7);
//        $('.message-list-wrapper').css('overflow', 'auto');
//    
//    }
//    
//    function sendMessage(message, recipient_id){
//        
//          send({
//            method: "POST",
//            url: getPreparedUrl(app.routing.message_create, recipient_id),
//            data: {body: message}
//        }, function (response){
//            buildMessagesDom(response.data);
//        });
//    }
//    
//    function isValidMessage(message){
//        return message.trim().length < 1 ? false : message;
//    }
//    
//    function loadThreadsList(){
//        
//        send({
//                method: "GET",
//                url:  app.routing.get_threads_list
//            }, function (response){
//                
//                if(response.data.length > 0){
//                   buildThreadsDom(response.data); 
//                }else{
//                    //showNoDataText();
//                }
//            });
//    }
//    
//    function buildThreadsDom(data, append){
//        var row = getMessageItemDOM(),
//            container = $('.msg-tab-1');
//        
//        $.each(data, function(index, entity){
//            var clonned_row = $(row).clone(),
//                photo = entity.photo ? entity.photo : '/img/profile/no_avatar.png',
//                date = dateAdapter(entity.date.date);
//            
//            entity.body = entity.body.length > 30 ? entity.body.substring(0,10) : entity.body;
//           
//            clonned_row.data('id', entity.id);
//            
//            $('.user-name', clonned_row).text(entity.user);
//            $('.thread-data-container', clonned_row).text(date);
//            $('.thread-data-container', clonned_row).append(' <span class="msg-body">' + entity.body + '...' +'</span>');
//            $('.user-img', clonned_row).attr('src', photo);
//            
//            if(!entity.viewed){
//                markAsUnread(clonned_row);
//            }
//            
//            appendPrepend(container, clonned_row, append);
//        });
//    }
//    
//    function appendPrepend(container, clonned_row, append){
//        append ?container.append(clonned_row) : container.prepend(clonned_row);
//    }
//    
//    function getMessageItemDOM(){
//        var dom =   '<div class="facts thread-facts" data-id="">' +
//                        '<div class="tab_list">' +
//                            '<a href="" class="b-link-stripe b-animate-go swipebox"  title="">' +
//                                    '<img src="" alt="Contact" width="70px"  class="img-responsive user-img" />' +
//                            '</a>' +
//                        '</div>' +
//                        '<div class="tab_list1">' +
//                            '<a href="#" class="user-name"></a>' +
//                            '<p class="thread-data-container"></p>' +
//                        '</div>' +
//                        '<div class="clearfix"> </div>' +
//                    '</div>';
//            
//        return $.parseHTML(dom);
//    }
//    
//    function markAsUnread(element){
//        element.addClass('new-thread-message');
//    }
//    
//    function markAsRed(element){
//        element.removeClass('new-thread-message');
//    }
//    
//    function showMessageContainer(){
//        var tab_1 = $('.msg-tab-1'),
//            tab_2 = $('.msg-tab-2'); 
//    
//        $('.message-grid1').removeClass('resp-tab-active');
//        $('.message-grid2').addClass('resp-tab-active');
//        
//        tab_1.removeClass('resp-tab-content-active');
//        tab_1.css("display", "none");
//        
//        tab_2.addClass('resp-tab-content-active');
//        tab_2.css('display', 'block');
//    }
//    
//    function loadThreadMessages(thread_id){
//        
//        showElement($('.messages-loader'));
//        
//        send({
//                method: "GET",
//                url:  getPreparedUrl(app.routing.get_thread_messages, thread_id)
//            }, function (response){
//                console.log('Response', response);
//                hideElement($('.messages-loader'));
//                
//                clearMessageDom();
//                
//                buildMessagesDom(response.data);
//                
//                setRecipientData(response.data);
//                
//                showElement($('.send-form-wrapper'));
//                
//                focusOnMsgContainer();
//                
//                manageNextPageLink(response.data, $('.message-list-wrapper'));
//
//            });
//    }
//    
//    function manageNextPageLink(response, container){
//        if(!response.pager.hasNextPage) {
//            return;
//        }
//        
//        var link = getNextPageDom();
//        
//        $('a', link).data('nextPage', response.pager.nextPage);
//        
//        container.prepend(link);
//        
//    }
//    
//    function getNextPageDom(){
//        return $.parseHTML('<div class="next-msg text-center"><a href="" class="next-msg-page-link"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>  Еще</a></div>');
//    }
//    
//    function clearMessageDom(){
//        $('.message-list-wrapper').empty();
//    }
//    
//    function getMessagesDom(){
//        var dom =   '<div class="facts message-facts">' +
//                        '<div class="tab_list">' +
//                           '<a href="" class="b-link-stripe b-animate-go   swipebox message-user-link"  title="">' +
//                                '<img src="" width="70px" alt=" " class="img-responsive" />' +
//                            '</a>' +
//                        '</div>' +
//                        '<div class="tab_list1">' +
//                            '<a href="#" class="user-name">excepturi sint occaecati</a>' +
//                            '<p class="message-data-container"></p>' +
//                        '</div>' +
//                        '<div class="clearfix"> </div>' +
//                    '</div>';
//            
//        return $.parseHTML(dom);
//    }
//    
//    function buildMessagesDom(data){
//        if(data.entities.length < 1) {
//            showNoDataContainer();//Messages from the tramslate symfony. Add as links
//        }
//        
//        var row = getMessagesDom(),
//            container = $('.message-list-wrapper');    
//        
//        $.each(data.entities, function(index, entity){ 
//            
//            var clonned_row = $(row).clone(),
//                photo = getUserPhoto(entity.sender.photo),
//                date = dateAdapter(entity.msg.created.date);
//                
//                $('.user-name', clonned_row).text(entity.sender.name)
//                $('img', clonned_row).attr('src', photo);
//                $('.message-data-container', clonned_row).text(date);
//                $('.message-data-container', clonned_row).append(' <span class="msg-body">' + entity.msg.body + '...' +'</span>');
//                
//            container.append(clonned_row);    
//        });
//        
//    }
//    
//    function setRecipientData(data){
//        $('#mesage-send-form').data('recipient-id', data.recipient)
//    }
//    
//    function showNoDataContainer(){
//        
//    }
//    
//    function getUserPhoto(photo){
//        return photo ? photo : '/img/profile/no_avatar.png';
//    }
//    
//    function focusOnMsgContainer(){
//        $( "msg-wrapper-2" ).focus()
//    }
//    
//});