var PostsMenuView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		var template = _.template($("#posts-menu-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#posts-menu").addClass('menu-active');
	}
});

var PostsLanguagesView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#posts-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#posts-menu-" + this.attributes.source).addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetActivatedLanguages' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#posts-languages-view").html(), { variable: 'data' })({ languages: response.languages, source: that.attributes.source });
				that.$el.html(template);

				$("#posts-languages-dropdown").val(DEFAULT_LANGUAGE).trigger('change');
			}
		});
	},
	events: {
		'change #posts-languages-dropdown': 'showAllPosts'
	},
	showAllPosts: function() {
		if(this.attributes.source == 'all') {
			if(typeof $_ALL_POSTS_VIEW !== 'undefined') {
				$_ALL_POSTS_VIEW.remove();
				$('<div id="posts-languages-posts-container"></div>').insertAfter("#posts-languages-container");
			}

			$_ALL_POSTS_VIEW = new AllPostsView();
			$_ALL_POSTS_VIEW.render();
		}
		else if(this.attributes.source == 'featured') {
			if(typeof $_FEATURED_POSTS_VIEW  !== 'undefined') {
				$_FEATURED_POSTS_VIEW.remove();
				$('<div id="posts-languages-posts-container"></div>').insertAfter("#posts-languages-container");
			}

			$_FEATURED_POSTS_VIEW = new FeaturedPostsView();
			$_FEATURED_POSTS_VIEW.render();
		}
	}
});

var AllPostsView = Backbone.View.extend({
	el: '#posts-languages-posts-container',
	render: function () {
		var that = this;
		
		$("#posts-languages-posts-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetPostsPagesCount', language_code: $("#posts-languages-dropdown").val() },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#all-posts-view").html(), { variable: 'data' })({ num_pages: response.num_pages });
				that.$el.html(template);

				if(response.num_pages > 0) {
					$_POSTS_IN_PAGE_VIEW = new PostsInPageView({ attributes: { page_no: 1 } });
					$_POSTS_IN_PAGE_VIEW.render();
				}
			}
		});
	},
	events: {
		'click #all-posts-prev-page-link': 'showPrevPage',
		'click #all-posts-next-page-link': 'showNextPage',
	},
	showNextPage: function() {
		$_POSTS_IN_PAGE_VIEW = new PostsInPageView({ attributes: { page_no: parseInt($("#all-posts-page-current").text(), 10) + 1 } });
		$_POSTS_IN_PAGE_VIEW.render();
	},
	showPrevPage: function() {
		$_POSTS_IN_PAGE_VIEW = new PostsInPageView({ attributes: { page_no: parseInt($("#all-posts-page-current").text(), 10) - 1 } });
		$_POSTS_IN_PAGE_VIEW.render();
	}
});

var PostsInPageView = Backbone.View.extend({
	el: '#posts-in-page-container',
	render: function () {
		var that = this;

		$("#posts-in-page-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetPosts', language_code: $("#posts-languages-dropdown").val() , page_no: this.attributes['page_no'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#post-in-page-view").html(), { variable: 'data' })({ posts: response.posts });
				that.$el.html(template);

				$("#all-posts-page-current").text(that.attributes['page_no']);
				if(that.attributes['page_no'] == 1)
					$("#all-posts-prev-page-link").hide();

				if(that.attributes['page_no'] != 1)
					$("#all-posts-prev-page-link").show();
				
				if(that.attributes['page_no'] == parseInt($("#all-posts-page-total").text(), 10))
					$("#all-posts-next-page-link").hide();

				if(that.attributes['page_no'] != parseInt($("#all-posts-page-total").text(), 10))
					$("#all-posts-next-page-link").show();
			}
		});
	}
});

var SinglePostView = Backbone.View.extend({
	el: '#single-detailed-post-container',
	render: function () {
		var that = this;

		$("#single-detailed-post-container").html('<img src="images/loader.gif" style="margin:100px auto;display:block;width:40px"/>').css('height', '100%').show();
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetPost', post_id: this.attributes['post_id'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#post-detailed-view").html(), { variable: 'data' })({ post: response.post });
				that.$el.html(template).show();
				that.$el.height($(document).height());
				$('html, body').scrollTop(0);
			},
			error: function(response) {
				that.$el.hide();
				alert(response.responseJSON.message);
			}
		});
	},
	events: {
		'click #post-detailed-container-close': 'hideDetailedPost',
		'change #post-change-featured-status': 'postFeaturedStatusChange',
		'click #post-info-get-stats': 'getStats',
		'click #post-info-delete-button': 'deletePost',
	},
	hideDetailedPost: function() {
		$("#single-detailed-post-container").hide();
	},
	postFeaturedStatusChange: function() {
		if($("#post-change-featured-status").attr('data-forced-change') == 1) {
			$("#post-change-featured-status").attr('data-forced-change', 0);
			return;
		}

		var confirm_change,
			command,
			that = this,
			post_id = $("#post-detailed-container").attr('data-post-id'),
			language_code = $("#post-detailed-container").attr('data-language-code');

		if($("#post-change-featured-status").val() == 1)
			confirm_change = confirm('Do you want to make this post featured ?');
		else 
			confirm_change = confirm('Do you want to un-feature this post ?');

		if(confirm_change == true) {
			if($("#post-change-featured-status").val() == 1)
				command = 'FeaturePost';
			else
				command = 'UnfeaturePost';

			$("#post-change-featured-status").attr('disabled', 'disabled');
			Backbone.ajax({
				type: 'POST',
				url: 'ajax-operations.php?command=' + command,
				cache: false,
				data: { post_id: post_id, language_code: language_code },
				dataType: 'JSON',
				success: function(response) {
					$("#post-change-featured-status").removeAttr('disabled');

					if(that.attributes.source == 'featured-posts-table' && command == 'UnfeaturePost') {
						$(".post-detailed-link[data-post-id='" + post_id + "']").closest('tr').remove();
					}
				},
				error: function(response) {
					if($("#post-change-featured-status").val() == 1)
						$("#post-change-featured-status").val('0').attr('data-forced-change', 1);
					else
						$("#post-change-featured-status").val('1').attr('data-forced-change', 1);
					$("#post-change-featured-status").removeAttr('disabled');
					
					alert(response.responseJSON.message);
				}
			});
		}
		else {
			if($("#post-change-featured-status").val() == 1)
				$("#post-change-featured-status").val('0').attr('data-forced-change', 1);
			else
				$("#post-change-featured-status").val('1').attr('data-forced-change', 1);
		}
	},
	getStats: function() {
		if($("#post-info-get-stats").attr('data-in-progress') == 1)
			return;

		$("#post-info-get-stats").attr('data-in-progress', 1).css('opacity', '0.5');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php?command=GetPostStatistics',
			cache: false,
			data: { post_id: $("#post-detailed-container").attr('data-post-id') },
			dataType: 'JSON',
			success: function(response) { 
				$("#post-info-get-stats").attr('data-in-progress', 0).css('opacity', '1').hide();
				$("#post-info-stats").show();

				$("#post-info-views").text(response.statistics.view_count);
				$("#post-info-played").html('<span>' + response.statistics.played_count + '</span><span class="post-info-label-content-light">(' + response.statistics.fully_played_count + ' times fully played)</span>');
				$("#post-info-shared").text(response.statistics.share_count);
				$("#post-info-comments").text(response.statistics.comment_count);
				if($("#post-info-premium").length == 1)
					$("#post-info-credits").text(response.statistics.credits_consumed_count);
				else 
					$("#post-info-stats").children().last().hide();
				
				$("#single-detailed-post-container").height($(document).height());
			},
			error: function(response) {
				$("#post-info-get-stats").attr('data-in-progress', 0).css('opacity', '1');
				alert(response.responseJSON.message);
			}
		});
	},
	deletePost: function() {
		if($("#post-info-delete-button").attr('data-in-progress') == 1)
			return;

		var post_id = $("#post-detailed-container").attr('data-post-id'),
			language_code = $("#post-info-delete-button").attr('data-language-code'),
			user_id = $("#post-info-delete-button").attr('data-user-id'),
			post_title = $("#post-info-title").text(),
			confirm_delete = confirm('Do you want to delete this post ?'),
			that = this;
		
		if(confirm_delete == true) {
			$("#post-info-delete-button").attr('data-in-progress', 1).css('opacity', '0.5');
			Backbone.ajax({
				type: 'POST',
				url: 'ajax-operations.php?command=DeletePost',
				cache: false,
				data: { post_id: post_id, language_code: language_code, user_id: user_id, post_title: post_title },
				dataType: 'JSON',
				success: function(response) { 
					$("#post-info-delete-button").attr('data-in-progress', 0).css('opacity', '1').hide();
					$("#post-detailed-container-close").trigger('click');
					
					if(that.attributes.source == 'featured-posts-table' || that.attributes.source == 'all-posts-table') {
						$(".post-detailed-link[data-post-id='" + post_id + "']").closest('tr').remove();
					}
				},
				error: function(response) {
					$("#post-info-delete-button").attr('data-in-progress', 0).css('opacity', '1');
					alert(response.responseJSON.message);
				}
			});
		}
	}
});

var FeaturedPostsView = Backbone.View.extend({
	el: '#posts-languages-posts-container',
	render: function () {
		var that = this;
		
		$("#posts-languages-posts-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetFeaturedPosts', language_code: $("#posts-languages-dropdown").val() },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#featured-posts-view").html(), { variable: 'data' })({ featured_posts: response.featured_posts });
				that.$el.html(template);
			}
		});
	},
});

var $_POSTS_MENU_VIEW,
	$_POSTS_LANGUAGES_VIEW,
	$_ALL_POSTS_VIEW,
	$_FEATURED_POSTS_VIEW,
	$_POSTS_IN_PAGE_VIEW,
	$_SINGLE_POST_VIEW;