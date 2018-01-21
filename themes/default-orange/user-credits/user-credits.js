$("#credits-available-button").on('click', function() {
    $(".credits-tab").removeClass('theme-active-button').addClass('theme-passive-button');
    $(this).removeClass('theme-passive-button').addClass('theme-active-button');
    $("#credits-available-container").show();

    $("#purchase-credits-container").hide();
    $("#transactions-container").hide();
});

$("#credits-purchase-button").on('click', function() {
    $(".credits-tab").removeClass('theme-active-button').addClass('theme-passive-button');
    $(this).removeClass('theme-passive-button').addClass('theme-active-button');
    $("#purchase-credits-container").show();

    $("#credits-available-container").hide();
    $("#transactions-container").hide();
});

$("#credits-transactions-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    $(".credits-tab").removeClass('theme-active-button').addClass('theme-passive-button');
    $(this).removeClass('theme-passive-button').addClass('theme-active-button');
    $("#transactions-container").show();

    $("#credits-available-container").hide();
    $("#purchase-credits-container").hide();

    $("#transactions-container").html('<i id="transactions-loader" class="fa fa-spin fa-spinner"></i>');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/credits.php?command=GetTransactions',
        cache: false,
        dataType: 'JSON',
        success: function(response) { 
            var html = '';

            for(var i=0; i<response.transactions.length; i++) {
                html += '<div class="single-transaction">';
                if(response.transactions[i].completed == 'PENDING')
                    html += '<div class="transaction-field transaction-not-completed">' + LANGUAGE_STRINGS['PAYMENT_PENDING_MESSAGE'] + '</div>'; 
                if(response.transactions[i].completed == 'FAILED')
                    html += '<div class="transaction-field transaction-failed">' + LANGUAGE_STRINGS['PAYMENT_FAILED_MESSAGE'] + '</div>'; 
                html += '<div class="transaction-field"><label>' + LANGUAGE_STRINGS['TRANSACTION_DATE'] + '</label>' + response.transactions[i]['time'] + '</div>'; 
                html += '<div class="transaction-field"><label>' + LANGUAGE_STRINGS['TRANSACTION_ID'] + '</label>' + response.transactions[i]['transaction_id'] + ' (' + LANGUAGE_STRINGS[response.transactions[i]['merchant']] + ')' +  '</div>'; 
                html += '<div class="transaction-field"><label>' + LANGUAGE_STRINGS['TRANSACTION_AMOUNT'] + '</label>' + response.transactions[i]['amount'] + '</div>'; 
                html += '<div class="transaction-field"><label>' + LANGUAGE_STRINGS['CREDITS'] + '</label>' + response.transactions[i]['num_credits'] + '</div>'; 
                html += '</div>'; 
            }

            $("#transactions-container").html(html);
        },
        error: function(response) {
            $(that).attr('data-in-progress', 0).css('opacity', '1');
            $("#pay-loader").hide();
            $("#token-error").html(response.responseJSON.message).show();
        }
    });
});

$("#credits-pack-quantity").on('change', function() {
    $("#final-credits-purchased").text($(this).attr('data-credits-unit') * $(this).val());
    $("#purchased-credits-purchased-price").text($(this).attr('data-price-unit') * $(this).val());
});

$("#pay-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    var that = this;

    $("#token-error").hide();
    $(this).attr('data-in-progress', 1).css('opacity', '0.5');
    $("#pay-loader").css('display', 'inline-block');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/credits.php?command=GetPaymentToken',
        cache: false,
        data: { quantity: $("#credits-pack-quantity").val(), payment_name: LANGUAGE_STRINGS['CREDITS_PACK'] },
        dataType: 'JSON',
        success: function(response) { 
            $(that).attr('data-in-progress', 0).css('opacity', '1');
            $("#pay-loader").hide();
            if('url' in response) 
                document.location = response.url;
        },
        error: function(response) {
            $(that).attr('data-in-progress', 0).css('opacity', '1');
            $("#pay-loader").hide();
            $("#token-error").html(response.responseJSON.message).show();
        }
    });
});