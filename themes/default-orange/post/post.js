function PlayQuiz(quiz_properties, quiz_data, premium_properties) {
    this.$quiz_properties = quiz_properties;
    this.$quiz_questions = quiz_data['questions'];
    this.$quiz_results = quiz_data['results'];
    this.$quiz_social_media_image = quiz_data['social_media_image'];
    this.$current_question = null;
    this.$premium_properties = premium_properties;
    this.$current_num_slides = 1;
    this.$current_slide = 1;
    this.$user_points = 0;
    this.$total_points = 0;
    this.$slide_direction = $("body").hasClass('rtl-language') ? '+' : '-';

    this.init();
}

PlayQuiz.prototype = {
    init: function() {
        this.addEvents();
    },

    addOptionClickEvent: function() {
        $(document).on('click', '.quiz-question-option-container', $.proxy(function(e) {
            var option_chosen = $(e.currentTarget).attr('data-option'),
                question_no = $(e.currentTarget).closest('.quiz-container-slide').attr('data-question');
            
            this.checkQuestionOption(question_no, option_chosen);
        }, this));
    },

    removeOptionClickEvent: function() {
        $(document).off('click', '.quiz-question-option-container');
    },

    addEvents: function() {
        $("#play-quiz-button").on('click', $.proxy(this.startQuiz, this));
        $(document).on('click', '.quiz-question-proceed-button', $.proxy(function(e) {
            this.$current_question++;
            this.showQuestion(this.$current_question);
        }, this));
        $("#quiz-music-control").on('click', $.proxy(function(e) {
            if($(e.currentTarget).attr('data-muted') == 0) {
                $(e.currentTarget).html('<i class="fa fa-volume-off"></i>').attr('data-muted', 1);
                $("#option-correct-music").get(0).muted = true;
                $("#option-wrong-music").get(0).muted = true;

                document.cookie = 'sound_muted=1; expires=Fri, 3 Aug 2016 20:47:11 UTC; path=/';
            }
            else if($(e.currentTarget).attr('data-muted') == 1) {
                $(e.currentTarget).html('<i class="fa fa-volume-up"></i>').attr('data-muted', 0);
                $("#option-correct-music").get(0).muted = false;
                $("#option-wrong-music").get(0).muted = false;

                document.cookie = 'sound_muted=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            }
        }, this));
        $("#quiz-music-control-dialog-container-close").on('click', $.proxy(function(e) {
            document.cookie = 'sound_tip=1; expires=Fri, 3 Aug 2016 20:47:11 UTC; path=/';
            $("#quiz-music-control-dialog-container").hide();
        }, this));
        $("#quiz-container").on('transitionend webkitTransitionEnd', $.proxy(function(e) {
            if(e.target.id == 'quiz-container')
                $("#quiz-container").height($(".quiz-container-slide").eq(this.$current_slide - 1).outerHeight(true));
        }, this));
        $("#quiz-facebook-share").on('click', $.proxy(function(e) {
            this.shareQuiz();
        }, this));
    },

    ajaxRequest: function(command) {
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/post.php?command=' + command,
            cache: false,
            data: { post_id: this.$quiz_properties['post_id'] },
            dataType: 'JSON',
            success: function(response) { 
                
            },
            error: function(response) {
                
            }
        });
    },

    adjustHeight: function() {
        if(this.$current_slide == 1)
            $("#quiz-container").height($(".quiz-container-slide").eq(this.$current_slide - 1).outerHeight(true));
        else {
            var current_slide_height = $(".quiz-container-slide").eq(this.$current_slide - 1).outerHeight(true),
                previous_slide_height = $(".quiz-container-slide").eq(this.$current_slide - 2).outerHeight(true);

            $("#quiz-container").height(current_slide_height >= previous_slide_height ? current_slide_height : previous_slide_height);
        }
    },

    escapeHTML: function(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    },

    loadQuestion: function(question_no) {
        var html = '',
            num_only_picture_options = 0,
            options_display_order = [],
            option_index,
            is_only_picture_option,
            option_container_class;

        html += '<div class="quiz-container-slide" id="quiz-question-' + question_no + '" data-question="' + question_no + '">';
        
            if(this.$quiz_questions[question_no - 1]['image_id'] != null) {
                html += '<div class="quiz-question-picture-container">';
                html += '<img src="' + LOCATION_SITE + 'img/QUIZ/question/' + this.$quiz_questions[question_no - 1]['image_id'] + '" />';
                if(this.$quiz_questions[question_no - 1]['text'] != '')
                    html += '<div class="quiz-question-picture-text ' + (this.$quiz_questions[question_no - 1]['text'].length > 120 ? 'quiz-question-picture-text-small' : '') +  '">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['text']) + '</div>';
                if(this.$quiz_questions[question_no - 1]['image_attribution'] != -1)
                    html += '<div class="quiz-image-attribution" title="' + this.escapeHTML(this.$quiz_questions[question_no - 1]['image_attribution']) + '">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['image_attribution']) + '</div>';
                html += '</div>';
            }
            else if(this.$quiz_questions[question_no - 1]['text'] != '') {
                html += '<div class="quiz-question-text-container"><div class="quiz-question-text">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['text']) + '</div></div>';
            }

            html += '<div class="quiz-question-options-separator"></div>';

            html += '<div class="quiz-question-options-container">';
                for(var j=0; j<this.$quiz_questions[question_no - 1]['options'].length; j++) {
                    if(this.$quiz_questions[question_no - 1]['options'][j]['image_id'] != null && this.$quiz_questions[question_no - 1]['options'][j]['text'] == '') {
                        options_display_order.unshift(j);
                        num_only_picture_options++;
                    }
                    else {
                        options_display_order.push(j); 
                    }
                }
                for(j=0; j<options_display_order.length; j++) {
                    option_index = options_display_order[j];
                    is_only_picture_option = (this.$quiz_questions[question_no - 1]['options'][option_index]['image_id'] != null && this.$quiz_questions[question_no - 1]['options'][option_index]['text'] == '');
                    
                    if(num_only_picture_options == this.$quiz_questions[question_no - 1]['options'].length) 
                        option_container_class = 'quiz-question-option-container-picture';
                    else if(is_only_picture_option) {
                        if(num_only_picture_options >= 3 && j<3)
                            option_container_class = 'quiz-question-option-container-picture';
                        else
                            option_container_class = 'quiz-question-option-container-picture-full';   
                    }
                    else 
                        option_container_class = '';

                    html += '<div class="quiz-question-option-container quiz-question-option-container-hover ' +  option_container_class + '" id="quiz-question-' + question_no + '-option-' + option_index + '" data-option="' + option_index + '">';
                    html += '<div class="quiz-question-option-status-container"></div>';

                    html += '<div class="quiz-question-option-inner-container">';
                    if(this.$quiz_questions[question_no - 1]['options'][option_index]['image_id'] != null) {
                        html += '<div class="quiz-question-option-picture">';
                        html += '<img src="' + LOCATION_SITE + 'img/QUIZ/option/' + this.$quiz_questions[question_no - 1]['options'][option_index]['image_id'] + '" />';
                        if(this.$quiz_questions[question_no - 1]['options'][option_index]['image_attribution'] != -1)
                            html += '<div class="quiz-image-attribution" title="' + this.escapeHTML(this.$quiz_questions[question_no - 1]['options'][option_index]['image_attribution']) + '">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['options'][option_index]['image_attribution']) + '</div>';
                        html += '</div>';
                        if(this.$quiz_questions[question_no - 1]['options'][option_index]['text'] != '')
                            html += '<div class="quiz-question-option-picture-text">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['options'][option_index]['text']) + '</div>';
                    }
                    else if(this.$quiz_questions[question_no - 1]['options'][option_index]['text'] != '')
                        html += '<div class="quiz-question-option-text">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['options'][option_index]['text']) + '</div>';
                    html += '</div>';

                    html += '</div>';
                }
            html += '</div>';

            if(this.$quiz_questions[question_no - 1]['hint'] != '' || this.$quiz_questions[question_no - 1]['fact'] != '') {
                html += '<div class="quiz-question-hint-fact-container">';
                    html += '<div class="quiz-question-hint-container" style="' + (this.$quiz_questions[question_no - 1]['hint'] != '' ? '' : 'visibility:hidden') + '">';
                    html += '<div class="quiz-question-hint-title">' + LANGUAGE_STRINGS['HINT'] + '</div><div class="quiz-question-hint-separator">&#8212;</div><div class="quiz-question-hint">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['hint']) + '</div>';
                    html += '</div>';
                
                    html += '<div class="quiz-question-fact-container" style="' + (this.$quiz_questions[question_no - 1]['fact'] != '' ? '' : 'visibility:hidden') + '">';
                    html += '<div class="quiz-question-fact-title">' + LANGUAGE_STRINGS['FACT'] + '</div><div class="quiz-question-fact-separator">&#8212;</div><div class="quiz-question-fact">' + this.escapeHTML(this.$quiz_questions[question_no - 1]['fact']) + '</div>';
                    html += '</div>';
                html += '</div>';
            }

            html += '<div class="quiz-question-proceed-container"><button class="quiz-question-proceed-button">' + (question_no == this.$quiz_questions.length ? LANGUAGE_STRINGS['RESULTS_BUTTON'] : LANGUAGE_STRINGS['NEXT_QUESTION_BUTTON']) + '</button></div>';

        html += '</div>';

        $("#quiz-container").append(html);
        this.$current_num_slides++;
    },

    startQuiz: function() {
        this.ajaxRequest('UpdatePostPlayedData');
        if(UPDATE_CREDITS == 1)
            this.ajaxRequest('UpdatePostCreditsData');

        $("#quiz-title-container").css('opacity', '0.5');

        if($("#quiz-music-control").attr('data-show') == 1) {
            $("#option-correct-music").get(0).volume = 0.4;
            $("#option-wrong-music").get(0).volume = 0.4;
            if($("#quiz-music-control").attr('data-muted') == 1) {
                $("#option-correct-music").get(0).muted = true;
                $("#option-wrong-music").get(0).muted = true;
            }
        }
        
        $("#quiz-current-question-container").show();
        this.showQuestion(1);
    },

    showQuestion: function(question_no) {
        if($("#quiz-current-question-container").offset().top - $(window).scrollTop() < 0)
            $('html, body').scrollTop($("#quiz-current-question-container").offset().top - 20);

        this.$current_question = question_no;

        if(question_no > this.$quiz_questions.length) {
            this.showResult();
            return;
        }
        else if(question_no == this.$quiz_questions.length) {
            this.loadResult();
        }
        else
            this.loadQuestion(question_no + 1);

        this.$current_slide++;
        $("#quiz-container").css({ 'width': this.$current_num_slides + '00%', 'transform': 'translateX(' + this.$slide_direction + (100/this.$current_num_slides)*(this.$current_slide - 1) + '%)' });
        this.adjustHeight();
        
        this.addOptionClickEvent();

        if(question_no == 1) {
            if($("#quiz-music-control").attr('data-show') == 1)
                $("#quiz-music-control").show();
            if($("#quiz-music-control-dialog-container").attr('data-show') == 1)
                $("#quiz-music-control-dialog-container").show();
        }

        $("#current-question").text(question_no);
        $("#total-questions").text(this.$quiz_questions.length);
    },

    loadResult: function() {
        var html = '<div id="quiz-result-container" class="quiz-container-slide">';
                html += '<div id="quiz-result-loader"></div>';
                html += '<div id="quiz-result"></div>';
            html += '</div>';

        $("#quiz-container").append(html);
        this.$current_num_slides++;
    },

    checkQuestionOption: function(question_no, option_chosen) {
        $("#quiz-question-" + question_no).find('.quiz-question-option-status-container').show();
        $("#quiz-question-" + question_no).find('.quiz-question-option-container').removeClass('quiz-question-option-container-hover');

        var transition_smaller_delay = 1;
        
        if(this.$quiz_properties['type'] == 1) {
            this.$total_points++;

            var correct_option,
                that = this;

            for(var i=0; i<this.$quiz_questions[question_no - 1]['options'].length; i++) {
                if(this.$quiz_questions[question_no - 1]['options'][i]['correct'] == 1) {
                    correct_option = i;
                    break;
                }
            }

            if(option_chosen == correct_option) {
                $("#quiz-question-" + question_no + "-option-" + option_chosen).find('.quiz-question-option-status-container').each(function() {
                    $(this).css('line-height', $(this).outerHeight() + 'px').append('<div class="quiz-question-option-correct-i"><i class="fa fa-check-circle"></i></div>');
                });
                
                this.$user_points++;

                if($("#quiz-music-control").attr('data-show') == 1)
                    $("#option-correct-music").get(0).play();
            }
            else {
                $("#quiz-question-" + question_no + "-option-" + option_chosen).find('.quiz-question-option-status-container').each(function() {
                    $(this).css('line-height', $(this).outerHeight() + 'px').append('<div class="quiz-question-option-wrong-i"><i class="fa fa-times-circle"></i></div>');
                });
                setTimeout(function() {
                    $("#quiz-question-" + question_no + "-option-" + correct_option).find('.quiz-question-option-status-container').each(function() {
                        $(this).css('line-height', $(this).outerHeight() + 'px').append('<div class="quiz-question-option-correct-i quiz-question-option-status-small"><i class="fa fa-check-circle"></i></div>');
                        $("#quiz-question-" + question_no + "-option-" + correct_option).find('.quiz-question-option-correct-i').addClass('quiz-question-option-status-large');
                    });
                }, 400);
                
                if($("#quiz-music-control").attr('data-show') == 1)
                    $("#option-wrong-music").get(0).play();

                transition_smaller_delay = 0;
            }
        }
        else if(this.$quiz_properties['type'] == 2) {
            var question_max_weight = parseInt(this.$quiz_questions[question_no - 1]['options'][0]['weight'], 10);
            for(var i=1; i<this.$quiz_questions[question_no - 1]['options'].length; i++) {
                if(parseInt(this.$quiz_questions[question_no - 1]['options'][i]['weight'], 10) > question_max_weight)
                    question_max_weight = parseInt(this.$quiz_questions[question_no - 1]['options'][i]['weight'], 10);
            }

            this.$total_points += question_max_weight;
            this.$user_points += parseInt(this.$quiz_questions[question_no - 1]['options'][option_chosen]['weight'], 10);
        }

        this.showQuestionFact(question_no, transition_smaller_delay);
        this.removeOptionClickEvent();
    },

    showQuestionFact: function(question_no, transition_smaller_delay) {
        if(transition_smaller_delay == 1) {
            $("#quiz-question-" + question_no).find('.quiz-question-hint-fact-container').addClass('quiz-question-hint-fact-container-smaller-delay');
            $("#quiz-question-" + question_no).find('.quiz-question-proceed-container').addClass('quiz-question-proceed-container-smaller-delay')
        }

        if(this.$quiz_questions[question_no - 1]['fact'] != '')
            $("#quiz-question-" + question_no).find('.quiz-question-hint-fact-container').show().css({ 'transform': 'translateX(' + this.$slide_direction + '50%)' });
        $("#quiz-question-" + question_no).find('.quiz-question-proceed-container').show().css({ 'transform': 'translateX(0%)' });
    },

    showResult: function() {
        $("#quiz-title-container").css('opacity', '1');
        $("#quiz-current-question-container").css('visibility', 'hidden');
        this.ajaxRequest('UpdatePostFullyPlayedData');

        this.$current_slide++;
        $("#quiz-container").css({ 'width': this.$current_num_slides + '00%', 'transform': 'translateX(' + this.$slide_direction + (100/this.$current_num_slides)*(this.$current_slide - 1) + '%)' });
        this.adjustHeight();

        $("#quiz-result-loader").html('<i class="fa fa-gear fa-spin"></i>');

        var result = (this.$user_points/this.$total_points)*100,
            result_index;

        if(result >= 0 && result <= 25)
            result_index = 0;
        else if(result > 25 && result <= 50)
            result_index = 1;
        else if(result > 50 && result <= 75)
            result_index = 2;
        else if(result > 75 && result <= 100)
            result_index = 3;
        
        var html = '<h3>' + this.$quiz_results[result_index]['title'] + '</h3>';
        if(this.$quiz_results[result_index]['image_id'] != null) {
            html += '<div id="quiz-result-image">';
            html += '<img src="' + LOCATION_SITE + 'img/QUIZ/result/' + this.$quiz_results[result_index]['image_id'] + '" />';
            if(this.$quiz_results[result_index]['image_attribution'] != -1)
                html += '<div class="quiz-image-attribution" title="' + this.$quiz_results[result_index]['image_attribution'] + '">' + this.$quiz_results[result_index]['image_attribution'] + '</div>';
            if(this.$quiz_results[result_index]['description'] != '')
                html += '<div id="quiz-result-picture-description">' + this.$quiz_results[result_index]['description'] + '</div>';
            html += '</div>';
        }
        else {
            if(this.$quiz_results[result_index]['description'] != '')
                html += '<div id="quiz-result-picture-description">' + this.$quiz_results[result_index]['description'] + '</div>';
        }

        $("#quiz-result").html(html);

        var show_final_result = function() {
            setTimeout($.proxy(function() {
                $("#quiz-result-loader").hide();
                $("#quiz-result").addClass('quiz-result-in');
            }, this), 1000);
        };

        if(this.$quiz_results[result_index]['image_id'] != null) {
            var result_image = $("#quiz-result-image img").get(0);
            result_image.onload = function () {
                show_final_result();
            };
        }
        else {
            show_final_result();
        }
    },

    shareQuiz: function() {
        var that = this;

        FB.ui({
            method: 'feed',
            link: POST_URL,
        }, function(response) { 
            if(response && !response.error_message) {
                that.ajaxRequest('UpdatePostSharesData');
            }  
        });
    }
};

function FacebookCommentCreated(response) {
    play_quiz.ajaxRequest('UpdatePostCommentsData');
}

$("#quiz-embed").on('click', function() {
    $("#quiz-embed-lightbox-container").height($(document).height()).show();
    if($("#quiz-embed-code").text() == '')
        $("#quiz-embed-show-title").trigger('click');
});

$(".quiz-embed-option input[type='checkbox']").on('click', function() {
    var embed_code = '<link href="' + LOCATION_SITE + 'embed/quizzio-embed.css" rel="stylesheet" type="text/css">' + "\n";
    embed_code += '<script src="' + LOCATION_SITE + 'embed/quizzio-embed.js"></script>' + "\n";
    embed_code += '<iframe id="quizzio-iframe" src="' + LOCATION_SITE + 'embed.php?post_id=' + play_quiz.$quiz_properties.post_id + '&language_code=' + play_quiz.$quiz_properties.language_code + '&show_comments=' + ($("#quiz-embed-show-comments").is(":checked") ? 1 : 0) + '&show_title=' + ($("#quiz-embed-show-title").is(":checked") ? 1 : 0) + '"></iframe>';
    $("#quiz-embed-code").text(embed_code);
});

$("#quiz-embed-lightbox-close").on('click', function() {
    $("#quiz-embed-lightbox-container").hide();
});