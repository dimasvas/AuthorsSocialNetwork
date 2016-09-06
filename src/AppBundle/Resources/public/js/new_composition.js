var newComposition = {
    init: function(App, core, extra){
        newComposition.setup();
        newComposition.bindValidation();
        newComposition.initWordCounter();
    },
    
    setup: function(){
        newComposition.descMax = 1000;
        newComposition.titleMax = 100;
        
        newComposition.descCounter = $('#desc-counter span');
        newComposition.titleCounter = $('#title-counter span');
        newComposition.titleInput = $('#composition_title');
        newComposition.descTextarea = $('#composition_description');
        newComposition.form = $('#new-composition-form');
    },
    
    initWordCounter: function(){
        $(newComposition.descTextarea).on(
            'keyup', 
            { 
                counter: newComposition.descCounter,
                max: newComposition.descMax
            }, 
            newComposition.wordCounter
        );
        $(newComposition.titleInput).on(
            'keyup', 
            { 
                counter: newComposition.titleCounter,
                max: newComposition.titleMax
            }, 
            newComposition.wordCounter
        );
    },
    
    bindValidation: function(){
        newComposition.form.validate({
            rules: {
                'composition[title]': {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
                'composition[description]': {
                    required: true,
                    maxlength: 1000,
                },

                'composition[language]': {
                    required: true,
                },
                
                'composition[genres][]' : {
                    required: true,
                }
            },
        
            messages: {
                "appbundle_composition[genres][]": "Выберите хотя бы один жанр.",
            },
        });
    },
    
    wordCounter: function(e){
        var len = $(this).val().length;
        
        $(e.data.counter).text(len);
        
        if (len > e.data.max) {
            $(this).val($(this).val().substring(0, e.data.max - 1));
            $(e.data.counter).text($(this).val().length);
        }
    } 
};

$(function(){newComposition.init(App, core, {})});

