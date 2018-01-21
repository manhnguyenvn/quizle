$("#edit-email").on('click', function() {
    $("#edit-email").hide();
    $("#submit-email-button").show();
    $("#user-email").removeAttr('readonly');
});

$("#submit-email-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    var email_reg_exp = /^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/;

    $("#email-error-container").hide();

    if(!email_reg_exp.test($.trim($("#user-email").val()))) {
        $("#email-error-container").text(LANGUAGE_STRINGS['INCORRECT_EMAIL']).show();
        return;
    }

    $("#submit-email-button").attr('data-in-progress', 1).css('opacity', '0.7');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/profile.php?command=ChangeEmail',
        data: { email: $.trim($("#user-email").val()) },
        cache: false,
        dataType: 'JSON',
        success: function(response) { 
            $("#submit-email-button").attr('data-in-progress', 0).css('opacity', '1').hide();
            $("#edit-email").show();
            $("#user-email").attr('readonly', 'readonly');
            
            if(response.confirmation_required == 1)
                $("#confirmation-code-container").show();
        },
        error: function(response) {
            $("#submit-email-button").attr('data-in-progress', 0).css('opacity', '1');
            $("#email-error-container").text(response.responseJSON.message).show();
        }
    });
});

$("#resend-confirmation-code").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    $("#resend-confirmation-code").attr('data-in-progress', 1).css('opacity', '0.7');
    $("#confirmation-code-container").slideUp(400);
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/profile.php?command=ResendConfirmationCode',
        cache: false,
        dataType: 'JSON',
        success: function(response) { 
            $("#resend-confirmation-code").attr('data-in-progress', 0).css('opacity', '1');
            $("#confirmation-code-container").slideDown(400);
        },
        error: function(response) {
            $("#resend-confirmation-code").attr('data-in-progress', 0).css('opacity', '1');
            $("#email-error-container-2").text(response.responseJSON.message).show();
        }
    });
});

$("#confirmation-code-submit-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/;

    $("#email-error-container-2").hide();
    $("#confirmation-code-wrong").hide();

    if(!blank_reg_exp.test($.trim($("#confirmation-code").val()))) {
        $("#confirmation-code-wrong").show();
        setTimeout(function() {
            $("#confirmation-code-wrong").hide();
        }, 2000);
        return;
    }

    $("#confirmation-code-submit-button").attr('data-in-progress', 1).css('opacity', '0.7');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/profile.php?command=CheckConfirmationCode',
        data: { confirmation_code: $.trim($("#confirmation-code").val()) },
        cache: false,
        dataType: 'JSON',
        success: function(response) { 
            $("#confirmation-code-submit-button").attr('data-in-progress', 0).css('opacity', '1');
            if(response.confirmation_code_correct == -1) {
                $("#confirmation-code-wrong").show();
                setTimeout(function() {
                    $("#confirmation-code-wrong").hide();
                }, 2000);
            }
            else if(response.confirmation_code_correct == 1) {
                $("#confirmation-code-correct").show();
                setTimeout(function() {
                    $("#confirmation-code-correct").hide();
                    $("#confirmation-code").val('');
                    $("#confirmation-code-container").slideUp(400);
                }, 2000);
            }
        },
        error: function(response) {
            $("#confirmation-code-submit-button").attr('data-in-progress', 0).css('opacity', '1');
            $("#email-error-container-2").text(response.responseJSON.message).show();
        }
    });
});