var rv = {
    init: function (App, core) {
        this.setup(App, core);
        this.bindEvent();
    },
    
    setup: function (App, core) {
        this.core = core;
    },
    
    bindEvent: function () {
        
        $('.rule-violation-link').popover({
            html: true
        });
        
        $('body').on('click', '.pop-cancel-rule-btn', this.dismissPopover);
        
        $('body').on('click', '.pop-send-rule-btn', this.sendComplain);
    },
    
    dismissPopover: function (e) {
        var popover = $(e.target).closest('.popover');
        
        rv.clearTextarea(popover);
        //Fix bug in boostrap 3.3.5 , 3.3.6. With double click to show after hide
        popover.data("bs.popover").inState.click = false;
        
        $(popover).popover('hide');
    },
    
    clearTextarea: function (popover) {
        popover.find('textarea').val('');
    },
    
    sendComplain: function (e) {
        var popover = $(e.target).closest('.popover'),
            text = popover.find('textarea').val(),
            type = $(e.target).data('type'),
            id = $(e.target).data('id');
            
        if(!text) {
            return;
        }
        
        rv.core.send({
            method: 'POST',
            url: Routing.generate('rule_violation_create'),
            data: {type: type, id: id, text: text}
        });
        
        popover.data("bs.popover").inState.click = false;
        
        $(popover).popover('hide');
    }
};

$(function (){
    rv.init(App, core, {}); 
});

