var paginationHandler = {
    
    init: function(options) {
        paginationHandler.setup(options);
    },
    
    setup: function (options) {
        paginationHandler.pagingWapper = options.wrapper;
        
        paginationHandler.text = {
            first: Translator.trans('common.first'),
            last: Translator.trans('common.last'),
            prev: Translator.trans('common.prev_short'),
            next: Translator.trans('common.next_short')
        };
    },
    
    buildPager: function (pagination, callback) {
        
        paginationHandler.resetPagination();
        
        if(!pagination.haveToPaginate) { 
            return;
        }
 
        $(paginationHandler.pagingWapper).twbsPagination({
            totalPages: pagination.totalPages,
            visiblePages: pagination.visiblePages,
            first: paginationHandler.text.first,
            last: paginationHandler.text.last,
            prev: paginationHandler.text.prev,
            next: paginationHandler.text.next,
            
            initiateStartPageClick: false,
            
            onPageClick: function (event, page) {
                //TODO: check if callback function
                callback(event, page);
            }
        });
    },
    
    resetPagination: function () {
        if($(paginationHandler.pagingWapper).data("twbs-pagination")) {
            $(paginationHandler.pagingWapper).twbsPagination('destroy');
        }

        $(paginationHandler.pagingWapper).empty();
    }
};