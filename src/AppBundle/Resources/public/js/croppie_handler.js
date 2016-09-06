var croppieHandler = {
    init: function(App, core, options) {
        croppieHandler.setup(App, core, options);
        croppieHandler.initCropInstance();
        croppieHandler.bindEvents();
    },
    
    setup: function(App, core, options){
        croppieHandler.App = App;
        croppieHandler.core = core;
        croppieHandler.url = options.url;
        croppieHandler.page = options.page;
        
        croppieHandler.cropObj = {};
        
        croppieHandler.dom = {
            uploadWrapper: $('#upload-wrapper'),
            imageWrapper: $('#image-wrapper'),
            fileInput: $('#upload'),
            cancelButton:  $('#upload-cancel'),
            uploadButton: $('#upload-do'),
            processingConatiner: $('#processing-container'),
            uploadForm: $('#upload-form'),
            base64Input: $('#imagebase64'),
            coverCollection: $('.composition-cover')        
        };
        
        croppieHandler.settings = options.settings;
        //TODO: Transaltor is global variable. Not good.
        croppieHandler.text = {
            processing: Translator.trans('common.processing'),
            upload: Translator.trans('common.loading'),
        };
    },
    
    bindEvents: function() {
        
        $(croppieHandler.dom.fileInput).on('change', croppieHandler.inputChange);
        
        $(croppieHandler.dom.cancelButton).click(croppieHandler.showCurrentImg);
        
        $(croppieHandler.dom.uploadButton).on('click', croppieHandler.getCroppedImg);
    },
       
    initCropInstance: function(){
        croppieHandler.cropObj = $(croppieHandler.dom.uploadWrapper)
                        .croppie(croppieHandler.settings);
    },
    
    inputChange: function (e) {
        croppieHandler.readFile(e.target);
    },
    
    getCroppedImg: function(e){
        e.preventDefault();
        
        croppieHandler.toggleProcessContainer('show', croppieHandler.text.processing + '...');
        
        croppieHandler.cropObj.croppie('result', {
            type: 'canvas',
            size: 'original'
        }).then(function (img) {
            $(croppieHandler.dom.base64Input).val(img);
            croppieHandler.uploadImg();
        });
    },
    
    resetFileInput: function (){
        $(croppieHandler.dom.fileInput).val('');
    },
    
    uploadImg: function() {
        croppieHandler.toggleProcessContainer('show', croppieHandler.text.upload + '...');
        croppieHandler.resetFileInput();
        
        //data:image/png;base64
        croppieHandler.core.send({
            method: "POST",
            url: croppieHandler.url,
            data: $(croppieHandler.dom.uploadForm).serialize()
        }, function(response){
            croppieHandler.uploadSuccessCallback(response);
        });
    },
    
    uploadSuccessCallback: function (response) {
        croppieHandler.clearBase64Input();
        croppieHandler.toggleUploadButton('hide');
        croppieHandler.toggleCancelButton('hide');
        croppieHandler.toggleProcessContainer('hide');
        croppieHandler.showCurrentImg();
        croppieHandler.setNewImage(response.data.img);
    }, 
    
    readFile: function(input) {
        if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    croppieHandler.cropObj.croppie(
                        'bind', 
                        {
                            url: e.target.result
                        }
                    );
                   
                    $(croppieHandler.dom.uploadWrapper).addClass('ready');
                    
                    croppieHandler.showNewImg();
                    
                };
                
                reader.readAsDataURL(input.files[0]);
                croppieHandler.clearFile(input);
            }
    },
    
    clearFile: function(input) {
        try {
            input.value = null;
        } catch(ex) { }
        if (input.value) {
            input.parentNode.replaceChild(input.cloneNode(true), input);
        }
    },
    
    clearBase64Input: function(){
        $(croppieHandler.dom.base64Input).val('');
    },
    
    toggleProcessContainer: function(toggle, text) {
        if(toggle === 'show'){
           // $(croppieHandler.dom.processingConatiner).text(text);
            $(croppieHandler.dom.processingConatiner).removeClass('hidden');
        } else {
          //  $(croppieHandler.dom.processingConatiner).text('');
            $(croppieHandler.dom.processingConatiner).addClass('hidden');
        }
    },
    
    showCurrentImg: function(e) {
        //TODO: Refactor to toggle class
        $(croppieHandler.dom.uploadWrapper).addClass('hidden');
        $(croppieHandler.dom.imageWrapper).removeClass('hidden');
        
        croppieHandler.toggleCancelButton('hide');
        croppieHandler.toggleUploadButton('hide');
    },
    
    showNewImg: function(e) {
        //TODO: Refactor to toggle class
        $(croppieHandler.dom.imageWrapper).addClass('hidden');
        $(croppieHandler.dom.uploadWrapper).removeClass('hidden');
        
        croppieHandler.toggleCancelButton('show');
        croppieHandler.toggleUploadButton('show');
    },
    
    toggleCancelButton: function (toggle){
        //TODO: Refactor to toggle class
        toggle === 'show' ?
              $(croppieHandler.dom.cancelButton).removeClass('hidden') :
              $(croppieHandler.dom.cancelButton).addClass('hidden');
 
    },
    
    toggleUploadButton: function (toggle){
        //TODO: Refactor to toggle class
        toggle === 'show' ?
            $(croppieHandler.dom.uploadButton).removeClass('hidden') :
            $(croppieHandler.dom.uploadButton).addClass('hidden');
    },
    
    setNewImage: function (img) {
        $.each(croppieHandler.dom.coverCollection, function(index, value){
            $(value).attr("src", img);
        });
        
        if(croppieHandler.page && croppieHandler.page == 'profile') {
            $('#menu-profile-img').attr("src", img);
        }
    }
};