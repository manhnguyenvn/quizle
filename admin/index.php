<?php
session_start();

if(!isset($_SESSION['admin'])) {
	header('Location: login.php');
	exit();
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Quizzio Admin</title>
<meta charset="UTF-8">
<link href='css/index.css' rel='stylesheet' type='text/css' />
<link href='css/statistics.css' rel='stylesheet' type='text/css' />
<link href='css/posts.css' rel='stylesheet' type='text/css' />
<link href='css/users.css' rel='stylesheet' type='text/css' />
<link href='css/settings.css' rel='stylesheet' type='text/css' />
<link href='css/languages.css' rel='stylesheet' type='text/css' />
<link href='css/transactions.css' rel='stylesheet' type='text/css' />
<link href='css/crons.css' rel='stylesheet' type='text/css' />
<link href='css/jquery.datetimepicker.css' rel='stylesheet' type='text/css' />
<script src="../js/jquery-1.11.3.min.js"></script>
<script src="js/html.sortable.min.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script src="../js/tooltipsy.min.js"></script>
<script src="js/underscore-min.js"></script>
<script src="js/backbone-min.js"></script>
</head>

<body>

<div id="main-container"></div>

<script type="text/template" id="initial-view">
	<div id="menu">
		<a id="stats-menu" href="#/stats">Statistics</a>
		<a id="posts-menu" href="#/posts">Posts</a>
		<a id="users-menu" href="#/users">Users</a>
		<a id="settings-menu" href="#/settings">Settings</a>
		<a id="languages-menu" href="#/languages">Languages</a>
		<a id="transactions-menu" href="#/transactions">Transactions</a>
		<a id="cron-menu" href="#/crons">Cron Status</a>
		<a id="logout-menu" href="logout.php">Logout</a>
	</div>
	<div id="menu-contents"></div>
	<div id="single-detailed-post-container"></div>
</script>

<script type="text/template" id="statistics-menu-view">
	<div class="inner-menu">
		<a id="stats-menu-today" href="#/stats/today">Today</a>
		<a id="stats-menu-search" href="#/stats/search">Search</a>
	</div>
	<div id="inner-menu-contents"></div>
</script>

<script type="text/template" id="statistics-today-view">
	<div id="statistics-data"></div>
</script>

<script type="text/template" id="statistics-search-view">
	<input type="text" readonly id="statistics-search-date" /><button id="search-statistics">Search</button>
	<div id="statistics-data"></div>
</script>

<script type="text/template" id="statistics-data-view">
	<table id="statistics-data-table">
		<tr>
			<td>Quizzes Views</td>
			<td><%= data.statistics.view_count %></td>
		</tr>
		<tr>
			<td>Quizzes Played</td>
			<td><%= data.statistics.played_count %></td>
		</tr>
		<tr>
			<td>Quizzes Fully Played</td>
			<td><%= data.statistics.fully_played_count %></td>
		</tr>
		<tr>
			<td>Quizzes Shares</td>
			<td><%= data.statistics.share_count %></td>
		</tr>
		<tr>
			<td>Quizzes Comments</td>
			<td><%= data.statistics.comment_count %></td>
		</tr>
		<tr>
			<td>Quizzes Created</td>
			<td><%= data.statistics.posts_count %></td>
		</tr>
		<tr>
			<td>Users Joined</td>
			<td><%= data.statistics.users_count %></td>
		</tr>
		<tr>
			<td>Credits Consumed By Premium Quizzes</td>
			<td><%= data.statistics.credits_consumed_count %></td>
		</tr>
		<tr>
			<td>Credits Purchased By Users</td>
			<td><%= data.statistics.credits_purchased_count %></td>
		</tr>
	</table>
</script>

<script type="text/template" id="posts-menu-view">
	<div class="inner-menu">
		<a id="posts-menu-all" href="#/posts/all">All</a>
		<a id="posts-menu-featured" href="#/posts/featured">Featured</a>
	</div>
	<div id="inner-menu-contents"></div>
</script>

<script type="text/template" id="posts-languages-view">
	<div id="posts-languages-container">
		<select id="posts-languages-dropdown">
		<%
		if(data.source == 'all')
			print('<option value="ALL">ALL</option>');
		for(var i=0; i<data.languages.length; i++) {
			print('<option value="' + data.languages[i]['language_code'] + '">' +  data.languages[i]['language_name'] + '</option>');
		}
		%>
		</select>
	</div>
	<div id="posts-languages-posts-container"></div>
</script>

<script type="text/template" id="all-posts-view">
	<% 
	if(data.num_pages == 0)
		print('<div class="all-posts-empty">No Posts</div>');
	else {
	%>
		<div id="posts-in-page-container"></div>
		<div id="pages-link-container">Page <span id="all-posts-page-current"></span> of <span id="all-posts-page-total"><%= data.num_pages %></span><span id="all-posts-prev-page-link">Previous Page</span><span id="all-posts-next-page-link">Next Page</span></div>
	<%
	}
	%>
</script>

<script type="text/template" id="post-in-page-view">
	<% 
	if(data.posts.length == 0)
		print('<div class="all-posts-empty">No Posts</div>');
	else {
	%>
		<table id="post-in-page-table">
			<thead>
				<tr>
					<th class="post-in-page-id-td">POST ID</th>
					<th class="post-in-page-title-td">POST TITLE</th>
					<th class="post-in-page-status-td">STATUS</th>
				</tr>
			</thead>
			<tbody>
			<%
			_.each(data.posts, function(post) { 
				var hidden_mode = post['post_published'] == 1 ? (post['post_hidden'] == 1 ? 1 : 0) : 0,
	            	draft_mode = post['post_published'] == 0 ? 1 : 0,
	            	post_title = post['post_title'] == '' ? '<span style="font-style:italic">UNTITLED QUIZ</span>' : post['post_title'],
	            	status = (draft_mode == 1 ? 'DRAFT' : (hidden_mode == 1 ? 'HIDDEN' : 'PUBLISHED')),
	            	image_html;

	            if(post['post_image_id'] == null)
                    image_html = '<span class="post-no-image">NO IMAGE</span>';
                else 
                    image_html = '<img class="post-image" src="../img/QUIZ/quiz/' + post['post_image_id'] + '" />'; 

				print('<tr>');
				print('<td class="post-in-page-id-td">' + post['post_id'] + '</td>');
				print('<td class="post-in-page-title-td">' + image_html + '<span class="post-detailed-link post-title" data-source="all-posts-table" data-post-id="' + post['post_id'] + '"><span class="post-detailed-link-title">' + post_title + '</span></span></td>');
				print('<td class="post-in-page-status-td">' + status + '</td>');
				print('</tr>');
			});
			%>
			</tbody>
		</table>
	<%
	}
	%>
</script>

<script type="text/template" id="post-detailed-view">
	<%
	var hidden_mode = data.post['post_published'] == 1 ? (data.post['post_hidden'] == 1 ? 1 : 0) : 0,
    	draft_mode = data.post['post_published'] == 0 ? 1 : 0,
    	post_title = data.post['post_title'] == '' ? '<span style="font-style:italic">UNTITLED QUIZ</span>' : data.post['post_title'],
    	status = (draft_mode == 1 ? 'DRAFT MODE' : (hidden_mode == 1 ? 'HIDDEN' : 'PUBLISHED')),
    	image_html;

    if(data.post['post_image_id'] == null)
        image_html = '<span class="post-no-image">NO IMAGE</span>';
    else 
        image_html = '<img class="post-image" src="../img/QUIZ/quiz/' + data.post['post_image_id'] + '" />'; 
	%>
	<div id="post-detailed-container-close">CLOSE</div>
	<div id="post-detailed-container" data-post-id="<%= data.post['post_id'] %>" data-language-code="<%= data.post['language_code'] %>">
		<div id="post-info-status" class="<%= (status == 'PUBLISHED' ? 'post-info-status-published' : '') %>"><%= status %></div>
		<div id="post-info-delete-button" data-language-code="<%= data.post['language_code'] %>" data-user-id="<%= data.post['user']['user_id'] %>">DELETE</div>
		<div id="post-info-container">
			<%= image_html %>
			<div id="post-info-text">
				<div id="post-info-title"><%= post_title %></div>
				<div id="post-info-description"><%= data.post['post_description'] %></div>
			</div>
		</div>
		<div id="post-info-language">
			<div class="post-info-labels">
				<label>Language</label>
				<span class="post-info-label-content"><%= data.post['language_code'] %></span>
			</div>
			<%
			if(status !== 'DRAFT MODE') {
			%>
			<div class="post-info-labels">
				<label>Link On Site</label>
				<span class="post-info-label-content"><a href="<%= '../post.php?language_code=' + data.post['language_code'] + '&post_id=' + data.post['post_id'] %>" target="_blank">Here</a></span>
			</div>
			<%
			}
			%>
		</div>
		<%
		if(status === 'PUBLISHED' && data.post['post_is_premium'] != 1) {
		%>
		<div id="post-info-featured">
			<div class="post-info-labels">
				<label>Featured</label>
				<span class="post-info-label-content">
					<select id="post-change-featured-status">
						<option value="1" <%= (data.post['featured'] == 1 ? 'selected' : '') %>>YES</option>
						<option value="0" <%= (data.post['featured'] == 0 ? 'selected' : '') %>>NO</option>
					</select>
				</span>
			</div>
		</div>
		<%
		}
		%>
		<div id="post-info-user">
			<div class="post-info-labels">
				<label>Created By</label>
				<span class="post-info-label-content"><img src="<%= data.post['user']['user_picture_url'] %>" /> <span><%= data.post['user']['user_full_name'] %></span><span class="post-info-label-content-light">on <%= data.post['post_created_date'] %></span></span>
			</div>
			<%
			if(data.post['post_is_premium'] == 1) {
			%>
			<div class="post-info-labels">
				<label>User Credits Remaining</label>
				<span class="post-info-label-content"><%= data.post['user']['user_available_credits'] %></span>
			</div>
			<%
			}
			%>
		</div>
		<%
		if(data.post['post_was_premium'] == 1) {
		%>
			<div id="post-info-premium">
				<div class="post-info-labels">
					<label>Post Premium Currently</label>
					<span class="post-info-label-content"><%= (data.post['post_is_premium'] == 1 ? 'YES' : 'NO') %></span>
				</div>
				
				<%
				if(data.post['post_is_premium'] == 0) {
				%>
				<div class="post-info-labels">
					<label>Post Premium <br />In Past</label>
					<span class="post-info-label-content">YES</span>
				</div>
				<%
				}
				%>

				<%
				if(data.post['post_is_premium'] == 1) {
				%>
				<div class="post-info-labels">
					<label>Domain Used</label>
					<span class="post-info-label-content"><%= data.post['post_premium_domain'] %></span>
				</div>
				<%
				}
				%>
			</div>
		<%
		}
		%>
		
		<%
		if(status !== 'DRAFT MODE') {
		%>
		<span id="post-info-get-stats">GET STATISTICS</span>
		<%
		}
		%>
		<div id="post-info-stats">
			<div class="post-info-labels">
				<label>Views Count</label>
				<span class="post-info-label-content" id="post-info-views"></span>
			</div>
			<div class="post-info-labels">
				<label>Played Count</label>
				<span class="post-info-label-content" id="post-info-played"></span>
			</div>
			<div class="post-info-labels">
				<label>Shared Count</label>
				<span class="post-info-label-content" id="post-info-shared"></span>
			</div>
			<div class="post-info-labels">
				<label>Comments Count</label>
				<span class="post-info-label-content" id="post-info-comments"></span>
			</div>
			<div class="post-info-labels">
				<label>Credits Consumed</label>
				<span class="post-info-label-content" id="post-info-credits"></span>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="featured-posts-view">
	<% 
	if(data.featured_posts.length == 0)
		print('<div class="all-posts-empty">There are no featued posts assigned. The 3 most recent posts will be automatically assigned as featured during display</div>');
	else {
	%>
		<table id="featured-posts-table">
			<thead>
				<tr>
					<th class="post-in-page-id-td">POST ID</th>
					<th class="post-in-page-title-td">POST TITLE</th>
				</tr>
			</thead>
			<tbody>
			<%
			_.each(data.featured_posts, function(post) { 
				var post_title = post['post_title'] == '' ? '<span style="font-style:italic">UNTITLED QUIZ</span>' : post['post_title'],
	            	image_html = '<img class="post-image" src="../img/QUIZ/quiz/' + post['post_image_id'] + '" />';

				print('<tr>');
				print('<td class="post-in-page-id-td">' + post['post_id'] + '</td>');
				print('<td class="post-in-page-title-td">' + image_html + '<span class="post-detailed-link post-title" data-source="featured-posts-table" data-post-id="' + post['post_id'] + '"><span class="post-detailed-link-title">' + post_title + '</span></span></td>');
				print('</tr>');
			});
			%>
			</tbody>
		</table>
	<%
	}
	%>
</script>

<script type="text/template" id="all-users-view">
	<% 
	if(data.num_pages == 0)
		print('<div class="all-users-empty">No Users</div>');
	else {
	%>
		<div id="users-in-page-container"></div>
		<div id="users-pages-link-container">Page <span id="all-users-page-current"></span> of <span id="all-users-page-total"><%= data.num_pages %></span><span id="all-users-prev-page-link">Previous Page</span><span id="all-users-next-page-link">Next Page</span></div>
	<%
	}
	%>
</script>

<script type="text/template" id="users-in-page-view">
	<% 
	if(data.users.length == 0)
		print('<div class="all-users-empty">No Users</div>');
	else {
	%>
		<table id="users-in-page-table">
			<thead>
				<tr>
					<th class="users-in-page-id-td">USER ID</th>
					<th class="users-in-page-name-td">USER</th>
					<th class="users-in-page-source-td">SOURCE</th>
					<th class="users-in-page-email-td">EMAIL</th>
					<th class="users-in-page-premium-td">PREMIUM</th>
				</tr>
			</thead>
			<tbody>
			<%
			_.each(data.users, function(user) { 
				print('<tr>');
				print('<td class="users-in-page-id-td">' + user['user_id'] + '</td>');
				print('<td class="users-in-page-name-td"><img src="' + user['user_picture_url'] + '" /><span>' + user['user_full_name'] + '</span></td>');
				print('<td class="users-in-page-source-td">' + user['registration_source'] + '</td>');
				print('<td class="users-in-page-email-td">' + (user['user_email'] != null ? user['user_email'] + (user['user_email_confirmed'] == 0 ? '<span>Not Confirmed</span>' : '') : '-') + '</td>');
				print('<td class="users-in-page-premium-td">' + (user['user_premium'] == 1 ? 'Yes <span>' + user['user_available_credits'] + ' credits</span>' : '-') + '</td>');
				print('</tr>');
			});
			%>
			</tbody>
		</table>
	<%
	}
	%>
</script>

<script type="text/template" id="settings-menu-view">
	<div class="inner-menu">
		<a id="site-menu" href="#/settings/site">General</a>
		<a id="sound-menu" href="#/settings/sound">Sound</a>
		<a id="email-menu" href="#/settings/email">Email</a>
		<a id="premium-menu" href="#/settings/premium">Premium</a>
		<a id="locale-menu" href="#/settings/locale">Locale</a>
		<a id="ads-menu" href="#/settings/ads">Ads</a>
		<a id="login-menu" href="#/settings/login">Login Info</a>
	</div>
	<div id="inner-menu-contents"></div>
</script>

<script type="text/template" id="site-settings-view">
	<div class="form-element">
		<label for="site-name">Site Name</label>
		<input type="text" id="site-name" value="<%= data.settings.site_name %>" />
	</div>
	<div class="form-element">
		<label for="site-logo">Site Logo</label>
		<div id="site-logo-container">
			<img src="../img/<%= data.settings.logo_name + '?' + data.settings.logo_cache %>" />
			<button id="site-logo-change">Change</button>
			<input type="file" id="site-logo" />
		</div>
		<span class="hastip" title="JPEG / PNG<br /><br />Use a logo sized image">?</span>
	</div>
	<div class="form-element">
		<label for="facebook-app-id">Facebook App Id</label>
		<input type="text" id="facebook-app-id" value="<%= data.settings.facebook_app_id %>" />
	</div>
	<div class="form-element">
		<label for="facebook-app-secret">Facebook App Secret</label>
		<input type="text" id="facebook-app-secret" value="<%= data.settings.facebook_app_secret %>" />
	</div>
	<div class="form-element">
		<label for="facebook-page-url">Facebook Page URL</label>
		<input type="text" id="facebook-page-url" value="<%= data.settings.facebook_page_url %>" />
	</div>
	<div class="form-element">
		<label for="ga-code">Google Analytics Code</label>
		<textarea id="ga-code"><%= data.settings.ga_code %></textarea>
	</div>
	<div class="form-element">
		<label for="max-image-size">Allowed Image Size</label>
		<select id="max-image-size">
			<option value="0.5" <%= data.settings.max_image_size == '0.5' ? 'selected' : '' %>>0.5 MB</option>
			<option value="1" <%= data.settings.max_image_size == '1' ? 'selected' : '' %>>1 MB</option>
			<option value="1.5" <%= data.settings.max_image_size == '1.5' ? 'selected' : '' %>>1.5 MB</option>
			<option value="2" <%= data.settings.max_image_size == '2' ? 'selected' : '' %>>2 MB</option>
		</select>
	</div>
	<div class="form-element">
		<label for="current-theme">Theme</label>
		<select id="current-theme">
			<%
			_.each(data.themes, function(theme) {
				print('<option value="' + theme + '" ' + (data.settings.current_theme == theme ? 'selected' : '') + '>' + theme + '</option>');
			});
			%>
		</select>
	</div>
	<div class="form-element">
		<label for="font-family">Font</label>
		<select id="font-family">
			<option value="DEFAULT-Arial" <%= data.settings.font_family == 'DEFAULT-Arial' ? 'selected' : '' %>>Arial</option>
			<option value="GOOGLE-Roboto" <%= data.settings.font_family == 'GOOGLE-Roboto' ? 'selected' : '' %>>Google Fonts - Roboto</option>
			<option value="GOOGLE-Lato" <%= data.settings.font_family == 'GOOGLE-Lato' ? 'selected' : '' %>>Google Fonts - Lato</option>
			<option value="GOOGLE-Open Sans" <%= data.settings.font_family == 'GOOGLE-Open Sans' ? 'selected' : '' %>>Google Fonts - Open Sans</option>
		</select>
	</div>
	<div class="form-element">
		<label for="social-image">Share Image</label>
		<div id="social-image-container">
			<img src="../img/<%= data.settings.share_image_name + '?' + data.settings.share_image_cache %>" />
			<button id="social-image-change">Change</button>
			<input type="file" id="social-image" />
		</div>
		<span class="hastip" title="Image which will be seen on sharing the home page & posts page<br /><br />At the minimum use image of dimensions 600 x 315 px">?</span>
	</div>
	<button id="site-settings-button">UPDATE</button>
	<div id="site-settings-error"></div>
</script>

<script type="text/template" id="sound-settings-view">
	<div class="form-element">
		<label for="sound-allowed">Sound Allowed</label>
		<select id="sound-allowed">
			<option value="0" <%= data.settings.sound_allowed == '0' ? 'selected' : '' %>>No</option>
			<option value="1" <%= data.settings.sound_allowed == '1' ? 'selected' : '' %>>Yes</option>
		</select>
		<span class="hastip" title="Sound which will be heard when user answers a question correctly or incorrectly for a trivia quiz<br /><br />Upload files of type mp3<br /><br />User can mute the sound">?</span>
	</div>
	<div id="sound-settings-container" style="<%= data.settings.sound_allowed == 1 ? '' : 'display:none' %>">
		<div class="form-element">
			<label for="correct-sound" style="padding:10px 0 0 0">Correct Answer Sound</label>
			<div id="correct-sound-container">
				<span id="correct-sound-name" style="<%= data.settings.correct_sound_present == true ? 'display:none' : '' %>"><%= data.settings.correct_sound_present == true ? '' : 'NONE' %></span>
				<%= data.settings.correct_sound_present == true ? '<audio id="correct-sound-music" controls src="../music-files/correct.mp3?' + Math.random() + '"></audio>' : '' %>
				<button id="correct-sound-change">Change</button>
				<input type="file" id="correct-sound" />
			</div>
		</div>
		<div class="form-element">
			<label for="wrong-sound" style="padding:10px 0 0 0">Wrong Answer Sound</label>
			<div id="wrong-sound-container" style="padding:10px 0 0 0">
				<span id="wrong-sound-name" style="<%= data.settings.wrong_sound_present == true ? 'display:none' : '' %>"><%= data.settings.wrong_sound_present == true ? '' : 'NONE' %></span>
				<%= data.settings.wrong_sound_present == true ? '<audio id="wrong-sound-music" controls src="../music-files/wrong.mp3?' + Math.random() + '"></audio>' : '' %>
				<button id="wrong-sound-change">Change</button>
				<input type="file" id="wrong-sound" />
			</div>
		</div>
	</div>
	<button id="sound-settings-button">UPDATE</button>
	<div id="sound-settings-error"></div>
</script>

<script type="text/template" id="email-settings-view">
	<div class="form-element">
		<label for="email-from">From Email</label>
		<input type="text" id="email-from" value="<%= data.settings.email_from %>" />
		<span class="hastip" title="Email address from which emails will be sent to users">?</span>
	</div>
	<div class="form-element">
		<label for="email-from-name">From Name</label>
		<input type="text" id="email-from-name" value="<%= data.settings.email_from_name %>" />
		<span class="hastip" title="Name / Label associated with the above email address">?</span>
	</div>
	<div class="form-element">
		<label for="smtp-remply">Email Method</label>
		<select id="email-method">
			<option value="0" <%= data.settings.smtp_used == '0' ? 'selected' : '' %>>PHP Mail</option>
			<option value="1" <%= data.settings.smtp_used == '1' ? 'selected' : '' %>>SMTP</option>
		</select>
	</div>
	<div id="smtp-settings-container" style="<%= data.settings.smtp_used == 1 ? '' : 'display:none' %>">
		<div class="form-element">
			<label for="smtp-host">SMTP Host</label>
			<input type="text" id="smtp-host" value="<%= data.settings.server_smtp_host %>" />
		</div>
		<div class="form-element">
			<label for="smtp-port">SMTP Port</label>
			<input type="text" id="smtp-port" value="<%= data.settings.server_smtp_port %>" />
		</div>
		<div class="form-element">
			<label for="smtp-security">SMTP Security</label>
			<select id="smtp-security">
				<option value="tls" <%= data.settings.server_smtp_security == 'tls' ? 'selected' : '' %>>TLS</option>
				<option value="ssl" <%= data.settings.server_smtp_security == 'ssl' ? 'selected' : '' %>>SSL</option>
			</select>
		</div>
		<div class="form-element">
			<label for="smtp-username">SMTP Username</label>
			<input type="text" id="smtp-username" value="<%= data.settings.server_smtp_username %>" />
		</div>
		<div class="form-element">
			<label for="smtp-password">SMTP Password</label>
			<input type="text" id="smtp-password" value="<%= data.settings.server_smtp_password %>" />
		</div>
	</div>
	<button id="email-settings-button">UPDATE</button>
	<div id="email-settings-error"></div>
</script>

<script type="text/template" id="premium-settings-view">
	<div class="form-element">
		<label for="smtp-remply">Premium Activated</label>
		<select id="premium-activated">
			<option value="0" <%= data.settings.activate_premium == '0' ? 'selected' : '' %>>No</option>
			<option value="1" <%= data.settings.activate_premium == '1' ? 'selected' : '' %>>Yes</option>
		</select>
		<span class="hastip" title="Premium section activated or deactivated">?</span>
	</div>
	<div id="premium-settings-container" style="<%= data.settings.activate_premium == 1 ? '' : 'display:none' %>">
		<div class="form-element">
			<label for="transaction-currency">Currency</label>
			<select id="transaction-currency">
				<option value="USD" <%= data.settings.transaction_currency == 'USD' ? 'selected' : '' %>>US Dollar</option>
				<option value="EUR" <%= data.settings.transaction_currency == 'EUR' ? 'selected' : '' %>>Euro</option>
				<option value="GBP" <%= data.settings.transaction_currency == 'GBP' ? 'selected' : '' %>>UK Pound</option>
			</select>
		</div>
		<div class="form-element">
			<label for="credits-quantity">Credits Quantity</label>
			<input type="text" id="credits-quantity" value="<%= data.settings.credits_quantity %>" />
			<span class="hastip" title="Example : You are offering 1000 credits for 5 USD. In this case 1000 is the Credits Quantity">?</span>
		</div>
		<div class="form-element">
			<label for="credits-value">Credits Value</label>
			<input type="text" id="credits-value" value="<%= data.settings.credits_value %>" />
			<span class="hastip" title="Example : You are offering 10000 credits for 5 USD. In this case 5 is the Credits Value">?</span>
		</div>
		<div class="form-element">
			<label for="premium-quiz-credits">Premium Quiz Credits</label>
			<input type="text" id="premium-quiz-credits" value="<%= data.settings.premium_quiz_credits %>" />
			<span class="hastip" title="Credits required to make a quiz premium. You can set it to 0 if you want">?</span>
		</div>
		<div class="form-element">
			<label for="paypal-sandbox-activated">Paypal</label>
			<select id="paypal-sandbox-activated">
				<option value="0" <%= data.settings.paypal_sandbox_activated == '0' ? 'selected' : '' %>>Live</option>
				<option value="1" <%= data.settings.paypal_sandbox_activated == '1' ? 'selected' : '' %>>Sandbox</option>
			</select>
			<span class="hastip" title="Sandbox option is just used to test Paypal transactions. Live option makes live transactions and real money is involved<br /><br />There are separate API keys for both">?</span>
		</div>
		<div id="paypal-live-settings" style="<%= data.settings.paypal_sandbox_activated == 0 ? '' : 'display:none' %>">
			<div class="form-element">
				<label for="paypal-api-username">Paypal Api Username</label>
				<input type="text" id="paypal-api-username" value="<%= data.settings.paypal_api_username %>" />
			</div>
			<div class="form-element">
				<label for="paypal-api-password">Paypal Api Password</label>
				<input type="text" id="paypal-api-password" value="<%= data.settings.paypal_api_password %>" />
			</div>
			<div class="form-element">
				<label for="paypal-api-signature">Paypal Api Signature</label>
				<input type="text" id="paypal-api-signature" value="<%= data.settings.paypal_api_signature %>" />
			</div>
		</div>
		<div id="paypal-sandbox-settings" style="<%= data.settings.paypal_sandbox_activated == 1 ? '' : 'display:none' %>">
			<div class="form-element">
				<label for="paypal-sandbox-api-username" class="normal-line-height">Paypal Sandbox Api <br />Username</label>
				<input type="text" id="paypal-sandbox-api-username" value="<%= data.settings.paypal_sandbox_api_username %>" />
			</div>
			<div class="form-element">
				<label for="paypal-sandbox-api-password" class="normal-line-height">Paypal Sandbox Api <br />Password</label>
				<input type="text" id="paypal-sandbox-api-password" value="<%= data.settings.paypal_sandbox_api_password %>" />
			</div>
			<div class="form-element">
				<label for="paypal-sandbox-api-signature" class="normal-line-height">Paypal Sandbox Api <br />Signature</label>
				<input type="text" id="paypal-sandbox-api-signature" value="<%= data.settings.paypal_sandbox_api_signature %>" />
			</div>
		</div>
	</div>
	<button id="premium-settings-button">UPDATE</button>
	<div id="premium-settings-error"></div>
</script>

<script type="text/template" id="locale-settings-view">
	<div class="form-element">
		<label for="default-language">Default Language</label>
		<select id="default-language">
			<%
			for(var i=0; i<data.languages.length; i++) {
				print('<option value="' + data.languages[i]['code'] + '" ' + (data.settings.language == data.languages[i]['code'] ? 'selected' : '') + '>' + data.languages[i]['name'] + '</option>');
			}
			%>
		</select>
	</div>
	<div class="form-element">
		<label for="timezone">Timezone</label>
		<select id="timezone">
			<%
			for(i=0; i<data.timezones.length; i++) {
				print('<option value="' + data.timezones[i] + '" ' + (data.settings.timezone == data.timezones[i] ? 'selected' : '') + '>' + data.timezones[i] + '</option>');
			}
			%>
		</select>
	</div>
	<button id="locale-settings-button">UPDATE</button>
	<div id="locale-settings-error"></div>
</script>

<script type="text/template" id="ads-settings-view">
	<div class="form-name">Maximum sizes of ads can be 300 x 250</div>
	<div class="form-element">
		<label for="ad-unit-1">Ad Unit 1</label>
		<textarea id="ad-unit-1" placeholder="Ad HTML" maxlength="1000"><%= data.settings.length >= 1 ? data.settings[0].ad_code : '' %></textarea>
	</div>
	<div class="form-element-extra">
		<div class="form-element-extra-inner">
			<input type="text" placeholder="Ad Width" value="<%= data.settings.length >= 1 ? data.settings[0].ad_width : '' %>" />
			<span>x</span>
			<input type="text" placeholder="Ad Height" value="<%= data.settings.length >= 1 ? data.settings[0].ad_height : '' %>" />
		</div>
	</div>
	<div class="form-element">
		<label for="ad-unit-2">Ad Unit 2</label>
		<textarea id="ad-unit-2" placeholder="Ad HTML" maxlength="1000"><%= data.settings.length >= 2 ? data.settings[1].ad_code : '' %></textarea>
	</div>
	<div class="form-element-extra">
		<div class="form-element-extra-inner">
			<input type="text" placeholder="Ad Width" value="<%= data.settings.length >= 2 ? data.settings[1].ad_width : '' %>" />
			<span>x</span>
			<input type="text" placeholder="Ad Height" value="<%= data.settings.length >= 2 ? data.settings[1].ad_height : '' %>" />
		</div>
	</div>
	<div class="form-element">
		<label for="ad-unit-3">Ad Unit 3</label>
		<textarea id="ad-unit-3" placeholder="Ad HTML" maxlength="1000"><%= data.settings.length >= 3 ? data.settings[2].ad_code : '' %></textarea>
	</div>
	<div class="form-element-extra">
		<div class="form-element-extra-inner">
			<input type="text" placeholder="Ad Width" value="<%= data.settings.length >= 3 ? data.settings[2].ad_width : '' %>" />
			<span>x</span>
			<input type="text" placeholder="Ad Height" value="<%= data.settings.length >= 3 ? data.settings[2].ad_height : '' %>" />
		</div>
	</div>
	<div class="form-element">
		<label for="ad-unit-4">Ad Unit 4</label>
		<textarea id="ad-unit-4" placeholder="Ad HTML" maxlength="1000"><%= data.settings.length == 4 ? data.settings[3].ad_code : '' %></textarea>
	</div>
	<div class="form-element-extra">
		<div class="form-element-extra-inner">
			<input type="text" placeholder="Ad Width" value="<%= data.settings.length == 4 ? data.settings[3].ad_width : '' %>" />
			<span>x</span>
			<input type="text" placeholder="Ad Height" value="<%= data.settings.length == 4 ? data.settings[3].ad_height : '' %>" />
		</div>
	</div>
	<button id="ads-settings-button">UPDATE</button>
	<div id="ads-settings-error"></div>
</script>

<script type="text/template" id="login-settings-view">
	<div class="form-element">
		<label for="login-username">Email</label>
		<input type="text" id="login-username" value="<%= data.settings.username %>" />
	</div>
	<div class="form-element">
		<label for="login-password">New Password</label>
		<input type="password" id="login-password" />
	</div>
	<div class="form-element">
		<label for="login-password-confirm" class="normal-line-height">Confirm New Password</label>
		<input type="password" id="login-password-confirm" />
	</div>
	<button id="login-settings-button">UPDATE</button>
	<div id="login-settings-error"></div>
</script>

<script type="text/template" id="languages-dropdown-view">
	<div id="languages-container">
		<select id="languages-dropdown">
		<% 
		for(var i=0; i<data.languages.length; i++) {
			print('<option value="' + data.languages[i]['language_code'] + '">' +  data.languages[i]['language_name'] + '</option>');
		}
		%>
		</select>
		<a href="#/languages/new" id="new-language-link">ADD LANGUAGE</a>
	</div>
	<div id="language-settings-container"></div>
</script>

<script type="text/template" id="language-settings-view">
	<div id="language-deactivated-notice" style="<%= data.settings.language_activated == '0' ? '' : 'display:none' %>">CURRENTLY DEACTIVATED</div>
	<div class="form-element">
		<label for="language-name">Language Name</label>
		<select id="language-name" disabled>
			<option data-direction="<%= data.settings.language_direction %>" value="<%= data.settings.language_code %>" selected><%= data.settings.language_name %></option>
		</select>
	</div>
	<div class="form-element">
		<label for="language-file-2">Language File</label>
		<div id="language-file-container">
			<span></span>
			<a href="../lang/<%= data.settings.language_code + '.txt?' + Math.random() %>" target="_blank"><%= data.settings.language_name %>.txt</a> 
			<button id="language-file-2-change">Change</button>
			<input type="file" id="language-file-2" />
		</div>
	</div>
	<div class="form-element">
		<label for="language-activated">Language Activated</label>
		<select id="language-activated">
			<option value="0" <%= data.settings.language_activated == '0' ? 'selected' : '' %>>No</option>
			<option value="1" <%= data.settings.language_activated == '1' ? 'selected' : '' %>>Yes</option>
		</select>
	</div>
	<div id="langauge-activated-container" style="<%= data.settings.language_activated == 1 ? '' : 'display:none' %>">
		<div class="form-element">
			<label>Categories & Tags<br /></label>
			<div id="langauge-tags-outer-container">
				<%
				for(var i in data.settings.categories) {
					data.settings.categories[i]['tags'] = _.sortBy(data.settings.categories[i]['tags'], "position");
				%>
				<div class="language-tags-container">
					<input type="text" maxlength="15" data-tag-id="<%= i %>" placeholder="Category Name" class="category-input" value="<%= data.settings.categories[i]['name'] %>" />
					<input type="text" maxlength="15" placeholder="Category Icon Name" class="icon-input" value="<%= data.settings.categories[i]['icon'] %>" />
					<ul class="language-tags-list" data-category-id="<%= i %>">
						<%
						for(var j=0; j<data.settings.categories[i]['tags'].length; j++) {
						%>
						<li class="tag-input-container">
							<input type="text" maxlength="15" data-tag-id="<%= data.settings.categories[i]['tags'][j]['id'] %>" placeholder="Tag Name" class="tag-input" value="<%= data.settings.categories[i]['tags'][j]['name'] %>" />
							<span class="language-delete-tag-button">-</span>
						</li>
						<%
						}
						%>
					</ul>
					<button class="language-add-tag-button" style="<%= data.settings.categories[i]['tags'].length == 6 ? 'display:none':'' %>">+</button>
				</div>
				<%
				}
				%>
			</div>
			<span class="hastip" title="You can add upto 3 categories<br /><br />You can add an icon for each category. Add the icon name in the field 'Category Icon Name' from http://fortawesome.github.io/Font-Awesome/icons/ <br /><br />Examples of icons can be tree, user, taxi etc<br /><br />There can be upto 6 tags in each category. You can change the position of tags by dragging them">?</span>
		</div>
		<div class="form-element">
			<label for="email-address-confirm" class="normal-line-height">Send email on email address change</label>
			<select id="email-address-confirm" disabled>
				<option value="0" <%= data.settings.send_email_confirm_email == '0' ? 'selected' : '' %>>No</option>
				<option value="1" <%= data.settings.send_email_confirm_email == '1' ? 'selected' : '' %>>Yes</option>
			</select>
			<span class="hastip" title="When user changes email address then an email will be sent to confirm his email address.<br /><br />First textbox is for email subject.<br />Second textbox is for email body">?</span>
			<div class="email-template-container" style="<%= data.settings.send_email_confirm_email == 1 ? '' : 'display:none' %>">
				<input id="email-confirm-subject" placeholder="Subject" type="text" value="<%= data.settings.confirm_email_template_subject %>" />
				<textarea id="email-confirm-body" placeholder="Body"><%= data.settings.confirm_email_template_body %></textarea>
				<span class="hastip shortcode-tooltip" title="You can use this shortcode in the email body. This shortcode will be replaced by the actual user full name in the email">USER_FULL_NAME</span>
				<span class="hastip shortcode-tooltip shortcode-tooltip-2" title="You can use this shortcode in the email body. This shortcode will be replaced by the actual email confirmation code in the email">CONFIRMATION_CODE</span>
			</div>
		</div>
		<div class="form-element">
			<label for="post-deleted-send-email" class="normal-line-height">Send email on post deletion</label>
			<select id="post-deleted-send-email">
				<option value="0" <%= data.settings.send_post_deleted_email == '0' ? 'selected' : '' %>>No</option>
				<option value="1" <%= data.settings.send_post_deleted_email == '1' ? 'selected' : '' %>>Yes</option>
			</select>
			<span class="hastip" title="When admin deletes a user's post, an email will be send to the user.<br /><br />First textbox is for email subject.<br />Second textbox is for email body">?</span>
			<div class="email-template-container" style="<%= data.settings.send_post_deleted_email == 1 ? '' : 'display:none' %>">
				<input id="post-deleted-subject" placeholder="Subject" type="text" value="<%= data.settings.post_deleted_email_template_subject %>" />
				<textarea id="post-deleted-body" placeholder="Body"><%= data.settings.post_deleted_email_template_body %></textarea>
				<span class="hastip shortcode-tooltip" title="You can use this shortcode in the email body. This shortcode will be replaced by the actual user full name in the email">USER_FULL_NAME</span>
				<span class="hastip shortcode-tooltip shortcode-tooltip-2" title="You can use this shortcode in the email body. This shortcode will be replaced by the actual title of the deleted post in the email">DELETED_POST_TITLE</span>
			</div>
		</div>
		<div class="form-element">
			<label for="email-credits-finished" class="normal-line-height">Send email when credits <br />finish</label>
			<select id="email-credits-finished">
				<option value="0" <%= data.settings.send_credits_finished_email == '0' ? 'selected' : '' %>>No</option>
				<option value="1" <%= data.settings.send_credits_finished_email == '1' ? 'selected' : '' %>>Yes</option>
			</select>
			<span class="hastip" title="Send email to user when his credits have finished.<br /><br />First textbox is for email subject.<br />Second textbox is for email body">?</span>
			<div class="email-template-container" style="<%= data.settings.send_credits_finished_email == 1 ? '' : 'display:none' %>">
				<input id="credits-finished-subject" placeholder="Subject" type="text" value="<%= data.settings.credits_finished_email_template_subject %>" />
				<textarea id="credits-finished-body" placeholder="Body"><%= data.settings.credits_finished_email_template_body %></textarea>
				<span class="hastip shortcode-tooltip" title="You can use this shortcode in the email body. This shortcode will be replaced by the actual user full name in the email">USER_FULL_NAME</span>
			</div>
		</div>
		<div id="tagless-posts-replacement-tag-container">
			Deletion of some tags will result in some posts being tagless. It is required that each post has at least one tag. <br /><br />To counter this you can add one tag from the below available tags to the those posts : 
			<select id="tagless-posts-replacement-tag"></select>
			<button id="tagless-posts-replacement-button">UPDATE</button>
		</div>
	</div>
	<button id="language-settings-button" data-language-code="<%= data.settings.language_code %>">UPDATE</button>
	<div id="language-settings-error"></div>
	<div id="language-settings-container-lightbox"></div>
</script>

<script type="text/template" id="new-language-view">
	<div id="new-language-container">
		<div class="form-name">ADD A NEW LANGUAGE</div>
		<div class="form-element">
			<label for="language-name">Language Name</label>
			<select id="language-name">
				<%
				for(var i=0; i<data.supported_languages.length; i++) {
					if(data.languages.indexOf(data.supported_languages[i]['code']) == -1) 
						print('<option data-direction="' + data.supported_languages[i]['direction'] + '" value="' + data.supported_languages[i]['code'] + '">' + data.supported_languages[i]['name'] + '</option>');
				}
				%>
			</select>
		</div>
		<div class="form-element">
			<label for="language-file">Language File</label>
			<div id="language-file-container">
				<span></span>
				<button id="language-file-change">Upload</button>
				<input type="file" id="language-file" />
			</div>
		</div>
		<button id="new-language-button">ADD</button>
		<div id="new-language-error"></div>
	</div>
</script>

<script type="text/template" id="transactions-menu-view">
	<div class="inner-menu">
		<a id="transactions-menu-all" href="#/transactions/all">All</a>
		<a id="transactions-menu-pending" href="#/transactions/pending">Pending</a>
	</div>
	<div id="inner-menu-contents"></div>
</script>

<script type="text/template" id="all-transactions-view">
	<% 
	if(data.num_pages == 0)
		print('<div class="all-transactions-empty">No Transactions</div>');
	else {
	%>
		<div id="transactions-in-page-container"></div>
		<div id="transactions-pages-link-container">Page <span id="all-transactions-page-current"></span> of <span id="all-transactions-page-total"><%= data.num_pages %></span><span id="all-transactions-prev-page-link">Previous Page</span><span id="all-transactions-next-page-link">Next Page</span></div>
	<%
	}
	%>
</script>

<script type="text/template" id="transactions-in-page-view">
	<% 
	if(data.users.length == 0)
		print('<div class="all-transactions-empty">No Transactions</div>');
	else {
	%>
		<table id="transactions-in-page-table">
			<thead>
				<tr>
					<th class="transactions-in-page-id-td">INT TRANSACTION ID</th>
					<th class="transactions-in-page-user-td">USER</th>
					<th class="transactions-in-page-credits-td">NUM CREDITS</th>
					<th class="transactions-in-page-amount-td">AMOUNT</th>
					<th class="transactions-in-page-transactionid-td">MERCHANT TRANSACTION ID</th>
					<th class="transactions-in-page-status-td">STATUS</th>
				</tr>
			</thead>
			<tbody>
			<%
			_.each(data.transactions, function(transaction) { 
				print('<tr class="' + (transaction['payment_status'] != 'COMPLETED' ? 'transaction-payment-not-completed' : '') + '">');
				print('<td class="transactions-in-page-id-td">' + transaction['transaction_id'] + '</td>');
				print('<td class="transactions-in-page-name-td"><img src="' + data.users[transaction['user_id']]['user_picture_url'] + '" /><span>' + data.users[transaction['user_id']]['user_full_name'] + '</span><span class="transaction-user-id">USER ID : ' + transaction['user_id'] + '</span></td>');
				print('<td class="transactions-in-page-credits-td">' + transaction['num_credits'] + '</td>');
				print('<td class="transactions-in-page-amount-td">' + transaction['transaction_amount'] + ' ' + transaction['transaction_currency'] + '</td>');
				print('<td class="transactions-in-page-transactionid-td">' + transaction['merchant_transaction_id'] + '</td>');
				print('<td class="transactions-in-page-status-td">' + transaction['merchant_transaction_status'] + (transaction['payment_status'] != 'COMPLETED' ? '<span>' + transaction['merchant_transaction_status_message'] + '</span>' : '') + '</td>');
				print('</tr>');
			});
			%>
			</tbody>
		</table>
	<%
	}
	%>
</script>

<script type="text/template" id="pending-transactions-view">
	<% 
	if(data.users.length == 0)
		print('<div class="all-transactions-empty">No Transactions</div>');
	else {
	%>
		<table id="transactions-in-page-table">
			<thead>
				<tr>
					<th class="transactions-in-page-id-td">INT TRANSACTION ID</th>
					<th class="transactions-in-page-user-td">USER</th>
					<th class="transactions-in-page-credits-td">NUM CREDITS</th>
					<th class="transactions-in-page-amount-td">AMOUNT</th>
					<th class="transactions-in-page-transactionid-td">MERCHANT TRANSACTION ID</th>
					<th class="transactions-in-page-status-td">STATUS</th>
					<th class="transactions-in-page-change-status-td">RECHECK STATUS</th>
				</tr>
			</thead>
			<tbody>
			<%
			_.each(data.transactions, function(transaction) { 
				print('<tr>');
				print('<td class="transactions-in-page-id-td">' + transaction['transaction_id'] + '</td>');
				print('<td class="transactions-in-page-name-td"><img src="' + data.users[transaction['user_id']]['user_picture_url'] + '" /><span>' + data.users[transaction['user_id']]['user_full_name'] + '</span><span class="transaction-user-id">USER ID : ' + transaction['user_id'] + '</span></td>');
				print('<td class="transactions-in-page-credits-td">' + transaction['num_credits'] + '</td>');
				print('<td class="transactions-in-page-amount-td">' + transaction['transaction_amount'] + ' ' + transaction['transaction_currency'] + '</td>');
				print('<td class="transactions-in-page-transactionid-td">' + transaction['merchant_transaction_id'] + '</td>');
				print('<td class="transactions-in-page-status-td">' + transaction['merchant_transaction_status'] + '<span>' + transaction['merchant_transaction_status_message'] + '</span></td>');
				print('<td class="transactions-in-page-change-status-td"><span class="transaction-check-status" data-transaction-id="' + transaction['transaction_id'] + '" data-merchant-transaction-id="' + transaction['merchant_transaction_id'] + '">Check</span></td>');
				print('</tr>');
			});
			%>
			</tbody>
		</table>
	<%
	}
	%>
</script>

<script type="text/template" id="crons-status-view">
	<div class="cron-status">
		<div class="cron-status-header">Generate Popular Posts CRON<span class="hastip" title="Generates the popular posts seen in the sidebar">?</span></div>
		<div class="cron-status-time">
			<label>Last Executed</label>
			<span><%= data.popular_posts_cron_time == -1 ? 'Never' : data.popular_posts_cron_time %></span>
		</div>
	</div>
	<div class="cron-status">
		<div class="cron-status-header">Refresh CSRF Token CRON<span class="hastip" title="Refreshes the CSRF token to make AJAX calls secure">?</span></div>
		<div class="cron-status-time">
			<label>Last Executed</label>
			<span><%= data.refresh_csrf_cron_time == -1 ? 'Never' : data.refresh_csrf_cron_time %></span>
		</div>
	</div>
</script>

<?php
require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
?>

<script>
var DEFAULT_LANGUAGE = '<?= DEFAULT_LANGUAGE ?>';
</script>

<script src="js/statistics.js"></script>
<script src="js/posts.js"></script>
<script src="js/users.js"></script>
<script src="js/settings.js"></script>
<script src="js/languages.js"></script>
<script src="js/transactions.js"></script>
<script src="js/crons.js"></script>
<script src="js/routes.js"></script>

</body>
</html>