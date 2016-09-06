"use strict"

var attachment = {
    
    init: function(App, core, settings) {
        attachment.setup(App, core);
        attachment.initFileUploadLib();
        attachment.getContent();
        attachment.bindDeleteEvent();
    },
    
    setup: function(App, core){
        attachment.data = App.data;
        attachment.type =  App.data.type;
        attachment.core = core;
        attachment.composition = App.data.composition;
        attachment.upload_dir = App.data.upload_dir;
        attachment.isOwner;
        attachment.thumb_dir = '/images/thumb/';
        attachment.audio_thumb = '/images/thumb/audio.png'
        
        attachment.isUserAuth = (App.isUserAuth === 'true');
        
        attachment.dom = {
            progress_bar: $('#progress .progress-bar'),
            container: $('#att-list-container'),
            typeContainer: $('#type-list-container'),
            noFiles: $('.no-files')
        };
        
        attachment.route = {
            getList: Routing.generate('user-attachment-list', {id: attachment.composition})
        };
        
        attachment.uploadSettings = {
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|mp3)$/i,
            maxFileSize: 15000000, // 20 MB
            loadImageMaxFileSize: 1000000, 
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            disableAudioPreview: true,    
            previewMaxWidth: 100,
            previewMaxHeight: 100,
            previewCrop: true,
            singleFileUploads: true,
            formData: {
                description: '',
                composition_id: attachment.composition
            }
        };
        
        attachment.text = {
            added: Translator.trans('common.added'),
            delete: Translator.trans('common.delete'),
            description: Translator.trans('common.description'),
            no_audio_support: Translator.trans('common.no_audio_support'),
            processing: Translator.trans('common.processing'), 
            upload: Translator.trans('common.upload'),
            upload_failed: Translator.trans('common.upload_failed')
        };
    },
    
    bindDeleteEvent: function () {
        $('#files-list-container')
            .on(
                'click', 
                'input.delete-attachment-btn', 
                attachment.deleteItem
            );
    },
    
    initAttachmentData: function (response) {
        attachment.isOwner = response.isOwner;
        attachment.upload_dir = response.upload_dir;
    },
    
    initFileUploadLib: function() {            
        $('#fileupload').fileupload(attachment.uploadSettings)
            .on('fileuploadadd', attachment.fileAdd)
            .on('fileuploadsubmit', attachment.uploadSubmit)
            .on('fileuploadprocessalways', attachment.fileProcessAlways)
            .on('fileuploadprogressall', attachment.fileProgressAll)
            .on('fileuploaddone', function (e, data) {
                
                attachment.hidePreview(data.context);

                attachment.hideProgressBar();

                attachment.onUploadSuccess(data.result);
            
        }).on('fileuploadfail', attachment.uploadFail)
            .prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    },
    
    fileProgressAll: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        
        $(attachment.dom.progress_bar).css('width', progress + '%');
    },
    
    fileProcessAlways: function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        
            if (file.preview) {
                node
                    .prepend('<br>')
                    .prepend(file.preview);
            }
            if (file.error) {
                node
                    .append('<br>')
                    .append($('<span class="text-danger"/>').text(file.error));
            }
            if (index + 1 === data.files.length) {
                data.context.find('button')
                    .text(attachment.text.upload)
                    .prop('disabled', !!data.files.error);
            }
    },
    
    fileAdd: function (e, data) {
        data.context = $('<div/>').addClass('new-file-item').appendTo('#files');

        $.each(data.files, function (index, file) {

            var node = $('<div/>').addClass('col-lg-2 col-md-3 col-sm-3 col-xs-3').append($('<div/>').text(file.name));

            if (!index) {
                if(file.type === 'audio/mp3') {
                    node.append('<img src="'+ attachment.audio_thumb +'">'); 
                    node.append(attachment.getUploadButton().clone(true).data(data));  
                } else {
                    node.append(attachment.getUploadButton().clone(true).data(data));      
                }
            }
            node.appendTo(data.context);

            data.context.append($(
                '<div class="form-group col-lg-6 col-md-8 col-sm-8 col-xs-8 col-xs-offset-1">' + 
                    '<label for="exampleInputEmail1">' + attachment.text.description  +'</label>' +
                    '<textarea class="form-control" rows="3"></textarea>' +
                '</div>' +
                '<div class="clearfix"></div>'
            ));
        });
    },
    
    uploadSubmit: function (e, data) {
        var textarea = $('textarea', $(data.context[0]));
        //set data to upload object    

        data.formData = {
            description: textarea.val(),
            composition_id: attachment.composition
        };

        textarea.val('').attr("disabled","disabled");
    },
    
    uploadFail: function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text(attachment.text.upload_failed);
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    },
    
    hidePreview: function (context) {
        $(context).fadeOut( "slow" );
    },
    
    hideProgressBar: function() {
        setTimeout(function() {
            $(attachment.dom.progress_bar).css('width',0);
        }, 1000);
    },
    
    onUploadSuccess: function(files) {
        attachment.buildDom([files]);
    },
    
    getContent: function(){
        attachment.core.send({
            method: "GET",
            url:  attachment.route.getList
        }, attachment.processResponse); 
    },
    
    processResponse: function(response){
        
        if(response.files.length < 1){
            attachment.showNoFiles();
            return;
        }
        
        attachment.initAttachmentData(response);
        
        attachment.buildDom(response.files);
    },
    
    buildDom: function(collection){
        
        var dom = attachment.getDom(),
            wrapper = $('<div/>');
        
        $.each(collection, function(index, entity){
            
            var clonned_dom = attachment.core.cloneDom(dom);
                //TODO: Refactor on backend. no date from upload listener (attach JMS serializer).
                if (entity.created) {
                    var  date = new Date(entity.created),
                        shown_date = date.getDate() + '.' + (date.getMonth() +1) + '.'+ date.getFullYear() ;
                } else {
                    shown_date = 'сегодня';
                }
                
                
            $(clonned_dom).addClass('class-item-' + entity.id);
            $('.delete-attachment-btn', clonned_dom).data('att-id', entity.id);
            $('.text', clonned_dom).text(entity.description);
            $('.date span', clonned_dom).text(shown_date);
            
            if(attachment.getThumbType(entity.file) ==='image') {
                $('.file-content .inner-content', clonned_dom).html('<a class="thumbnail" data-lightbox="image-'+entity.id + '"href="'+ attachment.upload_dir + entity.file +'"><img height="60px" src="'+ attachment.upload_dir + entity.file +'"></img></a>');
            }else {
                $('.file-content .inner-content', clonned_dom).prepend('<img src="'+ attachment.thumb_dir + "audio.png" +'"></img>');
                $('.text', clonned_dom).html('<div class="description-audio">'+ entity.description +'</div><div class="audio-player"></div>');
                $('.audio-player',clonned_dom).html(attachment.getAudioDom(entity.file));
            }
            
            wrapper.prepend(clonned_dom);
        });
        
        attachment.manageContainers(wrapper);
    },
    
    manageContainers: function(wrapper) {
        
        if(attachment.dom.container) {
            attachment.dom.container.append(wrapper);
        }
        
        if(attachment.type === 'music' || attachment.type === 'image') {
            var clonned_wrapper = $(wrapper).clone(true);
            $('.delete-attachment-btn', clonned_wrapper).remove();
            
            attachment.dom.typeContainer.append(clonned_wrapper);
        }
        
        attachment.core.hideElement(attachment.dom.noFiles);
    },
    
    getDom: function(){
        var delete_btn = (parseInt(attachment.data.isOwner,10) === 1) ? 
            '<input class="btn btn-default btn-xs delete-attachment-btn" type="submit" value="' + attachment.text.delete +'">':
            '';
        
        return $.parseHTML(
                '<li class="list-group-item">' +
                    '<div class="row">' +
                        '<div class="col-lg-2 file-content text-center">'+
                            '<div class="inner-content"></div>' +
                            delete_btn +
                        '</div>' +
                        '<div class="col-lg-10 file-description">' +
                            '<div class="date">' + attachment.text.added +': <span></span></div>' +
                            '<div class="text"></div>' +
                        '</div>' +
                    '</div>' +
                '</li>'
                );
    },
    
    getAudioDom: function(fileName){
        var fileExt = attachment.getFileType(fileName),
            audioDom = $.parseHTML(
                            '<audio controls>' +
                                '<source src="'+ attachment.upload_dir + fileName +'" type="audio/'+ fileExt +'">' +
                                attachment.text.no_audio_support +
                            '</audio>'
                        );
        return audioDom;        
    },
    
    getThumbType: function(fileName){
        var fileExt = attachment.getFileType(fileName),
            type;
       
       switch (fileExt) {
            case "jpg":
            case "png":
            case "gif":
                type = 'image';
                break; 
            case "mp3":
            case "wav":
                type = 'audio';
                break; 
            default: 
                throw new Error("Wrong fileType: " + fileExt);
        }
        
        return type;
    },
    
    getFileType: function(filename){
        return filename.substr(filename.lastIndexOf('.')+1);
    },
    
    getUploadButton: function () {
        return $('<button/>').addClass('btn btn-default btn-xs').prop('disabled', true)
                .text(attachment.text.processing + '...').on('click', function () {
                    var $this = $(this),
                        data = $this.data();
                    
                        $this.off('click').text('Abort')
                            .on('click', function () {
                                $this.remove();
                                data.abort();
                            });
                            
                    data.submit().always(function () {
                        $this.remove();
                    });
                });  
    },
    
    deleteItem: function (e) {
        
        var dataId = $(e.target).data('att-id');
        
        if(!dataId) {
            throw new Error('No data id for delete element.');
        }
        
        attachment.core.send({
            method: "DELETE",
            url: Routing.generate('attachment_delete', {id: dataId})
        }, function (response){
             attachment.manageDeleteItem(dataId);
        });
    },
    
    manageDeleteItem: function(dataId) {
        $('.class-item-' + dataId).remove();
    },

    
    showNoFiles: function () {
        $(attachment.dom.noFiles).removeClass('hidden');
    }
//    bindColorBoxEvent: function(){
//        $('a.gallery').colorbox({ opacity:0.5 , rel:'group1' });
//    }
};

$(function(){attachment.init(App, core, {})});


