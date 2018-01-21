<div id="sidebar">
	<div id="sidebar-create-post-container">
		<a href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-quiz', $__NO_PARAMETER_URL) ?>"><?= $__LANGUAGE_STRINGS['home_page']['CREATE_OWN_POST'] ?></a>
	</div>
	<div id="sidebar-posts-ad-container">
		<div id="facebook-page-container" style="<?= FACEBOOK_PAGE_URL == '' ? 'display:none': '' ?>">
			<?= FACEBOOK_PAGE_URL != '' ? '<div class="fb-page" data-href="' . FACEBOOK_PAGE_URL . '" data-tabs="timeline" data-height="100" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"></div>' : '' ?>
		</div>
	</div>
</div>