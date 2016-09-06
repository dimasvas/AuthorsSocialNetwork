/**************************
*  Core
***************************/

var core = {
    init: function(settings){
        core.setup();
    },
    
    setup: function(){
        core.path = {
            composition_cover : '/media/composition/cover/',
            user_img: '/media/user/profile/',
            no_user_img: '/media/user/default/no_avatar.png'
        };
        
        core.bindAuthenticated();
    },
    
    buildUrl: function(url, value, page){
        url.replace('%23', value);
        
        if(page){
            if(!isNaN(page)) {
                throw new Error('Wrong page parameter');
            }
        
            url.concat('/' + page);
        }
        
        return url;
    },
    
    hasItems: function(collection){
        return collection.length > 0 ? true : false;
    },
    
    cloneDom: function(element){
        return $(element).clone();
    },
    
    showElement: function(element){
        //TODO: check if shown Avoid dom redraw
        element.removeClass('hidden');
    },
    
    hideElement: function(element){
        //TODO: Check if shown Avoid dom redraw
        element.addClass('hidden');
    },
    
    emptyElement: function(element){
        element.empty();
    },
    
    getBoolean: function (value) {
        return (value === 'true');
    },
    //TODO: Reseacrh date formats
    dateParser: function(bdate, expose) {
        var  date = new Date(bdate),
             day =  date.getDate() > 10 ? date.getDate() : '0' + date.getDate(),
             month = (date.getMonth() +1) > 10 ? (date.getMonth() +1) : '0' + (date.getMonth() +1),
             shown_date = day + '.' + month + '.'+ date.getFullYear() ;   
        
        if(expose === 'short') {
            return shown_date;
        }
        
        return shown_date + ' '+ date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();                
    },
    
    //Deprecated. Now date available on backend
    dateAdapter: function(php_date, asObject) {
        var t = php_date.split(/[- :]/),
            date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]),
            minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        
        return (date.getMonth()+1) + '.'
                + date.getDate() + '.'
                + date.getFullYear() + ' '
                + date.getHours() + ':'
                + minutes; 
    },
    
    send: function(params, callback) {
        $.ajax(params).done(function( msg ) {
            if(typeof callback  === "function") {
                callback(msg);
            }
        });
    },
    
    bindAuthenticated: function () {
        $(document).ajaxError(function (event, jqXHR) {
            if (403 === jqXHR.status && jqXHR.responseText === 'not_authentificated') {
                window.location.reload();
            }
        });
    }
}    

$(function(){core.init()});
    /*******************************
     *  Common function
     ******************************/
    
    /**
     *  Ajax Send function
     * @param {type} params
     * @param {type} callback
     * @returns {undefined}
     */
    function send(params, callback) {
        $.ajax(params).done(function( msg ) {
            if(typeof callback  === "function") {
                callback(msg);
            }
        });
    }
    
    /**
     * Prepare URL
     * 
     * @param {type} url
     * @param {type} value
     * @returns string
     */
    function getPreparedUrl(url, value) {
        return url.replace('%23', value);
    }
    
    /**
     * Remove class hidden
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function showElement(element){
        element.removeClass('hidden');
    }
    
    /**
     * Hide element
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function hideElement(element) {
        element.addClass('hidden');
    }
    
    /**
     *  Empty element 
     *  
     * @param {type} element
     * @returns {undefined}
     */
    function emptyElement(element){
        element.empty();
    }
    /**
     * Date Adapter
     * @param {type} php_date
     * @returns {String}
     */
    function dateAdapter(php_date) {
        var t = php_date.split(/[- :]/),
            date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]),
            minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        
        return (date.getMonth()+1) + '.'
                + date.getDate() + '.'
                + date.getFullYear() + ' '
                + date.getHours() + ':'
                + minutes; 
    }
