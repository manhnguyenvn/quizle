<div id="contact-container">
	<span id="contact-header"><?= $__LANGUAGE_STRINGS['contact']['CONTACT_HEADER'] ?></span><span id="contact-email"></span>
</div>

<script>
	$.ajax({
	    type: 'GET',
	    url: LOCATION_SITE + 'ajax/contact.php',
	    cache: false,
	    data: { command: 'GetContactEmail' },
	    dataType: 'JSON',
	    success: function(response) {
	        $("#contact-email").append(response.contact_email);
	    },
	    error: function(error_response) {
	    }
	});
</script>