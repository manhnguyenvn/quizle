var InitialView = Backbone.View.extend({
	el: '#main-container',
	render: function () {
		var template = _.template($("#initial-view").html(), {});
		this.$el.html(template);
	},
	events: {
		'click .post-detailed-link': 'showDetailedPost',
	},
	showDetailedPost: function(e) {
		if(typeof $_SINGLE_POST_VIEW !== 'undefined') {
			$_SINGLE_POST_VIEW.remove();
			$('<div id="single-detailed-post-container"></div>').insertAfter("#menu-contents");
		}
		
		$_SINGLE_POST_VIEW = new SinglePostView({ attributes: { post_id: $(e.currentTarget).attr('data-post-id'), source: $(e.currentTarget).attr('data-source') } });
		$_SINGLE_POST_VIEW.render();
	},
});

var initial_view = new InitialView();

var Router = Backbone.Router.extend({
	routes: {
		'': 'statistics-today',
		'stats': 'statistics-today',
		'stats/today': 'statistics-today',
		'stats/search': 'statistics-search',
		'posts': 'all-posts',
		'posts/all': 'all-posts',
		'posts/featured': 'featured-posts',
		'users': 'all-users',
		'settings': 'site-settings',
		'settings/site': 'site-settings',
		'settings/sound': 'sound-settings',
		'settings/email': 'email-settings',
		'settings/premium': 'premium-settings',
		'settings/locale': 'locale-settings',
		'settings/ads': 'ads-settings',
		'settings/login': 'login-settings',
		'languages': 'languages',
		'languages/new': 'new-language',
		'transactions': 'all-transactions',
		'transactions/all': 'all-transactions',
		'transactions/pending': 'pending-transactions',
		'crons': 'crons-status',
	}
});

var router = new Router();
router.on('route:initial', function() {
	initial_view.render();
});

router.on('route:statistics-today', function() {
	initial_view.render();

	$_STATISTICS_MENU_VIEW = new StatisticsMenuView();
	$_STATISTICS_MENU_VIEW.render();
	
	$_STATISTICS_TODAY_VIEW = new StatisticsTodayView();
	$_STATISTICS_TODAY_VIEW.render();
});
router.on('route:statistics-search', function() {
	initial_view.render();

	$_STATISTICS_MENU_VIEW = new StatisticsMenuView();
	$_STATISTICS_MENU_VIEW.render();
	
	$_STATISTICS_SEARCH_VIEW = new StatisticsSearchView();
	$_STATISTICS_SEARCH_VIEW.render();
});

router.on('route:all-posts', function() {
	initial_view.render();

	$_POSTS_MENU_VIEW = new PostsMenuView();
	$_POSTS_MENU_VIEW.render();
	
	$_POSTS_LANGUAGES_VIEW = new PostsLanguagesView({ attributes: { source: 'all' } });
	$_POSTS_LANGUAGES_VIEW.render();
});
router.on('route:featured-posts', function() {
	initial_view.render();

	$_POSTS_MENU_VIEW = new PostsMenuView();
	$_POSTS_MENU_VIEW.render();
	
	$_POSTS_LANGUAGES_VIEW = new PostsLanguagesView({ attributes: { source: 'featured' } });
	$_POSTS_LANGUAGES_VIEW.render();
});

router.on('route:all-users', function() {
	initial_view.render();
	
	$_USERS_VIEW = new UsersView();
	$_USERS_VIEW.render();
});

router.on('route:site-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_SITE_SETTINGS_VIEW = new SiteSettingsView();
	$_SITE_SETTINGS_VIEW.render();
});
router.on('route:sound-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_SOUND_SETTINGS_VIEW = new SoundSettingsView();
	$_SOUND_SETTINGS_VIEW.render();
});
router.on('route:email-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_EMAIL_SETTINGS_VIEW = new EmailSettingsView();
	$_EMAIL_SETTINGS_VIEW.render();
});
router.on('route:premium-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_PREMIUM_SETTINGS_VIEW = new PremiumSettingsView();
	$_PREMIUM_SETTINGS_VIEW.render();
});
router.on('route:locale-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_LOCALE_SETTINGS_VIEW = new LocaleSettingsView();
	$_LOCALE_SETTINGS_VIEW.render();
});
router.on('route:ads-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_ADS_SETTINGS_VIEW = new AdsSettingsView();
	$_ADS_SETTINGS_VIEW.render();
});
router.on('route:login-settings', function() {
	initial_view.render();

	$_SETTINGS_MENU_VIEW = new SettingsMenuView();
	$_SETTINGS_MENU_VIEW.render();
	
	$_LOGIN_SETTINGS_VIEW = new LoginSettingsView();
	$_LOGIN_SETTINGS_VIEW.render();
});

router.on('route:languages', function() {
	initial_view.render();

	languages_dropdown_view = new LanguagesDropdownView({ attributes: { source: 'default' } });
	languages_dropdown_view.render();
});
router.on('route:new-language', function() {
	initial_view.render();

	new_language_view = new NewLanguageView();
	new_language_view.render();
});

router.on('route:all-transactions', function() {
	initial_view.render();

	$_TRANSACTIONS_MENU_VIEW = new TransactionsMenuView();
	$_TRANSACTIONS_MENU_VIEW.render();
	
	$_ALL_TRANSACTIONS_VIEW = new AllTransactionsView();
	$_ALL_TRANSACTIONS_VIEW.render();
});
router.on('route:pending-transactions', function() {
	initial_view.render();

	$_TRANSACTIONS_MENU_VIEW = new TransactionsMenuView();
	$_TRANSACTIONS_MENU_VIEW.render();
	
	$_PENDING_TRANSACTIONS_VIEW = new PendingTransactionsView();
	$_PENDING_TRANSACTIONS_VIEW.render();
});

router.on('route:crons-status', function() {
	initial_view.render();

	$_CRONS_STATUS_VIEW = new CronsStatusView();
	$_CRONS_STATUS_VIEW.render();
});

Backbone.history.start();