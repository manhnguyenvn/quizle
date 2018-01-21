<div id="footer">
	<span id="activated-languages" data-language-code="<?= $__LANGUAGE_CODE_CURRENT ?>"><?= $__LANGUAGE_SETTINGS['language_name'] ?></span>
    <div id="footer-links">
        <a class="footer-link" href="<?= LOCATION_SITE ?>pages/privacy.php"><?= $__LANGUAGE_STRINGS['footer']['PRIVACY'] ?></a>
        <a class="footer-link" href="<?= LOCATION_SITE ?>pages/terms.php"><?= $__LANGUAGE_STRINGS['footer']['TERMS'] ?></a>
        <a class="footer-link" href="<?= LOCATION_SITE . str_replace('URL_NAME', 'contact', $__NO_PARAMETER_URL) ?>"><?= $__LANGUAGE_STRINGS['footer']['CONTACT'] ?></a>
    </div>
</div>

<script>

$("#activated-languages").on('click', function() {
    if($("#activated-languages-list").is(":visible")) {
    	$("#activated-languages-list").remove();
    	return;
    }
    else if($("#activated-languages").attr('data-in-progress') == 1)
    	return;

    $("#activated-languages").attr('data-in-progress', 1).css('opacity', '0.6');
    $.ajax({
        type: 'GET',
        url: LOCATION_SITE + 'ajax/languages.php',
        cache: false,
        data: { command: 'GetActivatedLanguages' },
        dataType: 'JSON',
        success: function(response) {
            $("#activated-languages").attr('data-in-progress', 0).css('opacity', '1');

            var current_language = $("#activated-languages").attr('data-language-code'),
                html;

            if(response.languages.length == 1 && response['languages'][0]['language_code'] == current_language)
                return;

            html = '<div id="activated-languages-list">';

            for(var i=0; i<response.languages.length; i++) {
            	if(response['languages'][i]['language_code'] != current_language)
            		html += '<a href="' + LOCATION_SITE + (response.pretty_urls == 1 ? response['languages'][i]['language_code'] : '?language_code=' + response['languages'][i]['language_code']) + '" class="activated-language-option" data-language-id="' + response['languages'][i]['language_code'] + '">' + response['languages'][i]['language_name'] + '</a>';
            }
            html += '</div>';

            $("#footer").append(html);

        },
        error: function(error_response) {
        	$("#activated-languages").attr('data-in-progress', 0).css('opacity', '1');
        }
    });
});

</script>
