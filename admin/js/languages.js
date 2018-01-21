var LanguagesDropdownView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#languages-menu").addClass('menu-active');

		var that = this;
		
		$("#menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetLanguages' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#languages-dropdown-view").html(), { variable: 'data' })({ languages: response.languages });
				that.$el.html(template);
				
				if(that.attributes['source'] == 'add-language')
					$("#languages-dropdown").val(that.attributes['language_code']);
				else
					$("#languages-dropdown").val(DEFAULT_LANGUAGE);
				
				$("#languages-dropdown").trigger('change');
			}
		});
	},
	events: {
		'change #languages-dropdown': 'languageDropdownChange',
	},
	languageDropdownChange: function(e) {
		if(language_settings_view != undefined) {
			language_settings_view.remove();
			this.$el.append('<div id="language-settings-container"></div>');
		}

		language_settings_view = new LanguageSettingsView({ attributes: { language_code: $(e.currentTarget).val() } });
		language_settings_view.render();
	}
});

var LanguageSettingsView = Backbone.View.extend({
	el: '#language-settings-container',
	deleted_tags: [],
	render: function () {
		var that = this;

		$("#language-settings-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetLanguageSettings', language_code: this.attributes['language_code'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#language-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
				$(".language-tags-list").sortable();
			}
		});
	},
	events: {
		'change #language-activated': 'languageActivatedChange',
		'click .language-add-tag-button': 'addTag',
		'click .language-delete-tag-button': 'deleteTag',
		'change #email-address-confirm,#post-deleted-send-email,#email-credits-finished': 'sendEmailChange',
		'click #language-file-2-change': 'showUploadDialog',
		'change #language-file-2': 'changeFile',
		'click #language-settings-button': 'updateLanguage',
		'click #tagless-posts-replacement-button': 'addTagToTaglessPosts'
	},
	languageActivatedChange: function(e) {
		if($(e.currentTarget).val() == 1) 
			$("#langauge-activated-container").show();
		else
			$("#langauge-activated-container").hide();
	},
	sendEmailChange: function(e) {
		if($(e.currentTarget).val() == 1) 
			$(e.currentTarget).parent().find('.email-template-container').show();
		else 
			$(e.currentTarget).parent().find('.email-template-container').hide();
	},
	addTag: function(e) {
		$(e.currentTarget).prev().append('<li class="tag-input-container"><input type="text" maxlength="15" data-deleted="0" data-tag-id="NEW" placeholder="Tag Name" class="tag-input" /><span class="language-delete-tag-button">-</span></li>');
		if($(e.currentTarget).prev().find('.tag-input').length == 6)
			$(e.currentTarget).hide();

		$(".language-tags-list").sortable();
	},
	deleteTag: function(e) {
		$(e.currentTarget).closest('.language-tags-container').find('.language-add-tag-button').show();

		$(e.currentTarget).parent().remove();
		if($(e.currentTarget).prev().attr('data-tag-id') != 'NEW') 
			this.deleted_tags.push($(e.currentTarget).prev().attr('data-tag-id'));
	},
	showUploadDialog: function() {
		$("#language-file-2").trigger('click');
	},
	changeFile: function(e) {
		var file = $(e.currentTarget).get(0).files[0];
		
		$("#language-file-container a").hide();

		if(['text/plain'].indexOf(file.type) == -1) {
			$("#language-settings-error").text('Error : Wrong format of language file').show();
			$("#language-file-container").prev().addClass('form-error');
			return;
		}
		else {
			$("#language-settings-error").hide();
			$("#language-file-container").prev().removeClass('form-error');
		}
		
		$("#language-file-container span").text(file.name).show();
	},
	updateLanguage: function() {
		if($("#language-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		$("#language-settings-error").hide();
		$("#menu-contents").find('.form-error').removeClass('form-error');

		var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			file = $("#language-file-2").get(0).files[0],
			$post,
			num_tags,
			num_tags_blank,
			num_categories_blank,
			categories_tags,
			language_categories_tags = {},
			that = this;
		
		if(file != undefined) {
			if(['text/plain'].indexOf(file.type) == -1) {
				$("#language-settings-error").text('Error : Wrong format of language file').show();
				$("#language-file-container").prev().addClass('form-error');
			}
		}
		
		if($("#language-activated").val() == 1) {
			num_categories_blank = 0;
			$(".language-tags-container").each(function() {
				if(!blank_reg_exp.test($(this).find(".category-input").eq(0).val())) {
					if($(this).find(".tag-input").length > 0 || blank_reg_exp.test($(this).find(".icon-input").eq(0).val())) {
						$("#langauge-tags-outer-container").prev().addClass('form-error');
						$("#language-settings-error").text('Error : Category Name cannot be empty').show();
						return;
					}
					else {
						num_categories_blank++;
					}
				}
				else {
					if($(this).find(".tag-input").length == 0) {
						$("#langauge-tags-outer-container").prev().addClass('form-error');
						$("#language-settings-error").text('Error : A Category should have at least 1 Tag').show();
						return;
					}
					else {
						$(this).find(".tag-input").each(function() {
							if(!blank_reg_exp.test($(this).val())) {
								$("#langauge-tags-outer-container").prev().addClass('form-error');
								$("#language-settings-error").text('Error : Tag Name cannot be empty').show();
								return;
							}
						});
					}
				}
			});

			if(num_categories_blank == 3) {
				$("#langauge-tags-outer-container").prev().addClass('form-error');
				$("#language-settings-error").text('Error : There should be at least one Category').show();
				return;
			}

			if($("#email-address-confirm").val() == 1) {
				if(!blank_reg_exp.test($("#email-confirm-body").val())) {
					$("#email-confirm-body").prev().addClass('form-error');
				}
				else if($("#email-confirm-body").val().indexOf('CONFIRMATION_CODE') == -1) {
					$("#email-address-confirm").prev().addClass('form-error');
					$("#language-settings-error").text('Error : Email confirmation body must contain the shortcode CONFIRMATION_CODE. This shortcode will be replaced by the actual email confirmation code in the email').show();
				}
			}

			if($("#post-deleted-send-email").val() == 1) {
				if(!blank_reg_exp.test($("#post-deleted-body").val())) {
					$("#post-deleted-send-email").prev().addClass('form-error');
				}
			}

			if($("#email-credits-finished").val() == 1) {
				if(!blank_reg_exp.test($("#credits-finished-body").val())) {
					$("#email-credits-finished").prev().addClass('form-error');
				}
			}
		}

        if($("#menu-contents").find('.form-error').length != 0) 
			return;

		$(".language-tags-container").each(function() {
			categories_tags = [];
			$(this).find(".tag-input").each(function(index) {
				categories_tags.push({ name: $.trim($(this).val()), id: $(this).attr('data-tag-id'), position: index + 1 });
			});
			language_categories_tags[$(this).find('.category-input').attr('data-tag-id')] = { name: $(this).find('.category-input').val(), icon: $(this).find('.icon-input').val(), tags: categories_tags };
		});

		$post = new FormData();
		$post.append('language_code', $("#language-settings-button").attr('data-language-code'));
		$post.append('language_activated', $("#language-activated").val());
        $post.append('language_categories_tags', JSON.stringify(language_categories_tags));
        $post.append('send_email_confirm_email', $("#email-address-confirm").val());
        $post.append('confirm_email_template_subject', $.trim($("#email-confirm-subject").val()));
        $post.append('confirm_email_template_body', $.trim($("#email-confirm-body").val()));
        $post.append('send_post_deleted_email', $("#post-deleted-send-email").val());
        $post.append('post_deleted_email_template_subject', $.trim($("#post-deleted-subject").val()));
        $post.append('post_deleted_email_template_body', $.trim($("#post-deleted-body").val()));
        $post.append('send_credits_finished_email', $("#email-credits-finished").val());
        $post.append('credits_finished_email_template_subject', $.trim($("#credits-finished-subject").val()));
        $post.append('credits_finished_email_template_body', $.trim($("#credits-finished-body").val()));
        if(file != undefined) 
        	$post.append('language_file', file);
        $post.append('tags_to_delete', JSON.stringify(this.deleted_tags));

		$("#language-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=UpdateLanguage',
			cache: false,
			data: $post,
			processData: false,
    		contentType: false,
			dataType: 'JSON',
			success: function(response) {
				$("#language-settings-button").attr('data-in-progress', 0).css('opacity', '1');

				if(that.deleted_tags.length > 0) {
					if(response.deleted == -1) {
						var html = '';
						for(var i=0; i<response.tags.length; i++) {
							if(that.deleted_tags.indexOf(response.tags[i]['id']) == -1)
								html += '<option value="' + response.tags[i]['id'] + '">' + response.tags[i]['name'] + '</option>';
						}

						$("#tagless-posts-replacement-tag").html(html);
						$("#tagless-posts-replacement-tag-container").show();
						$("#language-settings-button").hide();
						$("#language-settings-container-lightbox").height($("#language-settings-container").height() - $("#tagless-posts-replacement-tag-container").height()).show();
					}
				}

				if(response.added_tags.length > 0) {
					for(i=0; i<response.added_tags.length; i++)
						$(".language-tags-list[data-category-id='" + response.added_tags[i]['category_id'] + "']").find(".tag-input").eq(response.added_tags[i]['position'] - 1).attr('data-tag-id', response.added_tags[i]['tag_id']);
				}

				if($("#language-activated").val() == 1) 
					$("#language-deactivated-notice").hide();
				else 
					$("#language-deactivated-notice").show();
			},
			error: function(response) {
				$("#language-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#language-settings-error").text(response.responseJSON.message).show();
			}
		});
	},
	addTagToTaglessPosts: function() {
		if($("#tagless-posts-replacement-button").attr('data-in-progress') == 1) {
			return;
		}

		$("#language-settings-error").hide();
		$("#menu-contents").find('.form-error').removeClass('form-error');

		var that = this;

		$("#tagless-posts-replacement-button").attr('data-in-progress', 1).css('opacity', '0.4');
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=AddTagToTaglessPosts',
			cache: false,
			data: { 'tags_to_delete': JSON.stringify(this.deleted_tags), 'replacement_tag': $("#tagless-posts-replacement-tag").val() },
			dataType: 'JSON',
			success: function(response) {
				$("#tagless-posts-replacement-button").attr('data-in-progress', 0).css('opacity', '1');

				that.deleted_tags = [];
				$("#tagless-posts-replacement-tag-container").hide();
				$("#language-settings-button").show();
				$("#language-settings-container-lightbox").hide();
			},
			error: function(response) {
				$("#tagless-posts-replacement-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#language-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var NewLanguageView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#languages-menu").addClass('menu-active');

		var that = this;
		
		$("#menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetLanguagesCode' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#new-language-view").html(), { variable: 'data' })({ languages: response.languages, supported_languages: response.supported_languages });
				that.$el.html(template);
			}
		});
	},
	events: {
		'click #language-file-change': 'showUploadDialog',
		'change #language-file': 'changeFile',
		'click #new-language-button': 'addLanguage',
	},
	showUploadDialog: function() {
		$("#language-file").trigger('click');
	},
	changeFile: function(e) {
		var file = $(e.currentTarget).get(0).files[0];
		
		if(['text/plain'].indexOf(file.type) == -1) {
			$("#new-language-error").text('Error : Wrong format of language file').show();
			$("#language-file-container").prev().addClass('form-error');
			return;
		}
		else {
			$("#new-language-error").hide();
			$("#language-file-container").prev().removeClass('form-error');
		}
		
		$("#language-file-container span").text(file.name).show();
	},
	addLanguage: function(e) {
		if($("#new-language-button").attr('data-in-progress') == 1) {
			return;
		}

		$("#site-settings-error").hide();
		$("#menu-contents").find('.form-error').removeClass('form-error');

		var file = $("#language-file").get(0).files[0],
			$post;
		
		if(file != undefined) {
			if(['text/plain'].indexOf(file.type) == -1) {
				$("#new-language-error").text('Error : Wrong format of language file').show();
				$("#language-file-container").prev().addClass('form-error');
			}
		}
		else {
			$("#language-file-container").prev().addClass('form-error');
		}

        if($("#menu-contents").find('.form-error').length != 0) 
			return;

        $post = new FormData();
        $post.append('language_code', $("#language-name").val());
        $post.append('language_name', $("#language-name option:selected").text());
        $post.append('language_direction', $("#language-name option:selected").attr('data-direction'));
        $post.append('language_file', file);

		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=AddLanguage',
			cache: false,
			data: $post,
			processData: false,
    		contentType: false,
			dataType: 'JSON',
			success: function() { 
				$("#new-language-button").attr('data-in-progress', 0).css('opacity', '1');
				router.navigate("languages", {trigger: false});
				
				if(languages_dropdown_view != undefined)
					languages_dropdown_view.remove();
				
				languages_dropdown_view = new LanguagesDropdownView({ attributes: { source: 'add-language', language_code: $("#language-name").val() } });
				languages_dropdown_view.render();
			},
			error: function(response) {
				$("#new-language-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#new-language-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var languages_dropdown_view,
	language_settings_view,
	new_language_view;