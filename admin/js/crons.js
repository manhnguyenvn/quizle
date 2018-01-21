var CronsStatusView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		$("#menu a").removeClass('menu-active');
		$("#cron-menu").addClass('menu-active');

		var that = this;
		
		$("#menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetCronsStatus' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#crons-status-view").html(), { variable: 'data' })({ popular_posts_cron_time: response.popular_posts_cron_time, refresh_csrf_cron_time: response.refresh_csrf_cron_time });
				that.$el.html(template);
				$('.hastip').tooltipsy({alignTo: 'element', offset: [1, 0]});
			}
		});
	}
});

var $_CRONS_STATUS_VIEW;