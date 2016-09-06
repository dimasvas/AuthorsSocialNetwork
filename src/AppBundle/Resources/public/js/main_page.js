
var mainPage = {
    init: function (App, core) {
        this.setup(App, core);
        this.bindEvent();
        this.bindValidation();
    },
    
    setup: function (App, core) {
        this.App = App;
        this.core = core;
        
        this.subscribeForm = $('#subscribe-form');
    },
    
    bindEvent: function () {
        this.initSlideshow();
        
        $('.grid2').click(mainPage.getLatest);
    },
    
    initSlideshow: function () {
      $("#main-slider").responsiveSlides({
            auto: true,
            pager: false,
            nav: true,
            speed: 500,
            namespace: "callbacks",
            before: function () {
            },
            after: function () {
            }
        });  
    },
    
    bindValidation: function() {
        this.subscribeForm.validate({
            submitHandler: function(form, e) {
                mainPage.submitEmail(form, e);
            },
            rules: {
                email_input: {
                    required: true,
                    email: true
                }
            }
        });
    },
    
    submitEmail: function (form, e) {
        e.preventDefault();
        
        var email = $('#email_input', form).val();
        
        if(!email) {
            return;
        }
        
        mainPage.core.send({
            method: "GET",
            url:  '',
            data: {email: email}
        }, mainPage.submitEmailSuccess);

    },
    
    submitEmailSuccess: function (resp){
        mainPage.showSuccessSubmitMsg();
    },
    
    showSuccessSubmitMsg: function () {
        
    },
    
    getLatest: function () {
        
    }
};

$(function(){mainPage.init(App, core, {})});

$(function(){
    
    $('.grid3').click(function(e){
        
        if($('.tab-3 .facts').length > 0){
            return;
        }
        
        send({
                method: "GET",
                url:  App.routing.comments_latest
            }, function (response){

                buildDom(response.data);
            });
    });
    
    function buildDom(collection){
        if(collection.length < 1) {
            return;
        }
        
        var row = getRow(),
            tab = $('.tab-3'),
            span = $.parseHTML('<span></span>'),
            row = getRow();

        $.each(collection, function(index, entity){
            var clonned_row = $(row).clone(),
                span = $(span).clone(),
                photo = entity.owner.photo ? entity.owner.photo : '/img/profile/no_avatar.png';
            
            if(index >= 4) {
                return false;
            }
            
            $('.tab_list img', clonned_row).prop('src', photo);
            
            $('.tab_list1 a', clonned_row).prop('href', getPreparedUrl(app.routing.composition_show, entity.composition.id));
            $('.tab_list1 a', clonned_row).text(entity.composition.title);
            $('.tab_list1 p', clonned_row).html(dateAdapter(entity.created.date) + '<span>' + entity.content + '</span>');

            
            tab.append(clonned_row);
        });
        
    }
    
    function getRow(){
        return $.parseHTML(
                '<div class="facts">' +
                    '<div class="tab_list">'+
                        '<img width="60px" src="" alt=" " class="img-responsive" />' +
                    '</div>' +
                    '<div class="tab_list1">' +
                        '<a href="#"></a>' +
                        '<p></p>' +
                    '</div>' +
                    '<div class="clearfix"> </div>' +
                '</div>');
    }
});