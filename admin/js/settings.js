var SettingsMenuView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		var template = _.template($("#settings-menu-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');
	}
});

var SiteSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#site-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetSiteSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#site-settings-view").html(), { variable: 'data' })({ settings: response.settings, themes: response.themes });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
			}
		});
	},
	events: {
		'click #site-logo-change': 'showUploadDialog',
		'change #site-logo': 'changeImage',
		'click #social-image-change': 'showUploadDialog',
		'change #social-image': 'changeImage',
		'click #site-settings-button': 'saveSettings',
	},
	showUploadDialog: function(e) {
		$(e.currentTarget).next().trigger('click');
	},
	changeImage: function(e) {
		var that = this,
			file = $(e.currentTarget).get(0).files[0];
		
		if(['image/png', 'image/jpeg', 'image/jpg'].indexOf(file.type) == -1) {
			$("#site-settings-error").text('Error : Only JPEG / PNG format allowed').show();
			$("#" + $(e.currentTarget).attr('id') + "-container").prev().addClass('form-error');
			return;
		}
		else {
			$("#site-settings-error").hide();
			$("#" + $(e.currentTarget).attr('id') + "-container").prev().removeClass('form-error');
		}
		
		var url = URL.createObjectURL($(e.currentTarget).get(0).files[0]);
		var img = new Image;
		img.onload = function() {
			$("#" + $(e.currentTarget).attr('id') + "-container img").attr('src', url).show();
		};
		img.src = url;
	},
	saveSettings: function(e) {
		if($("#site-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			logo_file = $("#site-logo").get(0).files[0],
			social_image_file = $("#social-image").get(0).files[0],
			that = this;

		$("#site-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		if(!blank_reg_exp.test($("#site-name").val())) {
			$("#site-name").prev().addClass('form-error');
		}

		if(!blank_reg_exp.test($("#facebook-app-id").val())) {
			$("#facebook-app-id").prev().addClass('form-error');
		}

		if(!blank_reg_exp.test($("#facebook-app-secret").val())) {
			$("#facebook-app-secret").prev().addClass('form-error');
		}
		
		if(logo_file != undefined) {
			if(['image/png', 'image/jpeg', 'image/jpg'].indexOf(logo_file.type) == -1) {
				$("#site-logo-container").prev().addClass('form-error');
				$("#site-settings-error").text('Error : Only JPEG / PNG format allowed').show();
			}
		}

		if(social_image_file != undefined) {
			if(['image/png', 'image/jpeg', 'image/jpg'].indexOf(social_image_file.type) == -1) {
				$("#social-image-container").prev().addClass('form-error');
				$("#site-settings-error").text('Error : Only JPEG / PNG format allowed').show();
			}
		}

		if($("#inner-menu-contents").find('.form-error').length != 0) 
			return;

		var settings = { site_name: $.trim($("#site-name").val()), facebook_app_id: $.trim($("#facebook-app-id").val()), facebook_app_secret: $.trim($("#facebook-app-secret").val()), facebook_page_url: $.trim($("#facebook-page-url").val()), ga_code: $.trim($("#ga-code").val()), max_image_size: $("#max-image-size").val(), current_theme: $("#current-theme").val(), font_family: $("#font-family").val() },
			$post = new FormData();
		
		for(var key in settings) {
    		$post.append('settings[' + key + ']', settings[key]);
		}
		if(logo_file != undefined) 
        	$post.append('logo', logo_file);
        if(social_image_file != undefined) 
        	$post.append('social', social_image_file);

        $("#site-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
        Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetSiteSettings',
			cache: false,
			data: $post,
			processData: false,
    		contentType: false,
			dataType: 'JSON',
			success: function(response) {
				$("#site-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#site-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#site-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var SoundSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#sound-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetSoundSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#sound-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
			}
		});
	},
	events: {
		'change #sound-allowed': 'soundAllowedChange',
		'click #correct-sound-change': 'showUploadDialog',
		'change #correct-sound': 'changeSound',
		'click #wrong-sound-change': 'showUploadDialog',
		'change #wrong-sound': 'changeSound',
		'click #sound-settings-button': 'saveSettings',
	},
	soundAllowedChange: function(e) {
		if($(e.currentTarget).val() == 1)
			$("#sound-settings-container").show();
		else
			$("#sound-settings-container").hide();
	},
	showUploadDialog: function(e) {
		$(e.currentTarget).next().trigger('click');
	},
	changeSound: function(e) {
		var that = this,
			file = $(e.currentTarget).get(0).files[0];
		
		if(['audio/mpeg'].indexOf(file.type) == -1) {
			$("#sound-settings-error").text('Error : Only MP3 format allowed').show();
			$("#" + $(e.currentTarget).attr('id') + "-container").prev().addClass('form-error');
			return;
		}
		else {
			$("#sound-settings-error").hide();
			$("#" + $(e.currentTarget).attr('id') + "-container").prev().removeClass('form-error');
			$("#" + $(e.currentTarget).attr('id') + "-name").text(file.name).show();
			$("#" + $(e.currentTarget).attr('id') + "-music").remove();
		}
	},
	saveSettings: function(e) {
		if($("#site-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		var correct_sound_file = $("#correct-sound").get(0).files[0],
			wrong_sound_file = $("#wrong-sound").get(0).files[0],
			that = this;

		$("#sound-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		if($("#sound-allowed").val() == 1) {
			if(correct_sound_file != undefined) {
				if(['audio/mpeg'].indexOf(correct_sound_file.type) == -1) {
					$("#correct-sound-container").prev().addClass('form-error');
					$("#sound-settings-error").text('Error : Only MP3 format allowed').show();
				}
			}

			if(wrong_sound_file != undefined) {
				if(['audio/mpeg'].indexOf(wrong_sound_file.type) == -1) {
					$("#wrong-sound-container").prev().addClass('form-error');
					$("#sound-settings-error").text('Error : Only MP3 format allowed').show();
				}
				else {
					$("#wrong-sound-music").remove();
					$("#wrong-sound-name").text(wrong_sound_file.name).show();
				}
			}
		}

		if($("#inner-menu-contents").find('.form-error').length != 0) 
			return;

		var settings = { sound_allowed: $("#sound-allowed").val() },
			$post = new FormData();
		
    	$post.append('settings[sound_allowed]', $("#sound-allowed").val());
		if($("#sound-allowed").val() == 1) {
			if(correct_sound_file != undefined) 
	        	$post.append('correct_sound_file', correct_sound_file);
	        if(wrong_sound_file != undefined) 
	        	$post.append('wrong_sound_file', wrong_sound_file);
	    }

        $("#sound-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
        Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetSoundSettings',
			cache: false,
			data: $post,
			processData: false,
    		contentType: false,
			dataType: 'JSON',
			success: function(response) {
				$("#sound-settings-button").attr('data-in-progress', 0).css('opacity', '1');

				if(correct_sound_file != undefined) {
					$('<audio id="correct-sound-music" src="../music-files/correct.mp3?' + Math.random() + '" controls></audio>').insertBefore("#correct-sound-change");
					$("#correct-sound-name").hide();
				}

				if(wrong_sound_file != undefined) {
					$('<audio id="wrong-sound-music" src="../music-files/wrong.mp3?' + Math.random() + '" controls></audio>').insertBefore("#wrong-sound-change");
					$("#wrong-sound-name").hide();
				}
			},
			error: function(response) {
				$("#sound-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#sound-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var EmailSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#email-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetEmailSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#email-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
			}
		});
	},
	events: {
		'click #email-settings-button': 'saveSettings',
		'change #email-method': 'emailMethodChange'
	},
	emailMethodChange: function(e) {
		if($(e.currentTarget).val() == 0)
			$("#smtp-settings-container").hide();
		else
			$("#smtp-settings-container").show();
	},
	saveSettings: function(e) {
		if($("#email-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			email_reg_exp = /^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/,
			digits_reg_exp = /^[0-9]{1,}$/,
			$post;

		$("#email-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		if(!email_reg_exp.test($("#email-from").val())) {
			$("#email-from").prev().addClass('form-error');
		}

		if(!blank_reg_exp.test($("#email-from-name").val())) {
			$("#email-from-name").prev().addClass('form-error');
		}

		if($("#email-method").val() == 1) {
			if(!blank_reg_exp.test($("#smtp-host").val())) {
				$("#smtp-host").prev().addClass('form-error');
			}

			if(!digits_reg_exp.test($("#smtp-port").val())) {
				$("#smtp-port").prev().addClass('form-error');
			}

			if(!blank_reg_exp.test($("#smtp-username").val())) {
				$("#smtp-username").prev().addClass('form-error');
			}

			if(!blank_reg_exp.test($("#smtp-password").val())) {
				$("#smtp-password").prev().addClass('form-error');
			}
		}

		if($("#inner-menu-contents").find('.form-error').length != 0) 
			return;

		$("#email-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		$post = { settings: { email_from: $.trim($("#email-from").val()), email_from_name: $.trim($("#email-from-name").val()), smtp_used: $("#email-method").val(), server_smtp_host: $.trim($("#smtp-host").val()), server_smtp_port: $.trim($("#smtp-port").val()), server_smtp_security: $("#smtp-security").val(), server_smtp_username: $.trim($("#smtp-username").val()), server_smtp_password: $.trim($("#smtp-password").val()) } };
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetEmailSettings',
			cache: false,
			data: $post,
			dataType: 'JSON',
			success: function() { 
				$("#email-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#email-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#email-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var PremiumSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#premium-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetPremiumSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#premium-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
			}
		});
	},
	events: {
		'click #premium-settings-button': 'saveSettings',
		'change #premium-activated': 'premiumActivatedChange',
		'change #paypal-sandbox-activated': 'paypalSandboxActivatedChange'
	},
	paypalSandboxActivatedChange: function(e) {
		if($(e.currentTarget).val() == 0) {
			$("#paypal-live-settings").show();
			$("#paypal-sandbox-settings").hide();
		}
		else {
			$("#paypal-live-settings").hide();
			$("#paypal-sandbox-settings").show();
		}
	},
	premiumActivatedChange: function(e) {
		if($(e.currentTarget).val() == 0)
			$("#premium-settings-container").hide();
		else
			$("#premium-settings-container").show();
	},
	saveSettings: function(e) {
		if($("#premium-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			digits_reg_exp = /^[0-9]{1,}$/,
			$post;

		$("#premium-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		if($("#premium-activated").val() == 1) {
			if(!digits_reg_exp.test($("#credits-quantity").val()) || $("#credits-quantity").val() == 0) {
				$("#credits-quantity").prev().addClass('form-error');
			}

			if(!digits_reg_exp.test($("#credits-value").val()) || $("#credits-value").val() == 0) {
				$("#credits-value").prev().addClass('form-error');
			}

			if(!digits_reg_exp.test($("#premium-quiz-credits").val())) {
				$("#premium-quiz-credits").prev().addClass('form-error');
			}

			if($("#paypal-sandbox-activated").val() == 0) {
				if(!blank_reg_exp.test($("#paypal-api-username").val())) {
					$("#paypal-api-username").prev().addClass('form-error');
				}

				if(!blank_reg_exp.test($("#paypal-api-password").val())) {
					$("#paypal-api-password").prev().addClass('form-error');
				}

				if(!blank_reg_exp.test($("#paypal-api-signature").val())) {
					$("#paypal-api-signature").prev().addClass('form-error');
				}
			}
			else {
				if(!blank_reg_exp.test($("#paypal-sandbox-api-username").val())) {
					$("#paypal-sandbox-api-username").prev().addClass('form-error');
				}

				if(!blank_reg_exp.test($("#paypal-sandbox-api-password").val())) {
					$("#paypal-sandbox-api-password").prev().addClass('form-error');
				}

				if(!blank_reg_exp.test($("#paypal-sandbox-api-signature").val())) {
					$("#paypal-sandbox-api-signature").prev().addClass('form-error');
				}
			}
		}

		if($("#inner-menu-contents").find('.form-error').length != 0) 
			return;

		$("#premium-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		$post = { settings: { activate_premium: $.trim($("#premium-activated").val()), transaction_currency: $("#transaction-currency").val(), credits_quantity: $.trim($("#credits-quantity").val()), credits_value: $("#credits-value").val(), premium_quiz_credits: $.trim($("#premium-quiz-credits").val()), paypal_sandbox_activated: $.trim($("#paypal-sandbox-activated").val()), paypal_api_username: $("#paypal-api-username").val(), paypal_api_password: $.trim($("#paypal-api-password").val()), paypal_api_signature: $.trim($("#paypal-api-signature").val()),    paypal_sandbox_api_username: $("#paypal-sandbox-api-username").val(), paypal_sandbox_api_password: $.trim($("#paypal-sandbox-api-password").val()), paypal_sandbox_api_signature: $.trim($("#paypal-sandbox-api-signature").val()) } };
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetPremiumSettings',
			cache: false,
			data: $post,
			dataType: 'JSON',
			success: function() { 
				$("#premium-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#premium-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#premium-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var LocaleSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#locale-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetLocaleSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#locale-settings-view").html(), { variable: 'data' })({ settings: response.settings, timezones: response.timezones, languages: response.languages });
				that.$el.html(template);
			}
		});
	},
	events: {
		'click #locale-settings-button': 'saveSettings'
	},
	saveSettings: function(e) {
		if($("#locale-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		$("#locale-settings-error").hide();

		$("#locale-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		$post = { settings: { timezone: $("#timezone").val(), language: $("#default-language").val() } };
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetLocaleSettings',
			cache: false,
			data: $post,
			dataType: 'JSON',
			success: function() { 
				$("#locale-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#locale-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#locale-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var AdsSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#ads-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetAdsSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#ads-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
			}
		});
	},
	events: {
		'click #ads-settings-button': 'saveSettings'
	},
	saveSettings: function(e) {
		if($("#ads-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		$("#ads-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			digits_reg_exp = /^[0-9]{1,}$/;

		$("#inner-menu-contents .form-element").each(function(index) {
			if(!blank_reg_exp.test($(this).find('textarea').val())) {
				if( blank_reg_exp.test($(".form-element-extra").eq(index).find("input[type='text']").eq(0).val()) || blank_reg_exp.test($(".form-element-extra").eq(index).find("input[type='text']").eq(1).val()) )
					$("#ad-unit-" + (index +1)).prev().addClass('form-error');
			}
			else {
				if( !digits_reg_exp.test($(".form-element-extra").eq(index).find("input[type='text']").eq(0).val()) || !digits_reg_exp.test($(".form-element-extra").eq(index).find("input[type='text']").eq(1).val()) )
					$("#ad-unit-" + (index +1)).prev().addClass('form-error');

				if( parseInt($(".form-element-extra").eq(index).find("input[type='text']").eq(0).val(), 10) > 300 || parseInt($(".form-element-extra").eq(index).find("input[type='text']").eq(1).val(), 10) > 250 )
					$("#ad-unit-" + (index +1)).prev().addClass('form-error');
			}
		});

		if($("#inner-menu-contents").find('.form-error').length != 0) {
			$("#ads-settings-error").text('Error: Incorrect Information').show();
			return;
		}

		$("#ads-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		$post = { settings: [	{ ad_id: 1, ad_code: !blank_reg_exp.test($("#ad-unit-1").val()) ? "NULL" : $.trim($("#ad-unit-1").val()), ad_width: $(".form-element-extra").eq(0).find("input[type='text']").eq(0).val(), ad_height: $(".form-element-extra").eq(0).find("input[type='text']").eq(1).val() }, 
								{ ad_id: 2, ad_code: !blank_reg_exp.test($("#ad-unit-2").val()) ? "NULL" : $.trim($("#ad-unit-2").val()), ad_width: $(".form-element-extra").eq(1).find("input[type='text']").eq(0).val(), ad_height: $(".form-element-extra").eq(1).find("input[type='text']").eq(1).val() }, 
								{ ad_id: 3, ad_code: !blank_reg_exp.test($("#ad-unit-3").val()) ? "NULL" : $.trim($("#ad-unit-3").val()), ad_width: $(".form-element-extra").eq(2).find("input[type='text']").eq(0).val(), ad_height: $(".form-element-extra").eq(2).find("input[type='text']").eq(1).val() }, 
								{ ad_id: 4, ad_code: !blank_reg_exp.test($("#ad-unit-4").val()) ? "NULL" : $.trim($("#ad-unit-4").val()), ad_width: $(".form-element-extra").eq(3).find("input[type='text']").eq(0).val(), ad_height: $(".form-element-extra").eq(3).find("input[type='text']").eq(1).val() } ] 
				};
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetAdsSettings',
			cache: false,
			data: $post,
			dataType: 'JSON',
			success: function() { 
				$("#ads-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#ads-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#ads-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var LoginSettingsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#settings-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#login-menu").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetLoginSettings' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#login-settings-view").html(), { variable: 'data' })({ settings: response.settings });
				that.$el.html(template);
			}
		});
	},
	events: {
		'click #login-settings-button': 'saveSettings'
	},
	saveSettings: function(e) {
		if($("#login-settings-button").attr('data-in-progress') == 1) {
			return;
		}

		var email_reg_exp = /^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/,
			blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
			password_edited = 1;

		$("#login-settings-error").hide();
		$("#inner-menu-contents").find('.form-error').removeClass('form-error');

		if(!email_reg_exp.test($("#login-username").val())) {
			$("#login-username").prev().addClass('form-error');
		}

		if(!blank_reg_exp.test($("#login-password").val()) && !blank_reg_exp.test($("#login-password-confirm").val()))
			password_edited = 0;

		if(blank_reg_exp.test($("#login-password").val())) {
			if($.trim($("#login-password").val()) != $.trim($("#login-password-confirm").val())) {
				$("#login-password").prev().addClass('form-error');
				$("#login-password-confirm").prev().addClass('form-error');
				$("#login-settings-error").text('Passwords do not match').show();
			}
		}

		if($("#inner-menu-contents").find('.form-error').length != 0) 
			return;

		$("#login-settings-button").attr('data-in-progress', 1).css('opacity', '0.4');
		$post = { settings: { username: $.trim($("#login-username").val()) } };
		if(password_edited == 1)
			$post['settings']['password'] = $.trim($("#login-password").val());
		Backbone.ajax({
			type: 'POST',
			url: 'ajax-operations.php?command=SetLoginSettings',
			cache: false,
			data: $post,
			dataType: 'JSON',
			success: function() { 
				$("#login-settings-button").attr('data-in-progress', 0).css('opacity', '1');
			},
			error: function(response) {
				$("#login-settings-button").attr('data-in-progress', 0).css('opacity', '1');
				$("#login-settings-error").text(response.responseJSON.message).show();
			}
		});
	}
});

var $_SETTINGS_MENU_VIEW,
	$_SITE_SETTINGS_VIEW,
	$_SOUND_SETTINGS_VIEW,
	$_EMAIL_SETTINGS_VIEW,
	$_PREMIUM_SETTINGS_VIEW,
	$_LOCALE_SETTINGS_VIEW,
	$_ADS_SETTINGS_VIEW,
	$_LOGIN_SETTINGS_VIEW;