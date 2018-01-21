var TransactionsMenuView = Backbone.View.extend({
	el: '#menu-contents',
	render: function () {
		var template = _.template($("#transactions-menu-view").html(), {});
		this.$el.html(template);

		$("#menu a").removeClass('menu-active');
		$("#transactions-menu").addClass('menu-active');
	}
});

var AllTransactionsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#inner-menu a").removeClass('inner-menu-active');
		$("#transactions-menu-all").addClass('inner-menu-active');

		var that = this;
		
		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetTransactionsPagesCount' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#all-transactions-view").html(), { variable: 'data' })({ num_pages: response.num_pages });
				that.$el.html(template);

				if(response.num_pages > 0) {
					$_TRANSACTIONS_IN_PAGE_VIEW = new TransactionsInPageView({ attributes: { page_no: 1 } });
					$_TRANSACTIONS_IN_PAGE_VIEW.render();
				}
			}
		});
	},
	events: {
		'click #all-transactions-prev-page-link': 'showPrevPage',
		'click #all-transactions-next-page-link': 'showNextPage',
	},
	showNextPage: function() {
		$_TRANSACTIONS_IN_PAGE_VIEW = new TransactionsInPageView({ attributes: { page_no: parseInt($("#all-transactions-page-current").text(), 10) + 1 } });
		$_TRANSACTIONS_IN_PAGE_VIEW.render();
	},
	showPrevPage: function() {
		$_TRANSACTIONS_IN_PAGE_VIEW = new TransactionsInPageView({ attributes: { page_no: parseInt($("#all-transactions-page-current").text(), 10) - 1 } });
		$_TRANSACTIONS_IN_PAGE_VIEW.render();
	}
});

var TransactionsInPageView = Backbone.View.extend({
	el: '#transactions-in-page-container',
	render: function () {
		var that = this;

		$("#transactions-in-page-container").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetAllTransactions', page_no: this.attributes['page_no'] },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#transactions-in-page-view").html(), { variable: 'data' })({ transactions: response.transactions, users: response.users });
				that.$el.html(template);

				$("#all-transactions-page-current").text(that.attributes['page_no']);
				if(that.attributes['page_no'] == 1)
					$("#all-transactions-prev-page-link").hide();

				if(that.attributes['page_no'] != 1)
					$("#all-transactions-prev-page-link").show();
				
				if(that.attributes['page_no'] == parseInt($("#all-transactions-page-total").text(), 10))
					$("#all-transactions-next-page-link").hide();

				if(that.attributes['page_no'] != parseInt($("#all-transactions-page-total").text(), 10))
					$("#all-transactions-next-page-link").show();
			}
		});
	}
});

var PendingTransactionsView = Backbone.View.extend({
	el: '#inner-menu-contents',
	render: function () {
		$("#inner-menu a").removeClass('inner-menu-active');
		$("#transactions-menu-pending").addClass('inner-menu-active');

		var that = this;

		$("#inner-menu-contents").html('<img src="images/loader.gif" />');
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'GetPendingTransactions' },
			dataType: 'JSON',
			success: function(response) { 
				var template = _.template($("#pending-transactions-view").html(), { variable: 'data' })({ transactions: response.transactions, users: response.users });
				that.$el.html(template);
			}
		});
	},
	events: {
		'click .transaction-check-status': 'checkTransactionStatus'
	},
	checkTransactionStatus: function(e) {
		if($(e.currentTarget).attr('data-in-progress') == 1)
			return;

		$(e.currentTarget).text('Checking ...').attr('data-in-progress', 1);
		Backbone.ajax({
			type: 'GET',
			url: 'ajax-operations.php',
			cache: false,
			data: { command: 'CheckTransactionStatus', transaction_id: $(e.currentTarget).attr('data-transaction-id'), merchant_transaction_id: $(e.currentTarget).attr('data-merchant-transaction-id') },
			dataType: 'JSON',
			success: function(response) { 
				if(response.completed == 1)
					$(e.currentTarget).parent().html('<span class="transaction-completed">Completed</span><span class="transaction-recheck-message">Credits transferred to user</span>');
				else if(response.completed == -1)
					$(e.currentTarget).parent().html('<span class="transaction-failed">' + response.details.payment_status + '</span><span class="transaction-recheck-message">' + response.details.transaction_status_message + '</span>');
				else if(response.completed == 0)
					$(e.currentTarget).parent().html(response.details.payment_status + '<span class="transaction-recheck-message">' + response.details.transaction_status_message + '</span>');
			},
			error: function(response) {
				$(e.currentTarget).text('Check').attr('data-in-progress', 0);
				alert(response.responseJSON.message);
			}
		});
	}
});

var $_TRANSACTIONS_MENU_VIEW,
	$_ALL_TRANSACTIONS_VIEW,
	$_TRANSACTIONS_IN_PAGE_VIEW,
	$_PENDING_TRANSACTIONS_VIEW;