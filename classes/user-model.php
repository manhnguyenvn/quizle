<?php

class UserApplicationObject
{
	private $_dbops;

	public function __construct() {
		$this->_dbops = new DatabaseOperations();
	}

	public function CreatePostUrl($language_code, $post_id, $post_title) {
		$url_parameters = [];
		if($language_code != DEFAULT_LANGUAGE) {
		    if(PRETTY_URLS == 0)
		    	$url_parameters['language_code'] = $language_code;
		    else
		    	$url_parameters[] = $language_code;
		}
		
		/* No accented characters */
		if(in_array($language_code, ['en-US', 'en-GB'])) {
			if(PRETTY_URLS == 0)
			    $post_url = LOCATION_SITE . 'post.php?' . http_build_query(array_merge($url_parameters, [ 'post_id' => $post_id, 'post_title' => rtrim(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($post_title))), '-') ]));
			else
				$post_url = LOCATION_SITE . implode('/', array_merge($url_parameters, [ 'post', $post_id, rtrim(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($post_title))), '-') ] ));
		}
		/* May contain accented characters */
		else if(in_array($language_code, ['af-ZA', 'id-ID', 'bs-BA', 'cs-CZ', 'da-DK', 'de-DE', 'fr-FR', 'hr-HR', 'it-IT', 'lv-LV', 'hu-HU', 'es-ES', 'es-LA', 'pl-PL', 'nl-NL', 'pt-BR', 'ro-RO', 'sv-SE', 'tr-TR', 'bg-BG'])) {
			setlocale(LC_ALL, 'en_US.UTF8');
			if(PRETTY_URLS == 0)
			    $post_url = LOCATION_SITE . 'post.php?' . http_build_query(array_merge($url_parameters, [ 'post_id' => $post_id, 'post_title' => rtrim(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', @iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($post_title)))), '-') ]));
			else
				$post_url = LOCATION_SITE . implode('/', array_merge($url_parameters, [ 'post', $post_id, rtrim(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', @iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($post_title)))), '-') ]));
		}
		/* All special characters */
		else if(in_array($language_code, ['mn-MN', 'sr-RS', 'he-IL', 'ur-PK', 'ar-AR', 'fa-IR', 'hi-IN', 'th-TH', 'ko-KR', 'zh-CN', 'ja-JP'])) {
			if(PRETTY_URLS == 0)
			    $post_url = LOCATION_SITE . 'post.php?' . http_build_query(array_merge($url_parameters, [ 'post_id' => $post_id, 'post_title' => $post_title ]));
			else
				$post_url = LOCATION_SITE . implode('/', array_merge($url_parameters, [ 'post', $post_id, $post_title ]));
		}

		return $post_url;
	}

	public function CreateProfileUrl($language_code, $user_id) {
		$url_parameters = [];

		if($language_code != DEFAULT_LANGUAGE) {
		    if(PRETTY_URLS == 0)
		    	$url_parameters['language_code'] = $language_code;
		    else
		    	$url_parameters[] = $language_code;
		}

		if(PRETTY_URLS == 0) {
	    	$profile_url = LOCATION_SITE . 'profile.php?' . http_build_query(array_merge($url_parameters, [ 'user_id' => $user_id ]));
	    }
	    else {
			$profile_url = LOCATION_SITE . implode('/', array_merge($url_parameters, [ 'profile', $user_id ] ));
		}

		return $profile_url;
	}

	public function CreatePost($user_id, $post_type, $post_properties, $post_data, $publish_post) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "INSERT INTO quizzio_posts VALUES(" .
					"NULL" . "," .
					$handle->real_escape_string($user_id) . ",'" .
					$handle->real_escape_string($post_type) . "', '" .
					$handle->real_escape_string($post_properties['title']) . "', '" .
					$handle->real_escape_string($post_properties['description']) . "', " .
					($post_properties['image_id'] == NULL ? "NULL" : "'" . $handle->real_escape_string($post_properties['image_id']) . "'") . ", " .
					($post_properties['image_attribution'] == NULL ? "NULL" : "'" . $handle->real_escape_string($post_properties['image_attribution']) . "'") . ", " .
					($post_type == 'QUIZ' ? ("'" . ($post_properties['type'] == 1 ? 'CORRECT_OPTION_BASED' : 'WEIGHT_BASED'). "'") : "NULL") . ", '" . 
					$post_properties['language_code'] . "', " .
					$publish_post . ", " . 
					"FALSE" . ", " . 
					"FALSE" . ", " . 
					"FALSE" . ", " . 
					"NULL" . ", '" .
					date('Y-m-d H:i:s') . "', '" .
					date('Y-m-d H:i:s') . "'" .
					")";
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "SELECT last_insert_id()";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$post_id = $row['last_insert_id()'];

		if($post_type == 'QUIZ')
			$query = "INSERT INTO quizzio_quiz_data VALUES(" . $post_id . ", '" . $handle->real_escape_string($post_data) . "'" . ")";
		$this->_dbops->ExecuteUIDQuery($query);

		for($i=0; $i<sizeof($post_properties['tags']); $i++) {
			$query = "INSERT INTO quizzio_post_tags VALUES(" . $post_properties['tags'][$i] . "," . $post_id . ")";
			$this->_dbops->ExecuteUIDQuery($query);
		}

		$this->_dbops->EndTransaction();

		return $post_id;
	}

	public function UpdatePost($post_id, $user_id, $post_properties, $post_data, $post_published) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "SELECT post_type,language_code,post_hidden FROM quizzio_posts WHERE post_id=" . $handle->real_escape_string($post_id);
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;
		$row = $this->_dbops->GetNextResultRow();
		$post_type = $row['post_type'];
		$language_code = $row['language_code'];
		$post_hidden = $row['post_hidden'];

		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "UPDATE quizzio_posts SET post_title='" .
					$handle->real_escape_string($post_properties['title']) . "', post_description='" .
					$handle->real_escape_string($post_properties['description']) . "', post_image_id=" .
					($post_properties['image_id'] == NULL ? "NULL" : "'" . $handle->real_escape_string($post_properties['image_id']) . "'") . ", post_image_attribution=" .
					($post_properties['image_attribution'] == NULL ? "NULL" : "'" . $handle->real_escape_string($post_properties['image_attribution']) . "'") . ", " .
					($post_type == 'QUIZ' ? ("post_quiz_type='" . ($post_properties['type'] == 1 ? 'CORRECT_OPTION_BASED' : 'WEIGHT_BASED') . "', ") : "") . "language_code='" . 
					$post_properties['language_code'] . "', post_published=" .
					$post_published . ", post_updated_ts='" .
					date('Y-m-d H:i:s') . "'" .
					" WHERE post_id=" . $post_id . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$query = "DELETE FROM quizzio_post_tags WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);
		for($i=0; $i<sizeof($post_properties['tags']); $i++) {
			$query = "INSERT INTO quizzio_post_tags VALUES(" . $post_properties['tags'][$i] . "," . $post_id . ")";
			$this->_dbops->ExecuteUIDQuery($query);
		}

		if($post_type == 'QUIZ') {
			$query = "UPDATE quizzio_quiz_data SET quiz_data='" . $handle->real_escape_string($post_data) . "'" . " WHERE post_id=" . $post_id;
			$this->_dbops->ExecuteUIDQuery($query, 0);
		}

		if($post_published == 1 && $post_hidden == 0 && $language_code != $post_properties['language_code']) {
			$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
			$this->_dbops->ExecuteUIDQuery($query, 0);
		}

		$this->_dbops->EndTransaction();
	}

	public function GetPostFull($post_id, $language_code) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "SELECT * FROM quizzio_posts WHERE post_id=" . $handle->real_escape_string($post_id);
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;
		$row = $this->_dbops->GetNextResultRow();
		$post_type = $row['post_type'];
		
		$post_properties = array(
								'post_id' => $post_id,
								'post_type' => $post_type,
								'title' => htmlspecialchars($row['post_title'], ENT_NOQUOTES),
								'description' => $row['post_description'],
								'image_id' => $row['post_image_id'],
								'image_attribution' => htmlspecialchars($row['post_image_attribution']),
								'language_code' => $row['language_code'],
								'tags' => array()
							);
		if($post_type == 'QUIZ')
			$post_properties['type'] = ( $row['post_quiz_type'] == 'CORRECT_OPTION_BASED' ? 1 : 2 );
		
		$premium_properties = array(
								'was_premium' => $row['post_was_premium'],
								'is_premium' => $row['post_is_premium'],
								'domain' => $row['post_premium_domain']
							);

		$is_published = $row['post_published'];
		$is_hidden = $row['post_hidden'];

		$post_user_properties = array('user_id' => $row['user_id'], 'updated_time' => GetTimeElapsed($row['post_updated_ts']));
		$query = "SELECT user_picture_url,registration_source,user_thirdparty_id,user_full_name FROM quizzio_users WHERE user_id=" . $post_user_properties['user_id'];
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$post_user_properties['user_picture_url'] = $row['user_picture_url'];
		$post_user_properties['user_full_name'] = $row['user_full_name'];
		$post_user_properties['registration_source'] = $row['registration_source'];
		$post_user_properties['user_thirdparty_id'] = $row['user_thirdparty_id'];
		
		$post_user_properties['profile_url'] = $this->CreateProfileUrl($language_code, $post_user_properties['user_id']);

		$post_url = $this->CreatePostUrl($post_properties['language_code'], $post_properties['post_id'], $post_properties['title']);
		$post_url_fb = LOCATION_SITE . 'post.php?post_id=' . $post_properties['post_id'] . '&language_code=' . $post_properties['language_code'];

		$query = "SELECT tag_id FROM quizzio_post_tags WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$post_properties['tags'][] = $row['tag_id'];
		}
		
		if($post_type == 'QUIZ')
			$query = "SELECT quiz_data AS post_data FROM quizzio_quiz_data WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;
		
		$row = $this->_dbops->GetNextResultRow();
		$post_data = json_decode($row['post_data'], true);

		if($post_type == 'QUIZ')
			return array('quiz_properties' => $post_properties, 'quiz_data' => $post_data, 'premium_properties' => $premium_properties, 'is_published' => $is_published, 'is_hidden' => $is_hidden, 'quiz_user_properties' => $post_user_properties, 'post_url' => $post_url, 'post_url_fb' => $post_url_fb);
	}

	public function GetUserPosts($user_id) {
		$posts = [];

		$query = "SELECT post_id,post_type,post_title,post_description,post_image_id,post_published,post_hidden,post_is_premium,language_code FROM quizzio_posts WHERE user_id=" . $user_id . " ORDER BY post_id DESC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);
			$row['post_description'] = htmlspecialchars($row['post_description'], ENT_NOQUOTES);
			
			$url_parameters = [];
			if($row['language_code'] != DEFAULT_LANGUAGE) {
			    if(PRETTY_URLS == 0)
			    	$url_parameters['language_code'] = $row['language_code'];
			    else
			    	$url_parameters[] = $row['language_code'];
			}

			if(PRETTY_URLS == 0)
				$row['edit_url'] = LOCATION_SITE . 'user-quiz.php?' . http_build_query(array_merge($url_parameters, [ 'post_id' => $row['post_id'] ]));
			else
				$row['edit_url'] = LOCATION_SITE . implode('/', array_merge($url_parameters, [ 'user-quiz', $row['post_id'] ] ));
			if($row['post_published'] == 1)
				$row['post_url'] = $this->CreatePostUrl($row['language_code'], $row['post_id'], $row['post_title']);

			$posts[] = $row;
		}

		return $posts;
	}

	public function GetUserProfilePosts($user_id, $page_no, $current_language_code) {
		$num_posts_in_one_page = 10;
		$posts = [];

		$query = "SELECT post_id,post_type,post_title,post_description,post_image_id,language_code FROM quizzio_posts WHERE user_id=" . $user_id . "  AND post_published=1 AND post_hidden=0 AND post_is_premium=0 ORDER BY post_id DESC LIMIT " . ($num_posts_in_one_page*($page_no - 1)) . "," . $num_posts_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);
			$row['post_description'] = htmlspecialchars($row['post_description'], ENT_NOQUOTES);
			
			$row['post_url'] = $this->CreatePostUrl($row['language_code'], $row['post_id'], $row['post_title']);
			
			$posts[] = $row;
		}

		return array('posts' => $posts, 'more_pages' => (sizeof($posts) < $num_posts_in_one_page ? 0 : 1));
	}

	public function HidePost($user_id, $post_id) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "UPDATE quizzio_posts SET post_hidden=TRUE WHERE post_id=" . $handle->real_escape_string($post_id) . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$this->_dbops->EndTransaction();
	}

	public function ShowPost($user_id, $post_id) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "UPDATE quizzio_posts SET post_hidden=FALSE WHERE post_id=" . $handle->real_escape_string($post_id) . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function DeletePost($user_id, $post_id) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "SELECT post_type,post_image_id FROM quizzio_posts WHERE post_id=" . $handle->real_escape_string($post_id);
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$post_type = $row['post_type'];
		$post_image_id = $row['post_image_id'];

		$query = "DELETE FROM quizzio_posts WHERE post_id=" . $post_id . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "DELETE FROM quizzio_post_tags WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$query = "DELETE FROM quizzio_post_statistics WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$query = "DELETE FROM quizzio_popular_posts WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		if($post_type == 'QUIZ') {
			$query = "SELECT quiz_data FROM quizzio_quiz_data WHERE post_id=" . $post_id;
			$this->_dbops->ExecuteSQuery($query);
			$quiz_data = json_decode($this->_dbops->GetNextResultRow()['quiz_data'], true);

			$query = "DELETE FROM quizzio_quiz_data WHERE post_id=" . $post_id;
			$this->_dbops->ExecuteUIDQuery($query);

			if($post_image_id != NULL) {
		    	unlink('../img/QUIZ/quiz/' . $post_image_id);
		    	unlink('../img/QUIZ/quiz/m-' . $post_image_id);
		    }

			for($i=0; $i<sizeof($quiz_data['questions']); $i++) {
		    	if($quiz_data['questions'][$i]['image_id'] != NULL)
		    		unlink('../img/QUIZ/question/' . $quiz_data['questions'][$i]['image_id']);

				for($j=0; $j<sizeof($quiz_data['questions'][$i]['options']); $j++) {
		            if($quiz_data['questions'][$i]['options'][$j]['image_id'] != NULL)
		            	unlink('../img/QUIZ/option/' . $quiz_data['questions'][$i]['options'][$j]['image_id']);
		        }
		    }

		    for($i=0; $i<sizeof($quiz_data['results']); $i++) {
		    	if($quiz_data['results'][$i]['image_id'] != NULL)
	            	unlink('../img/QUIZ/result/' . $quiz_data['results'][$i]['image_id']);
		    }

		    if($quiz_data['social_media_image']['image_id'] != NULL)
				unlink('../img/QUIZ/social/' . $quiz_data['social_media_image']['image_id']);
		}

		$this->_dbops->EndTransaction();
	}

	public function WasPostPremium($post_id) {
		if($post_id == NULL)
			return 0;

		$handle = $this->_dbops->GetDatabaseHandle();
		
		$query = "SELECT post_was_premium FROM quizzio_posts WHERE post_id=" . $handle->real_escape_string($post_id);
		$this->_dbops->ExecuteSQuery($query);
		$post_was_premium = $this->_dbops->GetNextResultRow()['post_was_premium'];

		return $post_was_premium;
	}

	public function PostPremiumActivate($user_id, $post_id, $premium_domain, $premium_quiz_credits, $post_was_premium) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();
		
		if($post_was_premium == 0) {
			$query = "UPDATE quizzio_users SET user_premium=1,user_available_credits=user_available_credits-" . $premium_quiz_credits . " WHERE user_id=" . $user_id;
			$this->_dbops->ExecuteUIDQuery($query, 0);
			
			$this->UpdatePostCreditsData($post_id, $premium_quiz_credits);
		}

		$query = "UPDATE quizzio_posts SET post_was_premium=" . 
					"TRUE" . ", post_is_premium=" . 
					"TRUE" . ", post_premium_domain='" . 
					$handle->real_escape_string($premium_domain) . "'" .
					" WHERE post_id=" . $handle->real_escape_string($post_id) . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$this->_dbops->EndTransaction();

		return ($post_was_premium == 0 ? $premium_quiz_credits : 0);
	}

	public function PostPremiumDeactivate($user_id, $post_id) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "UPDATE quizzio_posts SET post_is_premium=" . 
					"FALSE" .
					" WHERE post_id=" . $handle->real_escape_string($post_id) . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$this->_dbops->EndTransaction();
	}

	public function PostPremiumDomainEdit($user_id, $post_id, $premium_domain) {
		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "UPDATE quizzio_posts SET post_premium_domain='" . 
					$handle->real_escape_string($premium_domain) . "'" .
					" WHERE post_id=" . $handle->real_escape_string($post_id) . " AND user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);
	}

	public function UserPostsPremiumDeactivate($user_id) {
		$query = "UPDATE quizzio_posts SET post_is_premium=" . 
					FALSE .
					" WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function CheckUserExists($user_thirdparty_id, $registration_source) {
		$query = "SELECT user_id,user_full_name,user_premium,user_picture_url FROM quizzio_users WHERE user_thirdparty_id=" . $user_thirdparty_id . " AND registration_source='" . $registration_source . "'";
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;

		$row = $this->_dbops->GetNextResultRow();

		return $row;
	}

	public function CreateUser($user_data) {
		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "INSERT INTO quizzio_users VALUES(" .
					"NULL" . ",'" .
					$handle->real_escape_string($user_data['user_full_name']) . "', '" .
					$user_data['registration_source'] . "'," .
					$user_data['user_thirdparty_id'] . ", " .	
					"NULL" . ", '" .
					$handle->real_escape_string($user_data['user_picture_url']) . "', " .
					($user_data['user_gender'] == NULL ? "NULL" : "'" . $user_data['user_gender'] . "'") . ", " . 
					"NULL" . ", " .
					($user_data['user_email'] == NULL ? "NULL" : "'" . $handle->real_escape_string($user_data['user_email']) . "'") . ", " .
					($user_data['user_email'] == NULL ? "FALSE" : "TRUE") . ", " .
					"NULL" . ", '" .
					date('Y-m-d') . "', " .
					"FALSE" . ", " .
					0 .
					")";
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "SELECT last_insert_id()";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$user_id = $row['last_insert_id()'];

		return array('user_id' => $user_id, 'user_premium' => 0, 'user_picture_url' => $user_data['user_picture_url'], 'user_full_name' => $user_data['user_full_name']);
	}

	public function GetUser($user_id) {
		$query = "SELECT user_full_name,user_picture_url,user_email,user_email_confirmed,registration_source,user_thirdparty_id FROM quizzio_users WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;

		$row = $this->_dbops->GetNextResultRow();
		if($row['registration_source'] == 'FB')
			$row['user_picture_url'] = 'http://graph.facebook.com/v2.5/' . $row['user_thirdparty_id'] . '/picture?type=normal';
		
		return $row;
	}

	public function UserEmailChange($user_id, $email) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$user_details = $this->GetUser($user_id);
		if($user_details['user_email'] != NULL && $user_details['user_email_confirmed'] == 1) {
			if($user_details['user_email'] == $email)
				return -1;
		}

		$confirmation_code = rand(111111, 999999);
		$query = "UPDATE quizzio_users SET user_email='" . $handle->real_escape_string($email) . "',user_email_confirmed=0,user_email_confirmation_code='" . $confirmation_code . "' WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);

		return $confirmation_code;
	}

	public function GenerateNewConfirmationCode($user_id) {
		$confirmation_code = rand(111111, 999999);

		$query = "UPDATE quizzio_users SET user_email_confirmation_code='" . $confirmation_code . "' WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query, 0);

		return $confirmation_code;
	}

	public function UserEmailConfirm($user_id, $confirmation_code) {
		$query = "SELECT user_email_confirmation_code FROM quizzio_users WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteSQuery($query);
		$db_confirmation_code = $this->_dbops->GetNextResultRow()['user_email_confirmation_code'];

		if($db_confirmation_code == $confirmation_code) {
			$query = "UPDATE quizzio_users SET user_email_confirmed=1,user_email_confirmation_code=NULL WHERE user_id=" . $user_id;
			$this->_dbops->ExecuteUIDQuery($query);
			return 1;
		}
		else
			return -1;
	}

	public function GetUserCredits($user_id) {
		$query = "SELECT user_available_credits FROM quizzio_users WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;

		$row = $this->_dbops->GetNextResultRow();

		return $row['user_available_credits'];
	}

	public function UserCreditsUpdate($user_id, $change, $num_credits) {
		$query = "UPDATE quizzio_users SET user_premium=1,user_available_credits=user_available_credits" . $change . $num_credits . " WHERE user_id=" . $user_id;
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function SetUserTransaction($user_id, $transaction_details, $num_credits) {
		if($transaction_details['payment_status'] == 'Completed')
			$payment_status = 'COMPLETED';
		else 
			$payment_status = 'PENDING';

		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "INSERT INTO quizzio_users_transactions VALUES(" .
					"NULL" . "," .
					$user_id . ", '" .
					$payment_status . "','" .
					'PAYPAL' . "', '" .	
					$handle->real_escape_string($transaction_details['transaction_id']) . "', '" .
					$handle->real_escape_string($transaction_details['payment_status']) . "', '" .
					$handle->real_escape_string($transaction_details['transaction_status_message']) . "', " .
					$num_credits . ", " .
					$transaction_details['amount'] . ", '" .
					$transaction_details['currency'] . "', '" .
					date('Y-m-d H:i:s') . "'" .
					")";
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function GetUserTransactions($user_id) {
		$transactions = [];

		$query = "SELECT payment_status,merchant_name,merchant_transaction_id,num_credits,transaction_amount,transaction_currency,transaction_ts FROM quizzio_users_transactions WHERE user_id=" . $user_id . " ORDER BY transaction_id DESC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$transactions[] = [ 'completed' => $row['payment_status'], 'transaction_id' => $row['merchant_transaction_id'], 'merchant' => $row['merchant_name'], 'num_credits' => $row['num_credits'], 'amount' => $row['transaction_amount'] . ' ' . $row['transaction_currency'], 'time' => $row['transaction_ts'] ];
		}

		return $transactions;
	}

	public function UpdatePostViewData($post_id) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "INSERT INTO quizzio_post_statistics VALUES(" . $handle->real_escape_string($post_id) . ",'" . date('Y-m-d') . "',1, 0, 0, 0, 0, 0) ON DUPLICATE KEY UPDATE view_count=view_count+1";
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function UpdatePostPlayedData($post_id, $fully_played) {
		$handle = $this->_dbops->GetDatabaseHandle();

		if($fully_played == 0) {
			$query = "INSERT INTO quizzio_post_statistics VALUES(" . $handle->real_escape_string($post_id) . ",'" . date('Y-m-d') . "',0, 1, 0, 0, 0, 0) ON DUPLICATE KEY UPDATE played_count=played_count+1";
			$this->_dbops->ExecuteUIDQuery($query);
		}
		else {
			try {
				$query = "UPDATE quizzio_post_statistics SET fully_played_count=fully_played_count+1 WHERE post_id=" . $handle->real_escape_string($post_id) . " AND post_statistics_date='" . date('Y-m-d') . "'";
				$this->_dbops->ExecuteUIDQuery($query);
			}
			catch(Exception $e) {
				$query = "UPDATE quizzio_post_statistics SET fully_played_count=fully_played_count+1 WHERE post_id=" . $handle->real_escape_string($post_id) . " AND post_statistics_date='" . date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))) . "'";
				$this->_dbops->ExecuteUIDQuery($query);
			}
		}
	}

	public function UpdatePostSharesData($post_id) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "INSERT INTO quizzio_post_statistics VALUES(" . $handle->real_escape_string($post_id) . ",'" . date('Y-m-d') . "',0, 0, 0, 1, 0, 0) ON DUPLICATE KEY UPDATE share_count=share_count+1";
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function UpdatePostCommentsData($post_id) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "INSERT INTO quizzio_post_statistics VALUES(" . $handle->real_escape_string($post_id) . ",'" . date('Y-m-d') . "',0, 0, 0, 0, 1, 0) ON DUPLICATE KEY UPDATE comment_count=comment_count+1";
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function UpdatePostCreditsData($post_id, $credits_consumed, $deduct_user_credits = 0) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "INSERT INTO quizzio_post_statistics VALUES(" . $handle->real_escape_string($post_id) . ",'" . date('Y-m-d') . "',0, 0, 0, 0, 0, " . $credits_consumed . ") ON DUPLICATE KEY UPDATE credits_consumed_count=credits_consumed_count+" . $credits_consumed;
		$this->_dbops->ExecuteUIDQuery($query);

		if($deduct_user_credits == 1) {
			$query = "SELECT user_id FROM quizzio_posts WHERE post_id=" . $handle->real_escape_string($post_id);
			$this->_dbops->ExecuteSQuery($query);
			$row = $this->_dbops->GetNextResultRow();
			$user_id = $row['user_id'];

			$user_available_credits = $this->GetUserCredits($user_id);
			if($user_available_credits > 0)
				$this->UserCreditsUpdate($user_id, '-', 1);
			
			$user_available_credits--;
		}

		$this->_dbops->EndTransaction();

		if($deduct_user_credits == 1)
			return [ 'credits_available' => $user_available_credits, 'user_id' => $user_id ];
	}

	public function GeneratePopularPosts($activated_languages) {
		$posts = [];
		
		for($i=0; $i<sizeof($activated_languages); $i++) {
			/* Lifetime */
			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,SUM(quizzio_post_statistics.view_count) AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 GROUP BY post_id ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'LIFETIME';
				$row['popularity_type'] = 'VIEWS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,SUM(quizzio_post_statistics.played_count) AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 GROUP BY post_id ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'LIFETIME';
				$row['popularity_type'] = 'PLAYS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,SUM(quizzio_post_statistics.comment_count) AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 GROUP BY post_id ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'LIFETIME';
				$row['popularity_type'] = 'COMMENTS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,SUM(quizzio_post_statistics.share_count) AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 GROUP BY post_id ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'LIFETIME';
				$row['popularity_type'] = 'SHARES';
				$posts[] = $row;
			}


			/* Last Day */
			$last_day_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,quizzio_post_statistics.view_count AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 AND quizzio_post_statistics.post_statistics_date='" . $last_day_date . "' ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'ONE_DAY';
				$row['popularity_type'] = 'VIEWS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,quizzio_post_statistics.played_count AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 AND quizzio_post_statistics.post_statistics_date='" . $last_day_date . "' ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'ONE_DAY';
				$row['popularity_type'] = 'PLAYS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,quizzio_post_statistics.comment_count AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 AND quizzio_post_statistics.post_statistics_date='" . $last_day_date . "' ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'ONE_DAY';
				$row['popularity_type'] = 'COMMENTS';
				$posts[] = $row;
			}

			$query = "SELECT quizzio_post_statistics.post_id,quizzio_posts.language_code,quizzio_post_statistics.share_count AS count FROM quizzio_post_statistics INNER JOIN quizzio_posts ON quizzio_post_statistics.post_id=quizzio_posts.post_id AND quizzio_posts.language_code='" . $activated_languages[$i] . "' AND quizzio_posts.post_published=1 AND quizzio_posts.post_hidden=0 AND quizzio_posts.post_is_premium=0 AND quizzio_post_statistics.post_statistics_date='" . $last_day_date . "' ORDER BY count DESC LIMIT 0,5";
			$this->_dbops->ExecuteSQuery($query);
			for($j=0; $j<$this->_dbops->GetNumRows(); $j++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['popularity_age'] = 'ONE_DAY';
				$row['popularity_type'] = 'SHARES';
				$posts[] = $row;
			}
		}

		$this->_dbops->StartTransaction();

		$query = "TRUNCATE quizzio_popular_posts";
		$this->_dbops->ExecuteSQuery($query);

		for($i=0; $i<sizeof($posts); $i++) {
			$query = "INSERT INTO quizzio_popular_posts VALUES('" . $posts[$i]['language_code'] . "','" . $posts[$i]['popularity_type'] . "','" . $posts[$i]['popularity_age'] . "'," . $posts[$i]['post_id'] . "," . $posts[$i]['count'] . ")";
			$this->_dbops->ExecuteUIDQuery($query);
		}

		$this->_dbops->EndTransaction();
	}

	public function GetPopularPosts($popularity_age, $popularity_type, $language_code) {
		$posts = [];
		$post_ids = [];

		$query = "SELECT post_id FROM quizzio_popular_posts WHERE language_code='" . $language_code . "' AND popularity_type='" . $popularity_type . "' AND popularity_age='" . $popularity_age . "'";
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() > 0) {
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$post_ids[] = $row['post_id'];
			}
		
			$query = "SELECT post_id,post_title,post_image_id FROM quizzio_posts WHERE post_id IN (" . implode(',', $post_ids) . ") AND language_code='" . $language_code . "' AND post_published=1 AND post_hidden=0 AND post_is_premium=0";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);

			    $post_url = $this->CreatePostUrl($language_code, $row['post_id'], $row['post_title']);
				
				$posts[] = [ 'title' => $row['post_title'], 'image' => $row['post_image_id'], 'post_url' => $post_url ];
			}
		}

		return $posts;
	}
}

?>