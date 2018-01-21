var StatisticsMenuView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		var template = _.template($("#statistics-menu-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#stats-menu").addClass('menu-active');
	}
});

var StatisticsTodayView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		var template = _.template($("#statistics-today-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#stats-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#stats-menu-today").addClass('inner-menu-active');

		$_STATISTICS_DATA_VIEW = new StatisticsDataView({ attributes: { search_date: 'today' } });
		$_STATISTICS_DATA_VIEW.render();
	}
});

var StatisticsSearchView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		var template = _.template($("#statistics-search-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#stats-menu").addClass('menu-active');

		$("#inner-menu a").removeClass('inner-menu-active');
		$("#stats-menu-search").addClass('inner-menu-active');

		$("#statistics-search-date").datetimepicker({ timepicker:false, format:'Y-m-d' });
	},
	events: {
		'click #search-statistics': 'searchStatistics'
	},
	searchStatistics: function() {
		if($("#statistics-search-date").val() != '') {
			$_STATISTICS_DATA_VIEW = new StatisticsDataView({ attributes: { search_date: $("#statistics-search-date").val() } });
			$_STATISTICS_DATA_VIEW.render();
		}
	}
});

var StatisticsDataView = Backbone.View.extend({
	el: '#statistics-data',
	render: function () {		
		$("#statistics-data").html('<img src="images/loader.gif" />');
		
		var that = this;
		
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetStatistics', search_date: this.attributes['search_date'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#statistics-data-view").html(), { variable: 'data' })({ statistics: response.statistics });
				that.$el.html(template);
			}
		});
	}
});

var $_STATISTICS_MENU_VIEW,
	$_STATISTICS_DATA_VIEW,
	$_STATISTICS_TODAY_VIEW,
	$_STATISTICS_SEARCH_VIEW;