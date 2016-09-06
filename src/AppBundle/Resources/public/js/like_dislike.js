var ld = {
    init: function (App, core) {
        this.setup(App, core);
        this.bindEvent();
    },
    
    setup: function (App, core) {
        this.core = core;
        this.App = App;
        
        this.data = {
            greenColor: '#3c763d', 
            redColor: '#a94442',
            greyColor: '#333'
        };
        
        this.isUserAuth = core.getBoolean(this.App.isUserAuth);
    },
    
    bindEvent: function () {
        $('body').on('click', '.like-btn, .dislike-btn', this.onVoteClick);
    },
    
    onVoteClick: function (e) {
        e.preventDefault();
        
        var type = $(this).data('type');

        ld.core.send({
            method: "POST",
            url:  Routing.generate('vote_create', {comment_id: $(e.target).closest('.active-comment').data('id')}),
            data: {type: type}
        }, function (response){
            if(response.status === 'success') {
                ld.successResponse(response, e.target, type);
            }
        });
    },
    
    successResponse: function (response, target, type) {
        ld.setColor(target, type);
        ld.updateCouner(target, type);
    },
    
    setColor: function (target, type) {
        $(target).css('color', type === 'like' ? '#3c763d' : '#a94442');
    },
    
    updateCouner: function (target, type) {
        var counter = $(target).closest('a').siblings('.vote-counter'),
            value = ld.getValue(counter, type);

        $(counter).text(ld.getSignNumber(value));
        
        ld.setCounterColor(counter, value);
    },
    
    getValue: function (container, type) {
        var value = parseInt($(container).text(), 10);
        
        return (type === 'like') ? (value + 1) : (value - 1);
    },
    
    getSignNumber: function (value) {
        return (value > 0) ? "+" + value : value.toString();
    },
    
    setCounterColor: function (container, value) {
        var color = ld.data.greyColor;
        
        if(value < 0) {
            color = ld.data.redColor;
        }else if(value > 0){
            color = ld.data.greenColor;
        }
      
        $(container).css('color', color);
    }
};

$(function(){ld.init(App, core, {})});