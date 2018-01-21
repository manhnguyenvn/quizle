function QuizImagesUploader(quiz_object) {
    this.$file = null;
    this.$source = null;
    this.$quiz_object = quiz_object;
    this.$cropper_init = 0;
    this.$cropper_data = null;

    this.$container = $("#quiz-images-container");
    this.$upload_container = $("#quiz-images-upload-container");
    this.$upload_file = $("#quiz-image-file");
    this.$upload_image_container = $("#quiz-image-new-container");
    this.$upload_image = $("#quiz-image-new");
    this.$upload_container_title = $("#quiz-images-upload-title");
    this.$lightbox_close_button = $("#quiz-images-lightbox-close");
    this.$quiz_container = $("#quiz-container");
    this.$upload_button = $("#quiz-image-new-save");

    this.init();
}

QuizImagesUploader.prototype = {
    init: function() {
        this.addEvents();
    },

    addEvents: function() {
        this.$upload_container.on('dragenter dragover', $.proxy(this.dragover, this));
        this.$upload_container.on('dragleave', $.proxy(this.dragleave, this));
        this.$upload_container.on('drop', $.proxy(this.drop, this));
        this.$upload_container.on('click', $.proxy(function() {
            this.$upload_file.trigger('click');
        }, this));
        this.$upload_file.on('change', $.proxy(this.changeImage, this));
        this.$lightbox_close_button.on('click', $.proxy(function() {
            this.hideLightbox();
        }, this));
        this.$upload_button.on('click', $.proxy(this.uploadImage, this));
    },
    
    showLightbox: function(source) {
        $("#quiz-images-lightbox").height($(document).height()).show();
        $('html, body').scrollTop(0);

        if(source.image_type == 'option') {
            $("#quiz-images-option-type-upload-dimenson").show();
            $("#quiz-images-other-type-upload-dimenson").hide();
        }
        else {
            $("#quiz-images-option-type-upload-dimenson").hide();
            $("#quiz-images-other-type-upload-dimenson").show();
        }

        this.$source = source;
    },

    hideLightbox: function() {
        $("#quiz-images-upload-container").show();
        $("#quiz-image-new-container").hide();
        $("#quiz-image-new-attribution").val('').removeAttr('disabled');
        $("#quiz-image-new-no-attribution").prop('checked', false);
        this.$upload_image_container.css('width', '100%');
        if(this.$cropper_init == 1) {
            this.$upload_image.cropper('destroy');
            this.$cropper_init = 0;
            this.$container.width(this.$quiz_container.width());
        }

        $("#quiz-images-lightbox").hide();
    },

    dragover: function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.$upload_container.addClass('theme-upload-dragover');
    },

    dragleave: function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.$upload_container.removeClass('theme-upload-dragover');
    },

    drop: function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.$upload_container.removeClass('theme-upload-dragover');

        this.$file = e.originalEvent.dataTransfer.files[0];
        this.validateImage();
    },

    changeImage: function() {
        this.$file = this.$upload_file.prop('files')[0];
        this.validateImage();
    },

    validateImage: function() {
        if(['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].indexOf(this.$file.type) == -1) {
            this.validateError('extension');
            return;
        }
        
        if(this.$file.size > (MAX_IMAGE_SIZE_ALLOWED_MB*1024*1024)) {
            this.validateError('size');
            return;
        }

        var url = URL.createObjectURL(this.$file),
            img = new Image;
            that = this;

        img.onload = $.proxy(function() {
            if(that.$source.image_type == 'option') {
                if(img.width < 180) {
                    this.validateError('dimension');
                    return;
                }

                if(img.height < 180) {
                    this.validateError('dimension');
                    return;
                }
            }
            else {
                if(img.width < 600) {
                    this.validateError('dimension');
                    return;
                }

                if(img.height < 325) {
                    this.validateError('dimension');
                    return;
                }
            }
            
            this.$upload_container.hide();
            this.$upload_image.attr('src', img.src);
            if(img.width <= this.$container.width())
                this.$container.width(img.width);    
            this.$upload_image_container.show();
            $("#quiz-image-new-no-attribution").trigger('click');

            this.startCropper();
        }, this);
        img.src = url;
    },

    validateError: function(type) {
        if(type == 'dimension' && this.$source.image_type == 'option')
            $("#quiz-image-upload-" + type + "-option-error").clone().appendTo(this.$upload_container);
        else
            $("#quiz-image-upload-" + type + "-error").clone().appendTo(this.$upload_container);
        this.$upload_container_title.hide();
        setTimeout($.proxy(function() {
            this.$upload_container_title.show();
            this.$upload_container.find(".quiz-image-upload-error").remove();
        }, this), 3000);
    },

    startCropper: function() {
        var that = this;

        this.$cropper_init = 1;
        this.$upload_image.cropper({
            aspectRatio: that.$source.image_type == 'option' ? 1 : (600/325),
            minCropBoxWidth: that.$source.image_type == 'option' ? 180 : 600,
            minCropBoxHeight: that.$source.image_type == 'option' ? 180 : 325,
            cropBoxResizable: false,
            strict: true,
            guides: false,
            crop: function(data) {
                var json = [
                      '{"x":' + data.x,
                      '"y":' + data.y,
                      '"height":' + data.height,
                      '"width":' + data.width,
                      '"rotate":' + data.rotate + '}'
                    ].join();

                that.$cropper_data = json;
            }
        });
    },

    validateAttribution: function() {
        var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
            attribution = { error: 0, text: '' };

        $("#quiz-image-new-attribution").removeClass('theme-form-text-error');

        if($("#quiz-image-new-no-attribution").is(":checked")) {
            attribution.text = -1;
        }
        else {
            if(!blank_reg_exp.test($("#quiz-image-new-attribution").val())) {
                $("#quiz-image-new-attribution").addClass('theme-form-text-error');
                attribution.error = 1;
            }
            else {
                attribution.text = $.trim($("#quiz-image-new-attribution").val());
            }
        }

        return attribution;
    },

    uploadImage: function(e) {
        if($(e.currentTarget).attr('data-in-progress') == 1) 
            return

        var attribution = this.validateAttribution(),
            that = this,
            fd;

        if(attribution.error == 1) 
            return;

        fd = new FormData();
        fd.append('post_image', this.$file);
        fd.append('image_data', this.$cropper_data);
        fd.append('target', this.$source.image_type);
        fd.append('post_type', 'QUIZ');

        $(e.currentTarget).attr('data-in-progress', 1).css('opacity', '0.6');
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-image.php',
            data: fd,
            dataType: 'json',
            success: function (response) {
                $(e.currentTarget).attr('data-in-progress', 0).css('opacity', '1');
                that.$source.attribution = attribution.text;

                that.hideLightbox();
                that.$quiz_object.imageUploaded(that.$source, response.image_info);
            },
            error: function(response) {
                $(e.currentTarget).attr('data-in-progress', 0).css('opacity', '1');
                that.$quiz_object.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            },
            processData: false,
            contentType: false
        });
    }
};

function NewQuiz(element, new_quiz, quiz_properties, quiz_data, premium_properties, show_premium) {
    this.$quiz_images_uploader = new QuizImagesUploader(this);
    if(new_quiz == 1) {
        this.$quiz_properties = { post_id: null, image_id: null, image_attribution: null, title: '', description: '', type: 1, language_code: LANGUAGE_CODE_CURRENT, tags: [] };
        this.$quiz_questions = [];
        this.$quiz_results = [ { image_id: null, image_attribution: null, title: '', description: '' }, 
                                { image_id: null, image_attribution: null, title: '', description: '' },
                                { image_id: null, image_attribution: null, title: '', description: '' },
                                { image_id: null, image_attribution: null, title: '', description: '' }
                            ];
        this.$quiz_social_media_image = { image_id: null, image_attribution: null };
        this.$current_question = null;
        this.$premium_properties = { was_premium: false, is_premium: 0, domain: null };
    }
    else if(new_quiz == 0) {
        this.$quiz_properties = quiz_properties;
        this.$quiz_questions = quiz_data['questions'];
        this.$quiz_results = quiz_data['results'];
        this.$quiz_social_media_image = quiz_data['social_media_image'];
        this.$current_question = null;
        this.$premium_properties = premium_properties;
    }

    this.$images_to_delete = [];
    this.$quiz_container = element;
    this.$quiz_to_save = 0;
    this.$delete_question_button = $('#quiz-delete-question');
    this.$questions_links_container = $('#quiz-questions-links');
    this.$quiz_question_container = $('#quiz-question-container');
    this.$question_options_container = $("#quiz-question-options-container");
    this.$delete_option_button = this.$quiz_container.find('.quiz-delete-option');

    this.init();
    this.loadPremiumContainer();
    $('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});

    if(new_quiz == 0)
        this.loadQuiz(show_premium);
}

NewQuiz.prototype = {
    init: function() {
        this.addEvents();
    },

    addEvents: function() {
        $(document).on('click', '.quiz-image-delete', $.proxy(function(e) {
            var source = this.getSource(e, $(e.currentTarget).closest('.quiz-image-uploaded-container').attr('data-image-type'));
            this.deleteImage(source);
        }, this));
        $(document).on('click', '.quiz-image-edit', $.proxy(function(e) {
            var source = this.getSource(e, $(e.currentTarget).closest('.quiz-image-uploaded-container').attr('data-image-type'));
            this.showEditImageLightbox(source);
        }, this));
        $("#quiz-image-old-save").on('click', $.proxy(this.editImageAttribution, this));
        $("#quiz-image-new-no-attribution, #quiz-image-old-no-attribution").on('click', function() {
            if($(this).is(":checked")) 
                $(this).parent().prev().find("input[type='text']").val('').attr('disabled', 'disabled');
            else 
                $(this).parent().prev().find("input[type='text']").removeAttr('disabled');
        });
        $("#quiz-image-attribution-lightbox-close").on('click', $.proxy(this.closeEditImageLightbox, this));
        $(document).on('click', '.quiz-image-placeholder', $.proxy(this.imageChange, this));
        $(document).on('click', '.quiz-image-upload', function(e) {
            $(e.currentTarget).parent().prev().click();
        });
        $(".quiz-tab").on('click', function(e) {
            $(".quiz-tab-content").hide();
            $(".quiz-tab").removeClass('theme-active-button').addClass('theme-passive-button');
            $(this).removeClass('theme-passive-button').addClass('theme-active-button');
            
            switch($(e.currentTarget).attr('id')) {
                case 'quiz-tab-info':
                    $("#quiz-info-container").show();
                    break;

                case 'quiz-tab-questions':
                    $("#quiz-questions-main-container").show();
                    break;

                case 'quiz-tab-results':
                    $("#quiz-results-container").show();
                    break;

                case 'quiz-tab-tags':
                    $("#quiz-social-tags-container").show();
                    break;

                case 'quiz-tab-premium':
                    $("#quiz-premium-container").show();
            }
        });
        $("#quiz-title").on('keyup paste', $.proxy(this.quizTitleChange, this));
        $("#quiz-description").on('keyup paste', $.proxy(this.quizDescriptionChange, this));
        $("#quiz-type").on('click', $.proxy(this.quizTypeChange, this));
        $("#quiz-language").on('change', $.proxy(this.quizLanguageChange, this));
        $('#quiz-add-question').on('click', $.proxy(this.addQuestion, this));
        $('#quiz-add-option-button').on('click', $.proxy(this.addOption, this));
        $(document).on('click', '.quiz-question-link', $.proxy(function(e) {
            $("#quiz-question-container").find(".theme-form-text-error").removeClass('theme-form-text-error');
            this.showQuestion(parseInt($(e.currentTarget).attr('data-question-no'), 10));
        }, this));
        $("#quiz-question").on('keyup paste', $.proxy(this.questionTextChange, this));
        $("#quiz-delete-question").on('click', $.proxy(this.removeQuestion, this));
        $(document).on('keyup paste', '.quiz-option', $.proxy(this.optionTextChange, this));
        $(document).on('click', '.quiz-option-correct-container label', function() {
            if($(this).next().prop('checked'))
                $(this).next().prop('checked', false).trigger('change');
            else 
                $(this).next().prop('checked', true).trigger('change');
        });
        $(document).on('change', '.quiz-option-correct', $.proxy(this.optionCorrectChange, this));
        $(document).on('keyup paste click', '.quiz-option-weight', $.proxy(this.optionWeightChange, this));
        $(document).on('click', '.quiz-delete-option', $.proxy(this.removeOption, this));
        $("#quiz-question-hint-button").on('click', $.proxy(this.questionHintShow, this));
        $("#quiz-question-hint-text").on('keyup paste', $.proxy(this.questionHintChange, this));
        $("#quiz-question-fact-button").on('click', $.proxy(this.questionFactShow, this));
        $("#quiz-question-fact-text").on('keyup paste', $.proxy(this.questionFactChange, this));
        $(".quiz-result-button").on('click', $.proxy(function(e) {
            $("#quiz-result-title").removeClass('theme-form-text-error'); 
            this.showQuizResult($(e.currentTarget).attr('data-result-no'));
        }, this));
        $("#quiz-result-title").on('keyup paste', $.proxy(this.quizResultTitleChange, this));
        $("#quiz-result-description").on('keyup paste', $.proxy(this.quizResultDescriptionChange, this));
        $(document).on('click', '.quiz-single-tag label', $.proxy(this.quizTagLabelToggle, this));
        $(document).on('click', '.quiz-single-tag input[type="checkbox"]', $.proxy(this.quizTagInputToggle, this));
        $("#publish-quiz-button").on('click', $.proxy(this.updateQuiz, this));
        $("#save-draft-button").on('click', $.proxy(this.saveDraft, this));
        $("#quiz-error-dialog-close").on('click', $.proxy(this.closeErrorDialog, this));
        $("#premium-activate-button").on('click', $.proxy(this.activatePremium, this));
        $("#premium-deactivate-button").on('click', $.proxy(this.deactivatePremium, this));
        $("#premium-domain-edit-button").on('click', $.proxy(this.premiumDomainEdit, this));
        $("#premium-domain-save-button").on('click', $.proxy(this.premiumDomainSave, this));
        $("#user-credits-refresh-button").on('click', $.proxy(this.getUserCredits, this));
        $(window).on('beforeunload', $.proxy(this.showUnsavedStatus, this));
        $(window).on('unload', $.proxy(this.deleteOrphanImages, this));
    },

    loadQuiz: function(show_premium) {
        if(this.$quiz_properties['image_id'] != null)
            $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', 'quiz').appendTo('.quiz-image').find('img').attr('src', LOCATION_SITE + 'img/QUIZ/quiz/' + this.$quiz_properties['image_id']);
        $("#quiz-title").val(this.$quiz_properties['title']);
        $("#quiz-title-textcount").text(this.$quiz_properties['title'].length + ' / 90');
        $("#quiz-description").val(this.$quiz_properties['description']);
        $("#quiz-description-textcount").text(this.$quiz_properties['description'].length + ' / 150');
        $("#quiz-type").val(this.$quiz_properties['type']);
        $("#quiz-language").val(this.$quiz_properties['language_code']);

        if(this.$quiz_questions.length > 0) {
            this.$questions_links_container.show();
            this.$quiz_question_container.show();
            for(var i=1; i<=this.$quiz_questions.length; i++) {
                $("#quiz-question-link-template button").clone().text(i).attr('data-question-no', i).appendTo(this.$questions_links_container)
            }
            this.showQuestion(1);
        }

        this.showQuizResult(1);

        if(this.$quiz_social_media_image['image_id'] != null)
            $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', 'social').appendTo('.quiz-social-media-image').find('img').attr('src', LOCATION_SITE + 'img/QUIZ/social/' + this.$quiz_social_media_image['image_id']);
    
        if(this.$quiz_properties.tags.length > 0) {
            for(i=0; i<this.$quiz_properties.tags.length; i++) {
                $("input[type='checkbox'][value='" + this.$quiz_properties.tags[i] + "']").prop('checked', true);
            }
        }

        if(show_premium == 1)
            $("#quiz-tab-premium").trigger('click');
    },

    loadPremiumContainer: function() {
        if(this.$premium_properties.is_premium == 0) {
            $("#premium-domain").val(null).removeAttr('disabled');
            $("#premium-domain-edit-button").hide();
            $("#premium-domain-save-button").hide();
            $("#premium-activate-button").show();
            $("#premium-deactivate-button").hide();
        }
        else {
            $("#premium-domain").val(this.$premium_properties.domain).attr('disabled', 'true');
            $("#premium-domain-edit-button").show();
            $("#premium-domain-save-button").hide();
            $("#premium-activate-button").hide();
            $("#premium-deactivate-button").show();
        }
    },

    getSource: function(e, image_type) {
        var source = { image_type: image_type, question_no: null, option_index: null, result_no: null };

        if(source.image_type == 'question') {
            source.question_no = this.$current_question;
        }
        else if(source.image_type == 'option') {
            source.question_no = this.$current_question;
            source.option_index = this.$question_options_container.find(".quiz-option-container").index($(e.currentTarget).closest(".quiz-option-container"));
        }
        else if(source.image_type == 'result') {
            source.result_no = $("#quiz-results-buttons-container").find('.theme-active-button-small').attr('data-result-no');
        }

        return source;
    },

    imageChange: function(e) {
        var source = this.getSource(e, $(e.currentTarget).attr('data-image-type'));

        this.$quiz_images_uploader.showLightbox(source);
    },

    imageUploaded: function(source, image_info) {
        this.$quiz_to_save = 1;

        var image_uploaded_container;

        switch(source.image_type) {
            case 'quiz':
                image_uploaded_container = $(".quiz-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1, 1);
                $(".quiz-image").removeClass('theme-form-text-error'); 
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', source.image_type).appendTo('.quiz-image').find('img').attr('src', LOCATION_SITE + image_info.image_destination);

                this.$quiz_properties.image_id = image_info.image_id;
                this.$quiz_properties.image_attribution = source.attribution;
                break;

            case 'question':
                image_uploaded_container = $(".question-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);
                $(".question-image").removeClass('theme-form-text-error'); 
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', source.image_type).appendTo('.question-image').find('img').attr('src', LOCATION_SITE + image_info.image_destination);

                this.$quiz_questions[source.question_no - 1]['image_id'] = image_info.image_id;
                this.$quiz_questions[source.question_no - 1]['image_attribution'] = source.attribution;
                break;

            case 'option':
                image_uploaded_container = this.$question_options_container.find(".option-image").eq(source.option_index).find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);
                this.$question_options_container.find(".option-image").eq(source.option_index).removeClass('theme-form-text-error'); 
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', source.image_type).appendTo(this.$question_options_container.find(".option-image").eq(source.option_index)).find('img').attr('src', LOCATION_SITE + image_info.image_destination);

                this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_id'] = image_info.image_id;
                this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_attribution'] = source.attribution;
                break;

            case 'result':
                image_uploaded_container = $(".quiz-result-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', source.image_type).appendTo('.quiz-result-image').find('img').attr('src', LOCATION_SITE + image_info.image_destination);

                this.$quiz_results[source.result_no - 1]['image_id'] = image_info.image_id;
                this.$quiz_results[source.result_no - 1]['image_attribution'] = source.attribution;
                break;

            case 'social':
                image_uploaded_container = $(".quiz-social-media-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', source.image_type).appendTo('.quiz-social-media-image').find('img').attr('src', LOCATION_SITE + image_info.image_destination);

                this.$quiz_social_media_image['image_id'] = image_info.image_id;
                this.$quiz_social_media_image['image_attribution'] = source.attribution;
                break;
        }
    },

    deleteImage: function(source) {
        this.$quiz_to_save = 1;

        var image_uploaded_container;

        switch(source.image_type) {
            case 'quiz':
                image_uploaded_container = $(".quiz-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1, 1);

                this.$quiz_properties.image_id = null;
                this.$quiz_properties.image_attribution = null;
                break;

            case 'question':
                image_uploaded_container = $(".question-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);

                this.$quiz_questions[source.question_no - 1]['image_id'] = null;
                this.$quiz_questions[source.question_no - 1]['image_attribution'] = null;
                break;

            case 'option':
                image_uploaded_container = $("#quiz-question-options-container").find(".option-image").eq(source.option_index).find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);

                this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_id'] = null;
                this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_attribution'] = null;
                break;

            case 'result':
                image_uploaded_container = $(".quiz-result-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);

                this.$quiz_results[source.result_no - 1]['image_id'] = null;
                this.$quiz_results[source.result_no - 1]['image_attribution'] = null;
                break;

            case 'social':
                image_uploaded_container = $(".quiz-social-media-image").find('.quiz-image-uploaded-container');
                this.updateImagesDeleted(image_uploaded_container, 1);

                this.$quiz_social_media_image['image_id'] = null;
                this.$quiz_social_media_image['image_attribution'] = null;
                break;
        }
    },

    updateImagesDeleted: function(image_uploaded_containers, delete_from_dom, has_thumbnail) {
        var that = this;

        image_uploaded_containers.each(function() {
            if(typeof has_thumbnail !== 'undefined') {
                var src = $(this).find('img').attr('src').split('/');
                src[src.length - 1] = 'm-' + src[src.length - 1];
                that.$images_to_delete.push(src.join('/'));
            }

            that.$images_to_delete.push($(this).find('img').attr('src'));
            if(delete_from_dom == 1)
                $(this).remove();
        });
    },

    showLightbox: function(source) {
        $("#quiz-images-lightbox").height($(document).height()).show();

        this.$source = source;
    },

    hideLightbox: function() {
        $("#quiz-images-upload-container").show();
        $("#quiz-image-new-container").hide();
        $("#quiz-image-new-attribution").val('');
        $("#quiz-image-new-no-attribution").prop('checked', false);
        this.$upload_image_container.css('width', '100%');
        if(this.$cropper_init == 1) {
            this.$upload_image.cropper('destroy');
            this.$cropper_init = 0;
            this.$container.width(this.$quiz_container.width());
        }

        $("#quiz-images-lightbox").hide();
    },

    showEditImageLightbox: function(source) {
        $("#quiz-image-attribution-lightbox").height($(document).height()).show();

        var image_src,
            attribution,
            data_source = -1;

        switch(source.image_type) {
            case 'quiz':
                image_src = LOCATION_SITE + 'img/QUIZ/' + source.image_type + '/' + this.$quiz_properties.image_id;
                attribution = this.$quiz_properties.image_attribution;
                break;

            case 'question':
                image_src = LOCATION_SITE + 'img/QUIZ/' + source.image_type + '/' + this.$quiz_questions[source.question_no - 1]['image_id'];
                attribution = this.$quiz_questions[source.question_no - 1]['image_attribution'];
                break;

            case 'option':
                image_src = LOCATION_SITE + 'img/QUIZ/' + source.image_type + '/' + this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_id'];
                attribution = this.$quiz_questions[source.question_no - 1]['options'][source.option_index]['image_attribution'];
                data_source = source.option_index;
                break;

            case 'result':
                image_src = LOCATION_SITE + 'img/QUIZ/' + source.image_type + '/' + this.$quiz_results[source.result_no - 1]['image_id'];
                attribution = this.$quiz_results[source.result_no - 1]['image_attribution'];
                data_source = source.result_no;
                break;

            case 'social':
                image_src = LOCATION_SITE + 'img/QUIZ/' + source.image_type + '/' + this.$quiz_social_media_image['image_id'];
                attribution = this.$quiz_social_media_image['image_attribution'];
                break;
        }

        $("#quiz-image-old").attr('src', image_src);
        $("#quiz-image-old-save").attr('data-image-type', source.image_type).attr('data-source', data_source);
        if(attribution == -1) {
            $("#quiz-image-old-no-attribution").prop('checked', true);
            $("#quiz-image-old-attribution").val('').attr('disabled', 'disabled');
        }
        else {
            $("#quiz-image-old-no-attribution").prop('checked', false);
            $("#quiz-image-old-attribution").val(attribution).removeAttr('disabled');
        }
    },

    closeEditImageLightbox: function() {
        $("#quiz-image-attribution-lightbox").height($(document).height()).hide();
        $("#quiz-image-old").removeAttr('src');
        $("#quiz-image-old-attribution").removeClass('theme-form-text-error')
        $("#quiz-image-old-save").removeAttr('data-image-type').removeAttr('data-source');
    },

    editImageAttribution: function(e) {
        this.$quiz_to_save = 1;

        var image_type = $(e.currentTarget).attr('data-image-type'),
            data_source = $(e.currentTarget).attr('data-source'),
            attribution,
            blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/;

        $("#quiz-image-old-attribution").removeClass('theme-form-text-error');
        if($("#quiz-image-old-no-attribution").is(":checked")) {
            attribution = -1;
        }
        else {
            if(!blank_reg_exp.test($("#quiz-image-old-attribution").val())) {
                $("#quiz-image-old-attribution").addClass('theme-form-text-error');
                return;
            }
            else {
                attribution = $.trim($("#quiz-image-old-attribution").val());
            }
        }

        switch(image_type) {
            case 'quiz':
                this.$quiz_properties.image_attribution = attribution;
                break;

            case 'question':
                this.$quiz_questions[this.$current_question - 1]['image_attribution'] = attribution;
                break;

            case 'option':
                this.$quiz_questions[this.$current_question - 1]['options'][data_source]['image_attribution'] = attribution;
                break;

            case 'result':
                this.$quiz_results[data_source - 1]['image_attribution'] = attribution;
                break;

            case 'social':
                this.$quiz_social_media_image['image_attribution'] = attribution;
                break;
        }

        $("#quiz-image-attribution-lightbox-close").trigger('click');
    },

    quizTitleChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_properties.title = $.trim($(e.currentTarget).val());
        $("#quiz-title-textcount").text(this.$quiz_properties.title.length + ' / 90');
    },

    quizDescriptionChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_properties.description = $.trim($(e.currentTarget).val());
        $("#quiz-description-textcount").text(this.$quiz_properties.description.length + ' / 150');
    },

    quizTypeChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_properties.type = $(e.currentTarget).val();

        if(this.$quiz_properties.type == 1) {
            $("#quiz-question-options-container").find(".quiz-option-correct-container").show();
            $("#quiz-question-options-container").find(".quiz-option-weight").hide();

            $("#quiz-option-correct-instruction").show();
            $("#quiz-option-weight-instruction").hide();
        }
        else if(this.$quiz_properties.type == 2) {
            $("#quiz-question-options-container").find(".quiz-option-correct-container").hide();
            $("#quiz-question-options-container").find(".quiz-option-weight").show();

            $("#quiz-option-correct-instruction").hide();
            $("#quiz-option-weight-instruction").show();
        }
    },

    quizLanguageChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_properties.language_code = $(e.currentTarget).val();
        var direction = $("#quiz-language option:selected").attr('data-direction');

        if(direction == 'ltr') {
            $("body").css('direction', 'ltr').removeClass('rtl-language');
            this.$quiz_container.removeClass('quiz-container-rtl');
        }
        else if(direction == 'rtl') {
            $("body").css('direction', 'rtl').addClass('rtl-language');;
            this.$quiz_container.addClass('quiz-container-rtl');  
        }

        this.updateLanguageTags();
    },

    updateLanguageTags: function() {
        $(".quiz-single-tag").remove();
        this.$quiz_properties.tags = [];

        var $post = { post_type: 'QUIZ', language_code: this.$quiz_properties.language_code };

        $("#quiz-language").attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=GetLanguageTags',
            data: $post,
            cache: false,
            dataType: 'JSON',
            success: function(response) { 
                $("#quiz-language").removeAttr('disabled');

                var html = '';
                for(var i=0; i<response.language_tags.length; i++)
                    html += '<div class="quiz-single-tag"><input type="checkbox" value="' + response.language_tags[i]['id'] + '" autocomplete="off" /><label>' + response.language_tags[i]['name'] + '</label></div>';
                $("#quiz-tags").html(html);
            },
            error: function(response) {
                $("#quiz-language").removeAttr('disabled');
                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    questionHintShow: function() {
        $("#quiz-question-hint-button").hide();
        $("#quiz-question-hint-textbox-container").show();
    },

    questionHintChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_questions[this.$current_question - 1]['hint'] = $.trim($(e.currentTarget).val());
        $("#quiz-question-hint-textcount").text(this.$quiz_questions[this.$current_question - 1]['hint'].length + ' / 150');
    },

    questionFactShow: function() {
        $("#quiz-question-fact-button").hide();
        $("#quiz-question-fact-textbox-container").show();
    },

    questionFactChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_questions[this.$current_question - 1]['fact'] = $.trim($(e.currentTarget).val());
        $("#quiz-question-fact-textcount").text(this.$quiz_questions[this.$current_question - 1]['fact'].length + ' / 150');
    },

    addQuestion: function() {
        this.$quiz_to_save = 1;

        if(this.$quiz_questions.length == 18) {
            this.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [LANGUAGE_STRINGS['MAX_18_QUESTIONS']]);
            return;
        }

        this.$quiz_questions.push({ image_id: null, image_attribution: null, text: '', options: [], hint: '', fact: '' });

        var question_link = $("#quiz-question-link-template button").clone().text(this.$quiz_questions.length).attr('data-question-no', this.$quiz_questions.length).appendTo(this.$questions_links_container);

        if(this.$quiz_questions.length == 1) {
            this.$questions_links_container.show();
            this.$quiz_question_container.show();
        }

        this.showQuestion(this.$quiz_questions.length);
    },

    showQuestion: function(question_no) {
        var question = this.$quiz_questions[question_no - 1],
            option_element;

        this.$current_question = question_no;

        this.$questions_links_container.find('button').removeClass('theme-active-button-small').addClass('theme-passive-button-small');
        this.$questions_links_container.find('button[data-question-no="' + question_no + '"]').removeClass('theme-passive-button-small').addClass('theme-active-button-small');

        $(".question-image .quiz-image-uploaded-container").remove();
        $("#quiz-question-options-container").html('');
        if(question.image_id != null)
            $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', 'question').appendTo('.question-image').find('img').attr('src', LOCATION_SITE + 'img/QUIZ/question/' + question.image_id);
        $("#quiz-question").val(question.text);
        $("#quiz-question-textcount").text(question.text.length + ' / 180');

        if(question.options.length > 0) {
            $("#quiz-option-instruction").show();
            if(this.$quiz_properties.type == 1) {
                $("#quiz-option-correct-instruction").show();
                $("#quiz-option-weight-instruction").hide();
            }
            else if(this.$quiz_properties.type == 2) {
                $("#quiz-option-correct-instruction").hide();
                $("#quiz-option-weight-instruction").show();
            }
        }
        else {
            $("#quiz-option-instruction").hide();
        }

        for(var i=0; i<question.options.length; i++) {
            option_element = $("#quiz-option-template").find('.quiz-option-container').clone().appendTo('#quiz-question-options-container');

            if(question.options[i].image_id != null)
                $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', 'option').appendTo(option_element.find(".option-image")).find('img').attr('src', LOCATION_SITE + 'img/QUIZ/option/' + question.options[i].image_id);
            option_element.find(".quiz-option").val(question.options[i].text);
            option_element.find(".quiz-option-textcount").text(question.options[i].text.length + ' / 120');
            if(this.$quiz_properties.type == 1)
                option_element.find(".quiz-option-correct-container").show().first().find(".quiz-option-correct").prop('checked', question.options[i].correct);
            else if(this.$quiz_properties.type == 2)
                option_element.find(".quiz-option-weight").show().val(question.options[i].weight);
        }

        if(question.hint == '') {
            $("#quiz-question-hint-button").show();
            $("#quiz-question-hint-textbox-container").hide();
            $("#quiz-question-hint-text").val('');
        }
        else {
            $("#quiz-question-hint-button").hide();
            $("#quiz-question-hint-textbox-container").show();
            $("#quiz-question-hint-text").val(question.hint);
            $("#quiz-question-hint-textcount").text(question.hint.length + ' / 150');   
        }

        if(question.fact == '') {
            $("#quiz-question-fact-button").show();
            $("#quiz-question-fact-textbox-container").hide();
            $("#quiz-question-fact-text").val('');
        }
        else {
            $("#quiz-question-fact-button").hide();
            $("#quiz-question-fact-textbox-container").show();
            $("#quiz-question-fact-text").val(question.fact); 
            $("#quiz-question-fact-textcount").text(question.fact.length + ' / 150');  
        }
    },

    removeQuestion: function() {
        this.$quiz_to_save = 1;

        this.updateImagesDeleted($("#quiz-question-container").find(".quiz-image-uploaded-container"), 0);

        this.$quiz_questions.splice(this.$current_question - 1, 1);
        this.refreshQuestionLinks();

        if(this.$quiz_questions.length == 0) {
            this.$quiz_question_container.hide();
            this.$questions_links_container.show();
        }
        else if(this.$quiz_questions.length == 1) {
            this.showQuestion(1);
        }
        else {
            if(this.$current_question == 1)
                this.showQuestion(this.$current_question + 1);
            else 
                this.showQuestion(this.$current_question - 1);
        }
    },

    questionTextChange: function(e) {
        this.$quiz_to_save = 1;

        this.$quiz_questions[this.$current_question - 1]['text'] = $.trim($(e.currentTarget).val());
        $("#quiz-question-textcount").text(this.$quiz_questions[this.$current_question - 1]['text'].length + ' / 180');
    },

    addOption: function() {
        this.$quiz_to_save = 1;

        if(this.$quiz_questions[this.$current_question - 1]['options'].length == 5) {
            this.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [LANGUAGE_STRINGS['MAX_5_OPTIONS']]);
            return;
        }
        
        this.$quiz_questions[this.$current_question - 1]['options'].push({ image_id: null, image_attribution: null, text: '', correct: 0, weight: '' });
        var option_element = $("#quiz-option-template").find('.quiz-option-container').clone().appendTo('#quiz-question-options-container');

        if(this.$quiz_properties.type == 1)
            option_element.find(".quiz-option-correct-container").show();
        else if(this.$quiz_properties.type == 2)
            option_element.find(".quiz-option-weight").show();

        if(this.$quiz_questions[this.$current_question - 1]['options'].length == 1) {
            $("#quiz-option-instruction").show();
            if(this.$quiz_properties.type == 1) {
                $("#quiz-option-correct-instruction").show();
                $("#quiz-option-weight-instruction").hide();
            }
            else if(this.$quiz_properties.type == 2) {
                $("#quiz-option-correct-instruction").hide();
                $("#quiz-option-weight-instruction").show();
            }
        }
    },

    refreshQuestionLinks: function() {
        this.$questions_links_container.html('');

        for(var i=1; i<=this.$quiz_questions.length; i++) {
            $("#quiz-question-link-template button").clone().text(i).attr('data-question-no', i).appendTo(this.$questions_links_container);
        }
    },

    removeOption: function(e) {
        this.$quiz_to_save = 1;

        this.updateImagesDeleted($(e.currentTarget).closest(".quiz-option-container").find(".quiz-image-uploaded-container"), 0);

        var option_index = this.$question_options_container.find(".quiz-option-container").index($(e.currentTarget).closest(".quiz-option-container"));
        
        this.$quiz_questions[this.$current_question - 1]['options'].splice(option_index, 1);

        $(e.currentTarget).closest(".quiz-option-container").remove();

        if(this.$quiz_questions[this.$current_question - 1]['options'].length == 0) {
            $("#quiz-option-instruction").hide();
        }
    },

    optionTextChange: function(e) {
        this.$quiz_to_save = 1;

        var option_index = this.$question_options_container.find(".quiz-option-container").index($(e.currentTarget).closest(".quiz-option-container"));

        this.$quiz_questions[this.$current_question - 1]['options'][option_index]['text'] = $.trim($(e.currentTarget).val());
        this.$question_options_container.find(".quiz-option-container").eq(option_index).find('.quiz-option-textcount').text(this.$quiz_questions[this.$current_question - 1]['options'][option_index]['text'].length + ' / 120');
    },

    optionCorrectChange: function(e) {
        this.$quiz_to_save = 1;

        var option_index = this.$question_options_container.find(".quiz-option-container").index($(e.currentTarget).closest(".quiz-option-container")),
            option_correct = $(e.currentTarget).prop('checked') == true ? 1 : 0;

        if($(e.currentTarget).prop('checked'))
            this.$question_options_container.find(".quiz-option-correct").not(e.currentTarget).prop('checked', false);

        for(var i=0; i<this.$quiz_questions[this.$current_question - 1]['options'].length; i++) {
            if(i == option_index)
                this.$quiz_questions[this.$current_question - 1]['options'][i]['correct'] = option_correct;
            else 
                this.$quiz_questions[this.$current_question - 1]['options'][i]['correct'] = 0;
        }
    },

    optionWeightChange: function(e) {
        this.$quiz_to_save = 1;

        var option_index = this.$question_options_container.find(".quiz-option-container").index($(e.currentTarget).closest(".quiz-option-container"));

        this.$quiz_questions[this.$current_question - 1]['options'][option_index]['weight'] = $(e.currentTarget).val();
    },

    showQuizResult: function(result_no) {
        $("#quiz-results-buttons-container button").removeClass('theme-active-button-small').addClass('theme-passive-button-small');
        $(".quiz-result-button[data-result-no='" + result_no + "']").removeClass('theme-passive-button-small').addClass('theme-active-button-small');

        $(".quiz-result-image .quiz-image-uploaded-container").remove();
        if(this.$quiz_results[result_no - 1].image_id != null)
            $("#quiz-image-uploaded-container-template").find('.quiz-image-uploaded-container').clone().attr('data-image-type', 'result').appendTo('.quiz-result-image').find('img').attr('src', LOCATION_SITE + 'img/QUIZ/result/' + this.$quiz_results[result_no - 1].image_id);

        $("#quiz-result-title").val(this.$quiz_results[result_no - 1].title);
        $("#quiz-result-title-textcount").text(this.$quiz_results[result_no - 1].title.length + ' / 100');

        $("#quiz-result-description").val(this.$quiz_results[result_no - 1].description);
        $("#quiz-result-description-textcount").text(this.$quiz_results[result_no - 1].description.length + ' / 200');
    },

    quizResultTitleChange: function(e) {
        this.$quiz_to_save = 1;

        var result_no = $("#quiz-results-buttons-container").find('.theme-active-button-small').attr('data-result-no');

        this.$quiz_results[result_no - 1].title = $.trim($(e.currentTarget).val());
        $("#quiz-result-title-textcount").text(this.$quiz_results[result_no - 1].title.length + ' / 100');
    },

    quizResultDescriptionChange: function(e) {
        this.$quiz_to_save = 1;

        var result_no = $("#quiz-results-buttons-container").find('.theme-active-button-small').attr('data-result-no');

        this.$quiz_results[result_no - 1].description = $(e.currentTarget).val();
        $("#quiz-result-description-textcount").text(this.$quiz_results[result_no - 1].description.length + ' / 200');
    },

    quizTagLabelToggle: function(e) {
        this.$quiz_to_save = 1;

        if($(e.currentTarget).prev().is(":checked")) {
            $(e.currentTarget).prev().prop('checked', false);
            this.$quiz_properties.tags.splice(this.$quiz_properties.tags.indexOf($(e.currentTarget).prev().val()), 1);
        }
        else {
            if($("#quiz-tags input[type='checkbox']:checked").length < 3) {
                $(e.currentTarget).prev().prop('checked', true);
                this.$quiz_properties.tags.push($(e.currentTarget).prev().val());
            }
        }
    },

    quizTagInputToggle: function(e) {
        this.$quiz_to_save = 1;

        if($(e.currentTarget).is(":checked")) {
            if($("#quiz-tags input[type='checkbox']:checked").length > 3)
                $(e.currentTarget).prop('checked', false);
            else
                this.$quiz_properties.tags.push($(e.currentTarget).val());
        }
        else {
            this.$quiz_properties.tags.splice(this.$quiz_properties.tags.indexOf($(e.currentTarget).val()), 1);
        } 
    },

    updateQuiz: function() {
        if($("#publish-quiz-button").attr('data-in-progress') == 1)
            return;

        $("#quiz-container").find(".theme-form-text-error").removeClass('theme-form-text-error');

        var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
            digits_reg_exp = /^[0-9]{1,}$/,
            errors = [],
            error_dialog_title,
            that = this,
            $post,
            validateQuizProperties = function() {
                if(that.$quiz_properties.image_id == null) {
                    errors.push(LANGUAGE_STRINGS['QUIZ_PICTURE_EMPTY']);
                    $(".quiz-image").addClass('theme-form-text-error');
                }

                if(!blank_reg_exp.test(that.$quiz_properties.title)) {
                    errors.push(LANGUAGE_STRINGS['QUIZ_TITLE_EMPTY']);
                    $("#quiz-title").addClass('theme-form-text-error');
                }

                if(!blank_reg_exp.test(that.$quiz_properties.description)) {
                    errors.push(LANGUAGE_STRINGS['QUIZ_DESCRIPTION_EMPTY']);
                    $("#quiz-description").addClass('theme-form-text-error');
                }

                if(errors.length > 0)
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND'];
            },
            validateQuizQuestionsCount = function() {
                if(that.$quiz_questions.length < 2) {
                    errors.push(LANGUAGE_STRINGS['MIN_2_QUESTIONS']);
                }

                if(errors.length > 0)
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND'];
            },
            validateQuizQuestionProperties = function(question_index) {
                if(that.$quiz_questions[question_index]['image_id'] == null && !blank_reg_exp.test(that.$quiz_questions[question_index]['text'])) {
                    errors.push(LANGUAGE_STRINGS['REQUIRES_ATLEAST_PIC_OR_TEXT']);
                    $(".question-image").addClass('theme-form-text-error');
                    $("#quiz-question").addClass('theme-form-text-error');
                }

                if(that.$quiz_questions[question_index]['options'].length < 2) {
                    errors.push(LANGUAGE_STRINGS['MIN_2_OPTIONS']);
                }

                if(errors.length > 0) {
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND_IN_QUESTION'] + (question_index + 1);
                    that.showQuestion(question_index + 1);
                }
            },
            validateQuizQuestionOptions = function(question_index) {
                that.showQuestion(question_index + 1);

                var error_message,
                    no_correct_option_given = 1;

                if(that.$quiz_properties.type == 1) {
                    for(var i=0; i<that.$quiz_questions[question_index]['options'].length; i++) {
                        if(that.$quiz_questions[question_index]['options'][i]['correct'] == 1) {
                            no_correct_option_given = 0;
                            break;
                        }
                    }
                    if(no_correct_option_given == 1) {
                       errors.push(LANGUAGE_STRINGS['NO_CORRECT_OPTION_GIVEN']); 
                    }
                }

                for(i=0; i<that.$quiz_questions[question_index]['options'].length; i++) {
                    error_message = '';

                    if(that.$quiz_questions[question_index]['options'][i]['image_id'] == null && !blank_reg_exp.test(that.$quiz_questions[question_index]['options'][i]['text'])) {
                        error_message += LANGUAGE_STRINGS['REQUIRES_ATLEAST_PIC_OR_TEXT'];
                        $("#quiz-question-options-container").find(".quiz-option-container").eq(i).find(".option-image").addClass('theme-form-text-error');
                        $("#quiz-question-options-container").find(".quiz-option-container").eq(i).find(".quiz-option").addClass('theme-form-text-error');
                    }

                    if(that.$quiz_properties.type == 2) {
                        if(!digits_reg_exp.test(that.$quiz_questions[question_index]['options'][i]['weight'])) {
                            if(error_message != '')
                                error_message += '; ';
                            error_message += LANGUAGE_STRINGS['WEIGHT_SHOULD_BE_NUMBER'];
                            $("#quiz-question-options-container").find(".quiz-option-container").eq(i).find(".quiz-option-weight").addClass('theme-form-text-error');
                        }
                    }

                    if(error_message != '')
                        errors.push(LANGUAGE_STRINGS['OPTION'] + (i + 1) + ' : ' + error_message);
                }

                if(errors.length > 0) {
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND_IN_QUESTION'] + (question_index + 1);
                }
            },
            validateQuizResult = function(result_index) {
                var result_button_labels = [];
                $(".quiz-result-button").each(function() {
                    result_button_labels.push($(this).text());
                });

                if(!blank_reg_exp.test(that.$quiz_results[result_index]['title'])) {
                    errors.push(LANGUAGE_STRINGS['RESULT_TITLE_EMPTY']);
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND_IN_RESULT'] + '"' + result_button_labels[result_index] + '"';
                    $("#quiz-result-title").addClass('theme-form-text-error'); 
                    that.showQuizResult(result_index + 1);
                }
            },
            validateQuizTagsCount = function() {
                if(that.$quiz_properties.tags.length == 0) {
                    errors.push(LANGUAGE_STRINGS['MIN_1_TAG']);
                    error_dialog_title = LANGUAGE_STRINGS['ERRORS_FOUND_TAGS'];
                }
            };

        validateQuizProperties();
        if(errors.length != 0) {
            this.showErrorDialog(error_dialog_title, errors);
            return;
        }

        validateQuizQuestionsCount();
        if(errors.length != 0) {
            this.showErrorDialog(error_dialog_title, errors);
            return;
        }

        for(var i=0; i<that.$quiz_questions.length; i++) {
            validateQuizQuestionProperties(i);
            if(errors.length != 0) {
                this.showErrorDialog(error_dialog_title, errors);
                return;
            }

            validateQuizQuestionOptions(i);
            if(errors.length != 0) {
                this.showErrorDialog(error_dialog_title, errors);
                return;
            }
        }

        for(var i=0; i<that.$quiz_results.length; i++) {
            validateQuizResult(i);
            if(errors.length != 0) {
                this.showErrorDialog(error_dialog_title, errors);
                return;
            }
        }

        validateQuizTagsCount();
        if(errors.length != 0) {
            this.showErrorDialog(error_dialog_title, errors);
            return;
        }

        $("#publish-quiz-button").attr('data-in-progress', 1).append($("#quiz-button-loader-template i").clone());
        $post = {
            post_type: 'QUIZ',
            post_properties: this.$quiz_properties,
            post_data: JSON.stringify( { questions: this.$quiz_questions, results: this.$quiz_results, social_media_image: this.$quiz_social_media_image } ),
            images_to_delete: this.$images_to_delete
        };

        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=PublishPost',
            cache: false,
            data: $post,
            dataType: 'JSON',
            success: function(response) { 
                $("#publish-quiz-button").attr('data-in-progress', 0).find('i').remove();

                if('post_id' in response) {
                    that.$quiz_properties['post_id'] = response.post_id;
                    if(PRETTY_URLS == 0)
                        history.replaceState({}, null, (IS_DEFAULT_LANGUAGE == 1 ? '?post_id=' + response.post_id : '?language_code=' + LANGUAGE_CODE_CURRENT + '&post_id=' + response.post_id));
                    else
                        history.replaceState({}, null, LOCATION_SITE + (IS_DEFAULT_LANGUAGE == 1 ? '' : LANGUAGE_CODE_CURRENT + '/') + 'user-quiz/' + response.post_id);
                }

                that.$images_to_delete = [];
                that.$quiz_to_save = 0;

                $("#quiz-container-title-label").text(that.$quiz_properties.title.toUpperCase());
                $("#quiz-draft-mode-label").hide();

                $("#publish-quiz-button-1").hide();
                $("#publish-quiz-button-2").show();
                $("#save-draft-button").hide();
            },
            error: function(response) {
                $("#publish-quiz-button").attr('data-in-progress', 0).find('i').remove();

                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    saveDraft: function() {
        if($("#save-draft-button").attr('data-in-progress') == 1)
            return;

        $("#save-draft-button").attr('data-in-progress', 1).append($("#quiz-button-loader-template i").clone());
        var $post = {
                post_type: 'QUIZ',
                post_properties: this.$quiz_properties,
                post_data: JSON.stringify( { questions: this.$quiz_questions, results: this.$quiz_results, social_media_image: this.$quiz_social_media_image } ),
                images_to_delete: this.$images_to_delete
            },
            that = this;

        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=SaveDraft',
            cache: false,
            data: $post,
            dataType: 'JSON',
            success: function(response) { 
                $("#save-draft-button").attr('data-in-progress', 0).find('i').remove();

                if('post_id' in response) {
                    that.$quiz_properties['post_id'] = response.post_id;
                    if(PRETTY_URLS == 0)
                        history.replaceState({}, null, (IS_DEFAULT_LANGUAGE == 1 ? '?post_id=' + response.post_id : '?language_code=' + LANGUAGE_CODE_CURRENT + '&post_id=' + response.post_id));
                    else
                        history.replaceState({}, null, LOCATION_SITE + (IS_DEFAULT_LANGUAGE == 1 ? '' : LANGUAGE_CODE_CURRENT + '/') + 'user-quiz/' + response.post_id);
                }

                that.$images_to_delete = [];
                that.$quiz_to_save = 0;

                $("#quiz-container-title-label").text(that.$quiz_properties.title == '' ? LANGUAGE_STRINGS['UNTITLED_QUIZ_LABEL'].toUpperCase() : that.$quiz_properties.title.toUpperCase());
                $("#quiz-draft-mode-label").show();
            },
            error: function(response) {
                $("#save-draft-button").attr('data-in-progress', 0).find('i').remove();

                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    showErrorDialog: function(error_dialog_title, errors) {
        $("#quiz-error-dialog-container").height($(document).height()).show();
        $("#quiz-error-dialog-title").text(error_dialog_title);

        var ul = '';
        for(var i=0; i<errors.length; i++) {
            ul += '<li>' + errors[i] + '</li>';
        }
        $("#quiz-error-dialog-list").html(ul);
    },

    closeErrorDialog: function() {
        $("#quiz-error-dialog-container").hide();
    },

    activatePremium: function() {
        if($("#premium-activate-button").attr('data-in-progress') == 1)
            return;

        var url_reg_exp = /^(http:\/\/|https:\/\/){0,1}(www\.){0,1}(([\w-]{1,}(?:([\.]{1}[a-zA-Z-]{2,}))+))(?:([\/]{1}[\w\?\=\-\)\(\&\%\$\_\.]{0,})){0,}$/i,
            $post,
            that = this;

        if(!url_reg_exp.test($.trim($("#premium-domain").val())) && $.trim($("#premium-domain").val()) != 'localhost') {
            this.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [LANGUAGE_STRINGS['INVALID_DOMAIN_FORMAT']]);
            return;
        }

        if(confirm(LANGUAGE_STRINGS['CONFIRM_QUIZ_PREMIUM_ACTIVATE']) == false)
            return

        $post = { premium_domain: $.trim($("#premium-domain").val()), post_id: this.$quiz_properties['post_id'], post_type: 'QUIZ' };
        if(this.$quiz_properties['post_id'] == null) {
            $post.post_properties = this.$quiz_properties;
            $post.post_data = JSON.stringify( { questions: this.$quiz_questions, results: this.$quiz_results, social_media_image: this.$quiz_social_media_image } );
            $post.images_to_delete = this.$images_to_delete;
        }   

        $("#premium-activate-button").attr('data-in-progress', 1).append($("#quiz-button-loader-template i").clone());
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=ActivatePremium',
            cache: false,
            data: $post,
            dataType: 'JSON',
            success: function(response) { 
                $("#premium-activate-button").attr('data-in-progress', 0).find('i').remove();

                if('post_id' in response) {
                    that.$quiz_properties['post_id'] = response.post_id;
                    
                    if(PRETTY_URLS == 0)
                        history.replaceState({}, null, (IS_DEFAULT_LANGUAGE == 1 ? '?post_id=' + response.post_id : '?language_code=' + LANGUAGE_CODE_CURRENT + '&post_id=' + response.post_id));
                    else
                        history.replaceState({}, null, LOCATION_SITE + (IS_DEFAULT_LANGUAGE == 1 ? '' : LANGUAGE_CODE_CURRENT + '/') + 'user-quiz/' + response.post_id);

                    $("#quiz-draft-mode-label").show();
                    $("#quiz-container-title-label").text(that.$quiz_properties.title == '' ? LANGUAGE_STRINGS['UNTITLED_QUIZ_LABEL'].toUpperCase() : that.$quiz_properties.title.toUpperCase());
                }

                $("#user-credits-remaining-count").text(response.credits_remaining);
                $("#quiz-premium-label").show();

                that.$premium_properties.was_premium = that.$premium_properties.is_premium = 1;
                that.$premium_properties.domain = $.trim($("#premium-domain").val());
                that.loadPremiumContainer();
            },
            error: function(response) {
                $("#premium-activate-button").attr('data-in-progress', 0).find('i').remove();
                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    deactivatePremium: function() {
        if($("#premium-deactivate-button").attr('data-in-progress') == 1)
            return;

        if(confirm(LANGUAGE_STRINGS['CONFIRM_QUIZ_PREMIUM_DEACTIVATE']) == false)
            return

        var $post = { post_id: this.$quiz_properties['post_id'], post_type: 'QUIZ' },
            that = this;

        $("#premium-deactivate-button").attr('data-in-progress', 1).append($("#quiz-button-loader-template i").clone());
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=DeactivatePremium',
            cache: false,
            data: $post,
            dataType: 'JSON',
            success: function(response) { 
                $("#premium-deactivate-button").attr('data-in-progress', 0).find('i').remove();

                $("#quiz-premium-label").hide();

                that.$premium_properties.was_premium = that.$premium_properties.is_premium = 0;
                that.$premium_properties.domain = null;
                that.loadPremiumContainer();
            },
            error: function(response) {
                $("#premium-deactivate-button").attr('data-in-progress', 0).find('i').remove();
                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });

        return false;
    },

    premiumDomainEdit: function() {
        $("#premium-domain").removeAttr('disabled');
        $("#premium-domain-edit-button").hide();
        $("#premium-domain-save-button").show();
    },

    premiumDomainSave: function() {
        if($("#premium-domain-save-button").attr('data-in-progress') == 1)
            return;

        var url_reg_exp = /^(http:\/\/|https:\/\/){0,1}(www\.){0,1}(([\w-]{1,}(?:([\.]{1}[a-zA-Z-]{2,}))+))(?:([\/]{1}[\w\?\=\-\)\(\&\%\$\_\.]{0,})){0,}$/i,
            $post,
            that = this;

        if(!url_reg_exp.test($.trim($("#premium-domain").val())) && $.trim($("#premium-domain").val()) != 'localhost') {
            this.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [LANGUAGE_STRINGS['INVALID_DOMAIN_FORMAT']]);
            return;
        }

        $post = { premium_domain: $.trim($("#premium-domain").val()), post_id: this.$quiz_properties['post_id'], post_type: 'QUIZ' };   

        $("#premium-domain-save-button").attr('data-in-progress', 1).css('opacity', '0.6');
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=PremiumDomainEdit',
            cache: false,
            data: $post,
            dataType: 'JSON',
            success: function(response) { 
                $("#premium-domain-save-button").attr('data-in-progress', 0).css('opacity', '1');

                that.$premium_properties.domain = $.trim($("#premium-domain").val());
                that.loadPremiumContainer();
            },
            error: function(response) {
                $("#premium-domain-save-button").attr('data-in-progress', 0).css('opacity', '1');
                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    getUserCredits: function() {
        if($("#user-credits-refresh-button").attr('data-in-progress') == 1)
            return;

        var $post = { post_type: 'QUIZ' },
            that = this;

        $("#user-credits-refresh-button").attr('data-in-progress', 1).find('i').addClass('fa-spin');
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=GetUserCredits',
            data: $post,
            cache: false,
            dataType: 'JSON',
            success: function(response) { 
                $("#user-credits-refresh-button").attr('data-in-progress', 0).find('i').removeClass('fa-spin');

                $("#user-credits-remaining-count").text(response.credits_remaining);
            },
            error: function(response) {
                $("#user-credits-refresh-button").attr('data-in-progress', 0).find('i').removeClass('fa-spin');
                that.showErrorDialog(LANGUAGE_STRINGS['ERRORS_DIALOG_TITLE'], [response.responseJSON.message]);
            }
        });
    },

    deleteOrphanImages: function() {
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/save-post.php?command=DeleteOrphanImages',
            cache: false,
            data: { post_type: 'QUIZ' },
            dataType: 'JSON',
            success: function(response) { 
    
            },
            error: function(response) {
                
            }
        });
    },

    showUnsavedStatus: function(e) {
        if(this.$quiz_to_save == 1) {
            e.returnValue = LANGUAGE_STRINGS['QUIZ_UNSAVED_MESSAGE'];;
            return LANGUAGE_STRINGS['QUIZ_UNSAVED_MESSAGE'];
        }
    }
};