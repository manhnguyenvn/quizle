var UsersView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#users-menu").addClass('menu-active');

		var that = this;
		
		$("#menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetUsersPagesCount' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#all-users-view").html(), { variable: 'data' })({ num_pages: response.num_pages });
				that.$el.html(template);

				if(response.num_pages > 0) {
					$_USERS_IN_PAGE_VIEW = new UsersInPageView({ attributes: { page_no: 1 } });
					$_USERS_IN_PAGE_VIEW.render();
				}
			}
		});
	},
	events: {
		'click #all-users-prev-page-link': 'showPrevPage',
		'click #all-users-next-page-link': 'showNextPage',
	},
	showNextPage: function() {
		$_USERS_IN_PAGE_VIEW = new UsersInPageView({ attributes: { page_no: parseInt($("#all-users-page-current").text(), 10) + 1 } });
		$_USERS_IN_PAGE_VIEW.render();
	},
	showPrevPage: function() {
		$_USERS_IN_PAGE_VIEW = new UsersInPageView({ attributes: { page_no: parseInt($("#all-users-page-current").text(), 10) - 1 } });
		$_USERS_IN_PAGE_VIEW.render();
	}
});

var UsersInPageView = Backbone.View.extend({
	el: '#users-in-page-container',
	render: function () {
		var that = this;

		$("#users-in-page-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetUsers', page_no: this.attributes['page_no'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#users-in-page-view").html(), { variable: 'data' })({ users: response.users });
				that.$el.html(template);

				$("#all-users-page-current").text(that.attributes['page_no']);
				if(that.attributes['page_no'] == 1)
					$("#all-users-prev-page-link").hide();

				if(that.attributes['page_no'] != 1)
					$("#all-users-prev-page-link").show();
				
				if(that.attributes['page_no'] == parseInt($("#all-users-page-total").text(), 10))
					$("#all-users-next-page-link").hide();

				if(that.attributes['page_no'] != parseInt($("#all-users-page-total").text(), 10))
					$("#all-users-next-page-link").show();
			}
		});
	}
});

var $_USERS_VIEW,
	$_USERS_IN_PAGE_VIEW;