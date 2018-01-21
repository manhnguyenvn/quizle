<?php

class AdminApplicationObject
{
	private $_dbops;

	public function __construct() {
		$this->_dbops = new DatabaseOperations();
	}

	public function CreateTables($admin_email, $admin_password) {
		$this->_dbops->StartTransaction();

		$query = "DROP TABLE IF EXISTS quizzio_posts";
		$this->_dbops->ExecuteSQuery($query);	
		$query = "CREATE TABLE quizzio_posts (
					post_id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
					user_id INT UNSIGNED NOT NULL,
					post_type ENUM('QUIZ') NOT NULL,
					post_title VARCHAR(180) CHARSET utf8 NULL,
					post_description VARCHAR(300) CHARSET utf8 NULL ,
					post_image_id VARCHAR(50) NULL,
					post_image_attribution VARCHAR(120) NULL,
					post_quiz_type ENUM('CORRECT_OPTION_BASED', 'WEIGHT_BASED') NOT NULL,
					language_code CHAR(5) NOT NULL,
					post_published BOOLEAN NOT NULL,
					post_hidden BOOLEAN NOT NULL,
					post_was_premium BOOLEAN NOT NULL,
					post_is_premium BOOLEAN NOT NULL,
					post_premium_domain VARCHAR(50) NULL,
					post_created_ts TIMESTAMP NOT NULL,
					post_updated_ts TIMESTAMP NOT NULL,
					INDEX(language_code,post_published,post_hidden,post_is_premium),
					INDEX(user_id,post_published,post_hidden,post_is_premium)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_quiz_data";
		$this->_dbops->ExecuteSQuery($query);	
		$query = "CREATE TABLE quizzio_quiz_data (
					post_id INT UNSIGNED NOT NULL PRIMARY KEY,
					quiz_data VARCHAR(18000) CHARSET utf8 NULL
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_post_tags";
		$this->_dbops->ExecuteSQuery($query);	
		$query = "CREATE TABLE quizzio_post_tags (
					tag_id INT UNSIGNED NOT NULL,
					post_id INT UNSIGNED NOT NULL,
					INDEX(tag_id),
					INDEX(post_id)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_posts_featured";
		$this->_dbops->ExecuteSQuery($query);	
		$query = "CREATE TABLE quizzio_posts_featured (
					post_id INT UNSIGNED NOT NULL PRIMARY KEY,
					featured_at_ts INT UNSIGNED NOT NULL,
					language_code CHAR(5) NOT NULL,
					INDEX(language_code)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_users";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_users (
					user_id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
					user_full_name VARCHAR(50) NULL,
					registration_source CHAR(5) NOT NULL,
					user_thirdparty_id BIGINT UNSIGNED NULL,
					user_thirdparty_username VARCHAR(50) NULL,
					user_picture_url VARCHAR(300) NULL,
					user_gender CHAR(1) NULL,
					username VARCHAR(30) NULL,
					user_email VARCHAR(150) NULL,
					user_email_confirmed BOOLEAN NOT NULL,
					user_email_confirmation_code CHAR(6) NULL,
					user_joined_date DATE NOT NULL,
					user_premium BOOLEAN NOT NULL,
					user_available_credits BIGINT UNSIGNED NOT NULL
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_users_transactions";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_users_transactions (
					transaction_id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
					user_id INT UNSIGNED NOT NULL,
					payment_status ENUM('COMPLETED', 'PENDING', 'FAILED') NOT NULL,
					merchant_name VARCHAR(30) NOT NULL,
					merchant_transaction_id VARCHAR(100) NOT NULL,
					merchant_transaction_status VARCHAR(20) NOT NULL,
					merchant_transaction_status_message VARCHAR(50) NULL,
					num_credits BIGINT UNSIGNED NOT NULL,
					transaction_amount DECIMAL(10,2) NOT NULL,
					transaction_currency CHAR(3) NOT NULL,
					transaction_ts TIMESTAMP NOT NULL,
					INDEX(payment_status)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_languages";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_languages (
					language_code CHAR(5) NOT NULL PRIMARY KEY,
					language_name VARCHAR(20) CHARACTER SET utf8 NOT NULL,
					language_activated BOOLEAN NOT NULL,
					language_direction CHAR(3) NOT NULL,
					send_email_confirm_email BOOLEAN NOT NULL,
					confirm_email_template_subject VARCHAR(50) CHARACTER SET utf8 NULL,
					confirm_email_template_body VARCHAR(400) CHARACTER SET utf8 NULL,
					send_post_deleted_email BOOLEAN NOT NULL,
					post_deleted_email_template_subject VARCHAR(50) CHARACTER SET utf8 NULL,
					post_deleted_email_template_body VARCHAR(400) CHARACTER SET utf8 NULL,
					send_credits_finished_email BOOLEAN NOT NULL,
					credits_finished_email_template_subject VARCHAR(50) CHARACTER SET utf8 NULL,
					credits_finished_email_template_body VARCHAR(400) CHARACTER SET utf8 NULL
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_language_categories_tags";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_language_categories_tags (
					tag_id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
					tag_name CHAR(30) CHARACTER SET utf8 NULL,
					tag_icon_name CHAR(15) NULL,
					parent_tag_id INT UNSIGNED NULL,
					language_code CHAR(5) NOT NULL,
					position TINYINT UNSIGNED NULL,
					INDEX(language_code)
				)ENGINE=InnoDB AUTO_INCREMENT=1";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_ads";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_ads (
					ad_id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
					ad_code VARCHAR(1000) CHARACTER SET utf8 NULL,
					ad_width SMALLINT UNSIGNED NULL,
					ad_height SMALLINT UNSIGNED NULL
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_popular_posts";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_popular_posts (
					language_code CHAR(5) NOT NULL,
					popularity_type ENUM('VIEWS', 'PLAYS', 'COMMENTS', 'SHARES') NOT NULL,
					popularity_age ENUM('LIFETIME', 'ONE_DAY') NOT NULL,
					post_id INT UNSIGNED NOT NULL,
					popularity_type_count BIGINT UNSIGNED NOT NULL,
					INDEX(language_code, popularity_type, popularity_age)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$query = "DROP TABLE IF EXISTS quizzio_post_statistics";
		$this->_dbops->ExecuteSQuery($query);
		$query = "CREATE TABLE quizzio_post_statistics (
					post_id INT UNSIGNED NOT NULL,
					post_statistics_date DATE NOT NULL,
					view_count INT UNSIGNED NOT NULL,
					played_count INT UNSIGNED NOT NULL,
					fully_played_count INT UNSIGNED NOT NULL,
					share_count INT UNSIGNED NOT NULL,
					comment_count INT UNSIGNED NOT NULL,
					credits_consumed_count INT UNSIGNED NOT NULL,
					PRIMARY KEY(post_id, post_statistics_date),
					INDEX(post_id),
					INDEX(post_statistics_date)
				)ENGINE=InnoDB";
		$this->_dbops->ExecuteSQuery($query);

		$this->AddLanguage('en-US', 'English (US)', 'ltr', 1);
		$language_settings = $this->GetLanguageSettings('en-US', 1);
		$language_categories_tags = $language_settings['categories'];
		reset($language_categories_tags);
		$first_category = key($language_categories_tags);

		$language_categories_tags[$first_category]['name'] = 'CATEGORY';
		$language_categories_tags[$first_category]['icon'] = 'bars';
		$language_categories_tags[$first_category]['tags'] = [ ['name' => 'TAG 1','id' => 'NEW','position' => 1], ['name' => 'TAG 2','id' => 'NEW','position' => 2] ];
		$this->UpdateLanguageCategoriesTags($language_categories_tags, 'en-US');

		$this->SetSettings('login', ['username' => $admin_email, 'password' => $admin_password]);

		$query = "INSERT INTO quizzio_ads VALUES(1, NULL, NULL, NULL), (2, NULL, NULL, NULL), (3, NULL, NULL, NULL), (4, NULL, NULL, NULL)";
		$this->_dbops->ExecuteUIDQuery($query);

		$this->_dbops->EndTransaction();
	}

	public function GetQuizzioSupportedLanguages() {
		$supported_languages = [];
		$supported_languages[] = [ 'name' => 'Afrikaans', 'code' => 'af-ZA', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Indonesian', 'code' => 'id-ID', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Bosnian', 'code' => 'bs-BA', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Czech', 'code' => 'cs-CZ', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Danish', 'code' => 'da-DK', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'German', 'code' => 'de-DE', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'English (UK)', 'code' => 'en-GB', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'English (US)', 'code' => 'en-US', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'French (France)', 'code' => 'fr-FR', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Croatian', 'code' => 'hr-HR', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Italian', 'code' => 'it-IT', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Latvian', 'code' => 'lv-LV', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Hungarian', 'code' => 'hu-HU', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Spanish (Spain)', 'code' => 'es-ES', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Spanish (LA)', 'code' => 'es-LA', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Polish', 'code' => 'pl-PL', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Dutch', 'code' => 'nl-NL', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Portuguese (Brazil)', 'code' => 'pt-BR', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Romanian', 'code' => 'ro-RO', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Swedish', 'code' => 'sv-SE', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Turkish', 'code' => 'tr-TR', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Bulgarian', 'code' => 'bg-BG', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Mongolian', 'code' => 'mn-MN', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Serbian', 'code' => 'sr-RS', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Hebrew', 'code' => 'he-IL', 'direction' => 'rtl' ];
		$supported_languages[] = [ 'name' => 'Urdu', 'code' => 'ur-PK', 'direction' => 'rtl' ];
		$supported_languages[] = [ 'name' => 'Arabic', 'code' => 'ar-AR', 'direction' => 'rtl' ];
		$supported_languages[] = [ 'name' => 'Persian', 'code' => 'fa-IR', 'direction' => 'rtl' ];
		$supported_languages[] = [ 'name' => 'Hindi', 'code' => 'hi-IN', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Thai', 'code' => 'th-TH', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Korean', 'code' => 'ko-KR', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Simplified Chinese (China)', 'code' => 'zh-CN', 'direction' => 'ltr' ];
		$supported_languages[] = [ 'name' => 'Japanese', 'code' => 'ja-JP', 'direction' => 'ltr' ];

		return $supported_languages;
	}

	public function GetSettings($type) {
		switch($type) {
			case 'site':
				$settings = array(	
								'site_name' => stripslashes(SITE_NAME), 
								'facebook_app_id' => FACEBOOK_APP_ID, 
								'facebook_app_secret' => FACEBOOK_APP_SECRET, 
								'facebook_page_url' => stripslashes(FACEBOOK_PAGE_URL), 
								'ga_code' => stripslashes(GOOGLE_ANALYTICS_CODE),
								'max_image_size' => MAX_IMAGE_SIZE_ALLOWED_MB,
								'current_theme' => CURRENT_THEME,
								'font_family' => FONT_FAMILY,
								'logo_name' => LOGO_NAME,
								'logo_cache' => LOGO_CACHE,
								'share_image_name' => SHARE_IMAGE_NAME,
								'share_image_cache' => SHARE_IMAGE_CACHE
								);
				break;

			case 'email':
				$settings = array(	
								'email_from_name' => stripslashes(FROM_EMAIL_NAME), 
								'email_from' => FROM_EMAIL, 
								'smtp_used' => USE_SMTP, 
								'server_smtp_host' => SMTP_HOST, 
								'server_smtp_port' => SMTP_PORT, 
								'server_smtp_security' => SMTP_SECURITY, 
								'server_smtp_username' => stripslashes(SMTP_USERNAME), 
								'server_smtp_password' => stripslashes(SMTP_PASSWORD)
								);
				break;

			case 'premium':
				$settings = array(	
								'activate_premium' => ACTIVATE_PREMIUM, 
								'transaction_currency' => TRANSACTION_CURRENCY, 
								'credits_quantity' => CREDITS_QUANTITY, 
								'credits_value' => CREDITS_VALUE, 
								'premium_quiz_credits' => PREMIUM_QUIZ_CREDITS, 
								'paypal_sandbox_activated' => PAYPAL_SANDBOX_ACTIVATED, 
								'paypal_api_username' => PAYPAL_API_USERNAME, 
								'paypal_api_password' => PAYPAL_API_PASSWORD, 
								'paypal_api_signature' => PAYPAL_API_SIGNATURE,
								'paypal_sandbox_api_username' => PAYPAL_SANDBOX_API_USERNAME, 
								'paypal_sandbox_api_password' => PAYPAL_SANDBOX_API_PASSWORD, 
								'paypal_sandbox_api_signature' => PAYPAL_SANDBOX_API_SIGNATURE
							);
				break;

			case 'locale':
				$settings = array(	
								'timezone' => TIMEZONE, 
								'language' => DEFAULT_LANGUAGE
								);
				break;

			case 'login':
				$settings = array('username' => LOGIN_EMAIL);
		}
		
		return $settings;
	}

	public function GetAdsSettings() {
		$settings = array();

		$query = "SELECT * FROM quizzio_ads";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$settings[] = $this->_dbops->GetNextResultRow();
		}

		return $settings;
	}

	public function Get3AvailableAds() {
		$ads = [];

		$query = "SELECT * FROM quizzio_ads WHERE ad_code IS NOT NULL LIMIT 0,3";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$ads[] = $this->_dbops->GetNextResultRow();
		}

		return $ads;
	}

	public function GetAd($ad_index) {
		$query = "SELECT * FROM quizzio_ads WHERE ad_code IS NOT NULL LIMIT " . ($ad_index - 1). ",1";
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return NULL;
		else 
			return $this->_dbops->GetNextResultRow();
	}

	public function SetAdsSettings($settings) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();

		for($i=0; $i<4; $i++) {
			$query = "UPDATE quizzio_ads SET " . 
						"ad_code=" . ($settings[$i]['ad_code'] == 'NULL' ? "NULL" : "'" . $handle->real_escape_string($settings[$i]['ad_code']) . "'") . "," .
						"ad_width=" . ($settings[$i]['ad_code'] == 'NULL' ? "NULL" : $handle->real_escape_string($settings[$i]['ad_width'])) . "," .
						"ad_height=" . ($settings[$i]['ad_code'] == 'NULL' ? "NULL" : $handle->real_escape_string($settings[$i]['ad_height'])) .
					" WHERE ad_id=" . $settings[$i]['ad_id'];
			$this->_dbops->ExecuteUIDQuery($query, 0);
		}

		$this->_dbops->EndTransaction();
	}

	public function SetSettings($type, $settings) {
		$old_settings = array(	
							'SITE_NAME' => addslashes(SITE_NAME), 
							'FACEBOOK_APP_ID' => FACEBOOK_APP_ID, 
							'FACEBOOK_APP_SECRET' => FACEBOOK_APP_SECRET, 
							'FACEBOOK_PAGE_URL' => addslashes(FACEBOOK_PAGE_URL), 
							'MAX_IMAGE_SIZE_ALLOWED_MB' => MAX_IMAGE_SIZE_ALLOWED_MB,
							'CURRENT_THEME' => CURRENT_THEME, 
							'FONT_FAMILY' => FONT_FAMILY, 
							'FROM_EMAIL_NAME' => addslashes(FROM_EMAIL_NAME), 
							'FROM_EMAIL' => FROM_EMAIL, 
							'USE_SMTP' => USE_SMTP, 
							'SMTP_HOST' => SMTP_HOST, 
							'SMTP_PORT' => SMTP_PORT, 
							'SMTP_SECURITY' => SMTP_SECURITY, 
							'SMTP_USERNAME' => addslashes(SMTP_USERNAME), 
							'SMTP_PASSWORD' => addslashes(SMTP_PASSWORD),
							'ACTIVATE_PREMIUM' => ACTIVATE_PREMIUM, 
							'TRANSACTION_CURRENCY' => TRANSACTION_CURRENCY, 
							'CREDITS_QUANTITY' => CREDITS_QUANTITY, 
							'CREDITS_VALUE' => CREDITS_VALUE, 
							'PREMIUM_QUIZ_CREDITS' => PREMIUM_QUIZ_CREDITS, 
							'PAYPAL_SANDBOX_ACTIVATED' => PAYPAL_SANDBOX_ACTIVATED, 
							'PAYPAL_API_USERNAME' => PAYPAL_API_USERNAME, 
							'PAYPAL_API_PASSWORD' => PAYPAL_API_PASSWORD, 
							'PAYPAL_API_SIGNATURE' => PAYPAL_API_SIGNATURE,
							'PAYPAL_SANDBOX_API_USERNAME' => PAYPAL_SANDBOX_API_USERNAME, 
							'PAYPAL_SANDBOX_API_PASSWORD' => PAYPAL_SANDBOX_API_PASSWORD, 
							'PAYPAL_SANDBOX_API_SIGNATURE' => PAYPAL_SANDBOX_API_SIGNATURE,
							'LOGIN_EMAIL' => LOGIN_EMAIL,
							'LOGIN_PASSWORD_MD5' => LOGIN_PASSWORD_MD5,
							'GOOGLE_ANALYTICS_CODE' => addslashes(GOOGLE_ANALYTICS_CODE),
							'LOGO_NAME' => LOGO_NAME,
							'LOGO_CACHE' => LOGO_CACHE,
							'SHARE_IMAGE_NAME' => SHARE_IMAGE_NAME,
							'SHARE_IMAGE_CACHE' => SHARE_IMAGE_CACHE,
							'SOUND_ALLOWED' => SOUND_ALLOWED,
							'TIMEZONE' => TIMEZONE,
							'DEFAULT_LANGUAGE' => DEFAULT_LANGUAGE
							);

		switch($type) {
			case 'site':
				$new_settings = array(	
								'SITE_NAME' => addslashes($settings['site_name']), 
								'FACEBOOK_APP_ID' => $settings['facebook_app_id'], 
								'FACEBOOK_APP_SECRET' => $settings['facebook_app_secret'],
								'FACEBOOK_PAGE_URL' => addslashes($settings['facebook_page_url']), 
								'GOOGLE_ANALYTICS_CODE' => addslashes($settings['ga_code']),
								'MAX_IMAGE_SIZE_ALLOWED_MB' => $settings['max_image_size'],
								'CURRENT_THEME' => $settings['current_theme'],
								'FONT_FAMILY' => $settings['font_family'],
								);
				if(array_key_exists('logo_file_name', $settings)) {
					$new_settings['LOGO_NAME'] = $settings['logo_file_name'];
					$new_settings['LOGO_CACHE'] = $old_settings['LOGO_CACHE'] + 1;
				}
				if(array_key_exists('social_file_name', $settings)) {
					$new_settings['SHARE_IMAGE_NAME'] = $settings['social_file_name'];
					$new_settings['SHARE_IMAGE_CACHE'] = $old_settings['SHARE_IMAGE_CACHE'] + 1;
				}

				break;

			case 'sound':
				$new_settings = array(	
								'SOUND_ALLOWED' => $settings['sound_allowed'],
								);

				break;

			case 'email':
				$new_settings = array(	
								'FROM_EMAIL_NAME' => addslashes($settings['email_from_name']), 
								'FROM_EMAIL' => $settings['email_from'],
								'USE_SMTP' => $settings['smtp_used'],
								'SMTP_HOST' => $settings['server_smtp_host'],
								'SMTP_PORT' => $settings['server_smtp_port'],
								'SMTP_SECURITY' => $settings['server_smtp_security'],
								'SMTP_USERNAME' => addslashes($settings['server_smtp_username']),
								'SMTP_PASSWORD' => addslashes($settings['server_smtp_password'])
								);
				break;

			case 'premium':
				$new_settings = array(	
								'ACTIVATE_PREMIUM' => $settings['activate_premium'],
								'TRANSACTION_CURRENCY' => $settings['transaction_currency'],
								'CREDITS_QUANTITY' => $settings['credits_quantity'],
								'CREDITS_VALUE' => $settings['credits_value'],
								'PREMIUM_QUIZ_CREDITS' => $settings['premium_quiz_credits'],
								'PAYPAL_SANDBOX_ACTIVATED' => $settings['paypal_sandbox_activated'],
								'PAYPAL_API_USERNAME' => $settings['paypal_api_username'],
								'PAYPAL_API_PASSWORD' => $settings['paypal_api_password'],
								'PAYPAL_API_SIGNATURE' => $settings['paypal_api_signature'],
								'PAYPAL_SANDBOX_API_USERNAME' => $settings['paypal_sandbox_api_username'],
								'PAYPAL_SANDBOX_API_PASSWORD' => $settings['paypal_sandbox_api_password'],
								'PAYPAL_SANDBOX_API_SIGNATURE' => $settings['paypal_sandbox_api_signature']
							);
				break;

			case 'locale':
				$new_settings = array(	
								'TIMEZONE' => addslashes($settings['timezone']), 
								'DEFAULT_LANGUAGE' => $settings['language']
								);
				break;

			case 'login':
				$new_settings = array('LOGIN_EMAIL' => $settings['username']);
				if(array_key_exists('password', $settings))
					$new_settings['LOGIN_PASSWORD_MD5'] = md5($settings['password']);
		}

		$final_settings = array_replace($old_settings, $new_settings);
		
		$fp = fopen('../settings/settings-2.php', 'w+');
		$file_contents = "<?php" . PHP_EOL . PHP_EOL;
		foreach($final_settings as $key => $value) {
			$file_contents .= "define('" . $key . "', '" . $value . "');" . PHP_EOL . PHP_EOL;
		}
		$file_contents .= "?>";
		$result = fwrite($fp, $file_contents);
		fclose($fp);
		if($result == FALSE)
			throw new Exception('Settings file write failed');
	}

	public function GetTagTitle($tag_id) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "SELECT tag_name FROM quizzio_language_categories_tags WHERE tag_id=" . $handle->real_escape_string($tag_id);
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			throw new Exception($this->show_db_error ? $this->dbh->error : 'Error : Tag does not exist', 2);

		$row = $this->_dbops->GetNextResultRow();

		return $row['tag_name'];
	}

	public function GetLanguages($only_activated = 0, $only_ids = 0) {
		$languages = array();

		$query = "SELECT language_code,language_name,language_direction FROM quizzio_languages" . ($only_activated == 1 ? " WHERE language_activated=1" : "");
		$this->_dbops->ExecuteSQuery($query);

		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			if($only_ids == 0)
				$languages[] = $this->_dbops->GetNextResultRow();
			else 
				$languages[] = $this->_dbops->GetNextResultRow()['language_code'];
		}

		return $languages;
	}

	public function GetLanguageSettings($language_code, $short_detail = 0) {
		$settings = array();

		$query = "SELECT " . ($short_detail == 0 ? "*" : "language_name,language_activated,language_direction") . " FROM quizzio_languages WHERE language_code='" . $language_code . "'";
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return -1;
		$row = $this->_dbops->GetNextResultRow();
		$settings = array_merge($settings, $row);

		$settings['categories'] = array();
		$query = "SELECT tag_id,tag_name,tag_icon_name FROM quizzio_language_categories_tags WHERE language_code='" . $language_code . "' AND parent_tag_id IS NULL";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$settings['categories'][$row['tag_id']] = array('name' => $row['tag_name'], 'icon' => $row['tag_icon_name'], 'tags' => array());
		}

		$query = "SELECT tag_id,tag_name,position,parent_tag_id FROM quizzio_language_categories_tags WHERE language_code='" . $language_code . "' AND parent_tag_id IS NOT NULL ORDER BY position ASC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$settings['categories'][$row['parent_tag_id']]['tags'][] = array('name' => $row['tag_name'], 'id' => $row['tag_id'], 'position' => $row['position']);
		}		
		
		return $settings;
	}

	public function GetLanguageEmail($language_code, $email_type) {
		switch($email_type) {
			case 'confirm_email':
				$query = "SELECT send_email_confirm_email AS email_send,confirm_email_template_subject AS email_subject,confirm_email_template_body AS email_body FROM quizzio_languages WHERE language_code='" . $language_code . "'";
				break;

			case 'post_deleted':
				$query = "SELECT send_post_deleted_email AS email_send,post_deleted_email_template_subject AS email_subject,post_deleted_email_template_body AS email_body FROM quizzio_languages WHERE language_code='" . $language_code . "'";
				break;

			case 'credits_finished':
				$query = "SELECT send_credits_finished_email AS email_send,credits_finished_email_template_subject AS email_subject,credits_finished_email_template_body AS email_body FROM quizzio_languages WHERE language_code='" . $language_code . "'";
		}

		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		
		return $row;
	}

	public function GetLanguageTags($language_code, $only_ids = 0) {
		$language_categories_tags = $this->GetLanguageSettings($language_code, 1);

		$language_tags = array();
		foreach ($language_categories_tags['categories'] as $category_id => $category_data) {
			for($i=0; $i<sizeof($category_data['tags']); $i++) {
				if($only_ids == 0)
					$language_tags[] = array('id' => $category_data['tags'][$i]['id'], 'name' => $category_data['tags'][$i]['name']);
				else
					$language_tags[] = $category_data['tags'][$i]['id'];
 			}
		}
		
		return $language_tags;
	}

	public function AddLanguage($language_code, $language_name, $language_direction, $language_activated = 0) {
		$this->_dbops->StartTransaction();

		$handle = $this->_dbops->GetDatabaseHandle();
		$query = "INSERT INTO quizzio_languages VALUES('" .
					$language_code . "', '" .
					$handle->real_escape_string($language_name) . "'," .
					$language_activated . ",'" .
					$handle->real_escape_string($language_direction) . "'," .
					1 . ",'" .
					$handle->real_escape_string("Your Email Confirmation Code") . "','" .
					$handle->real_escape_string("This is your email confirmation code : CONFIRMATION_CODE") . "'," .
					0 . "," .
					"NULL" . "," .
					"NULL" . "," .
					0 . "," .
					"NULL" . "," .
					"NULL" .
					")";
		$this->_dbops->ExecuteUIDQuery($query);

		for($i=0; $i<3; $i++) {
			$query = "INSERT INTO quizzio_language_categories_tags VALUES (" .
						"NULL" . "," .
						"NULL" . "," .
						"NULL" . "," .
						"NULL" . ",'" .
						$language_code . "'," .
						"NULL" .
						")";
			$this->_dbops->ExecuteUIDQuery($query);
		}

		$this->_dbops->EndTransaction();
	}

	public function UpdateLanguage($language_settings, $language_categories_tags, $language_code) {
		$this->_dbops->StartTransaction();

		$this->UpdateLanguageSettings($language_settings, $language_code);
		$added_tags = $this->UpdateLanguageCategoriesTags($language_categories_tags, $language_code);

		$this->_dbops->EndTransaction();

		return $added_tags;
	}

	public function UpdateLanguageSettings($language_settings, $language_code) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$fields = array();
		foreach($language_settings as $key => $value) {
			if(in_array($key, array('send_email_confirm_email', 'send_post_deleted_email', 'send_credits_finished_email')))
				$fields[] = $key . "=" . $value;
			else 
				$fields[] = $key . "='" . $handle->real_escape_string($value) . "'";
		}

		$query = "UPDATE quizzio_languages SET " . implode(',', $fields) . " WHERE language_code='" . $language_code . "'";
		$this->_dbops->ExecuteUIDQuery($query, 0);
	}

	public function UpdateLanguageCategoriesTags($language_categories_tags, $language_code) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$added_tags = [];
		foreach($language_categories_tags as $category_id => $category_data) {
			$query = "UPDATE quizzio_language_categories_tags SET tag_name='" . $handle->real_escape_string($category_data['name']) . "',tag_icon_name='" . $handle->real_escape_string($category_data['icon']) . "' WHERE tag_id=" . $category_id;
			$this->_dbops->ExecuteUIDQuery($query, 0);

			for($i=0; $i<sizeof($category_data['tags']); $i++) {
				if($category_data['tags'][$i]['id'] == 'NEW') {
					$query = "INSERT INTO quizzio_language_categories_tags VALUES(" . 
							"NULL" . ", '" . 
							$handle->real_escape_string($category_data['tags'][$i]['name']) . "'," . 
							"NULL" . "," . 
							$category_id . ",'" .
							$language_code . "'," .
							$category_data['tags'][$i]['position'] .
							")";
					$this->_dbops->ExecuteUIDQuery($query);

					$query = "SELECT last_insert_id()";
					$this->_dbops->ExecuteSQuery($query);
					$row = $this->_dbops->GetNextResultRow();
					$tag_id = $row['last_insert_id()'];
					$added_tags[] = [ 'category_id' => $category_id, 'position' => $category_data['tags'][$i]['position'], 'tag_id' => $tag_id ];
				} 
				else {
					$query = "UPDATE quizzio_language_categories_tags SET tag_name='" . $handle->real_escape_string($category_data['tags'][$i]['name']) . "',position=" . $category_data['tags'][$i]['position'] . " WHERE tag_id=" . $category_data['tags'][$i]['id'];
					$this->_dbops->ExecuteUIDQuery($query, 0);	
				}			
			}
		}

		return $added_tags;
	}

	public function DeleteLanguageTags($tags_to_delete, $replacement_tag_to_insert = NULL) {
		$this->_dbops->StartTransaction();

		$posts_having_tags_to_delete = [];

		$query = "SELECT post_id FROM quizzio_post_tags WHERE tag_id IN (" . implode(',', $tags_to_delete) . ")";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$posts_having_tags_to_delete[] = $this->_dbops->GetNextResultRow()['post_id'];
		}

		if(sizeof($posts_having_tags_to_delete) > 0) {
			$section_of_above_posts_not_having_tags_to_delete = [];
			$query = "SELECT post_id FROM quizzio_post_tags where post_id IN (" . implode(',', $posts_having_tags_to_delete) . ") AND tag_id NOT IN (" . implode(',', $tags_to_delete) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$section_of_above_posts_not_having_tags_to_delete[] = $this->_dbops->GetNextResultRow()['post_id'];
			}

			$posts_with_no_tags = array_diff($posts_having_tags_to_delete, $section_of_above_posts_not_having_tags_to_delete);

			if(sizeof($posts_with_no_tags) > 0) {
				if($replacement_tag_to_insert == NULL)
					return -1;
				else {
					for($i=0; $i<sizeof($posts_with_no_tags); $i++) {
						$query = "INSERT INTO quizzio_post_tags VALUES(" . $replacement_tag_to_insert . "," . $posts_with_no_tags[$i] . ")";
						$this->_dbops->ExecuteUIDQuery($query);
					}
				}
			}
		}
		
		$query = "DELETE FROM quizzio_language_categories_tags WHERE tag_id IN (" . implode(',', $tags_to_delete) . ")";
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "DELETE FROM quizzio_post_tags WHERE tag_id IN (" . implode(',', $tags_to_delete) . ")";
		$this->_dbops->ExecuteUIDQuery($query, 0);

		$this->_dbops->EndTransaction();

		return 1;
	}

	public function GetPostsPagesCount($language_code) {
		$num_posts_in_one_page = 20;

		$query = "SELECT count(*) FROM quizzio_posts" . ($language_code != 'ALL' ? " WHERE language_code='" . $language_code . "'" : "");
		$this->_dbops->ExecuteSQuery($query);
		$num_posts = $this->_dbops->GetNextResultRow()['count(*)'];
		$num_pages = ($num_posts == 0 ? 0 : ceil($num_posts/$num_posts_in_one_page));

		return $num_pages;
	}

	public function GetPosts($page_no, $language_code) {
		$posts = [];
		$num_posts_in_one_page = 20;
	
		$query = "SELECT post_id,post_type,post_title,post_image_id,post_published,post_hidden FROM quizzio_posts" . ($language_code != 'ALL' ? " WHERE language_code='" . $language_code . "'" : "") . " ORDER BY post_id DESC LIMIT " . ($num_posts_in_one_page*($page_no - 1)) . "," . $num_posts_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$posts[] = $this->_dbops->GetNextResultRow();
		}

		return $posts;
	}

	public function GetPost($post_id) {
		$query = "SELECT * FROM quizzio_posts WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			throw new Exception('Error : Post Does Not Exist');
		$post = $this->_dbops->GetNextResultRow();
		$post['post_created_date'] = date('jS M Y', strtotime($post['post_created_ts']));
		unset($post['post_created_ts']);

		$query = "SELECT user_id,user_full_name,user_available_credits,user_picture_url FROM quizzio_users WHERE user_id=" . $post['user_id'];
		$this->_dbops->ExecuteSQuery($query);
		$post['user'] = $this->_dbops->GetNextResultRow();
		unset($post['user_id']);

		$query = "SELECT count(*) FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNextResultRow()['count(*)'] == 0)
			$post['featured'] = 0;
		else 
			$post['featured'] = 1;

		return $post;
	}

	public function SetPostFeatured($post_id, $language_code) {
		$query = "SELECT count(*) FROM quizzio_posts_featured";
		$this->_dbops->ExecuteSQuery($query);
		$num_featured_posts = $this->_dbops->GetNextResultRow()['count(*)'];

		if($num_featured_posts >= 5) {
			$query = "DELETE FROM quizzio_posts_featured ORDER BY featured_at_ts ASC LIMIT 1";
			$this->_dbops->ExecuteUIDQuery($query);
		}

		$query = "INSERT INTO quizzio_posts_featured VALUES(" . $post_id . "," . time() . ", '" . $language_code . "')";
		$this->_dbops->ExecuteUIDQuery($query); 
	}

	public function SetPostUnfeatured($post_id) {
		$query = "DELETE FROM quizzio_posts_featured WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteUIDQuery($query);
	}

	public function GetPostStatistics($post_id) {
		$statistics = [];

		$query = "SELECT SUM(view_count) AS total_view_count, SUM(played_count) AS total_played_count, SUM(fully_played_count) AS total_fully_played_count, SUM(share_count) AS total_share_count, SUM(comment_count) AS total_comment_count, SUM(credits_consumed_count) AS total_credits_consumed_count FROM quizzio_post_statistics WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$statistics['view_count'] = ($row['total_view_count'] == NULL ? 'NA' : $row['total_view_count']);
		$statistics['credits_consumed_count'] = ($row['total_credits_consumed_count'] == NULL ? 'NA' : $row['total_credits_consumed_count']);
		$statistics['played_count'] = ($row['total_played_count'] == NULL ? 'NA' : $row['total_played_count']);
		$statistics['fully_played_count'] = ($row['total_fully_played_count'] == NULL ? 'NA' : $row['total_fully_played_count']);
		$statistics['share_count'] = ($row['total_share_count'] == NULL ? 'NA' : $row['total_share_count']);
		$statistics['comment_count'] = ($row['total_comment_count'] == NULL ? 'NA' : $row['total_comment_count']);

		return $statistics;
	}

	public function GetCompleteStatisticsByDate($date) {
		if($date == 'today')
			$date = date('Y-m-d');

		$statistics = [];

		$query = "SELECT SUM(view_count) AS total_view_count, SUM(played_count) AS total_played_count, SUM(fully_played_count) AS total_fully_played_count, SUM(share_count) AS total_share_count, SUM(comment_count) AS total_comment_count, SUM(credits_consumed_count) AS total_credits_consumed_count FROM quizzio_post_statistics WHERE post_statistics_date='" . $date . "'";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$statistics['view_count'] = ($row['total_view_count'] == NULL ? 'NA' : $row['total_view_count']);
		$statistics['credits_consumed_count'] = ($row['total_credits_consumed_count'] == NULL ? 'NA' : $row['total_credits_consumed_count']);
		$statistics['played_count'] = ($row['total_played_count'] == NULL ? 'NA' : $row['total_played_count']);
		$statistics['fully_played_count'] = ($row['total_fully_played_count'] == NULL ? 'NA' : $row['total_fully_played_count']);
		$statistics['share_count'] = ($row['total_share_count'] == NULL ? 'NA' : $row['total_share_count']);
		$statistics['comment_count'] = ($row['total_comment_count'] == NULL ? 'NA' : $row['total_comment_count']);

		$query = "SELECT count(*) FROM quizzio_posts WHERE post_created_ts BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59'";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$statistics['posts_count'] = $row['count(*)'];

		$query = "SELECT count(*) FROM quizzio_users WHERE user_joined_date='" . $date . "'";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$statistics['users_count'] = $row['count(*)'];

		$query = "SELECT SUM(num_credits) AS total_num_credits FROM quizzio_users_transactions WHERE transaction_ts BETWEEN '" . $date . " 00:00:00' AND '" . $date . " 23:59:59'";
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$statistics['credits_purchased_count'] = ($row['total_num_credits'] == NULL ? 'NA' : $row['total_num_credits']);

		return $statistics;
	}

	public function DeletePost($post_id) {
		$this->_dbops->StartTransaction();

		$query = "SELECT post_type,post_image_id FROM quizzio_posts WHERE post_id=" . $post_id;
		$this->_dbops->ExecuteSQuery($query);
		$row = $this->_dbops->GetNextResultRow();
		$post_type = $row['post_type'];
		$post_image_id = $row['post_image_id'];

		$query = "DELETE FROM quizzio_posts WHERE post_id=" . $post_id;
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

	public function GetUsersPagesCount() {
		$num_users_in_one_page = 20;

		$query = "SELECT count(*) FROM quizzio_users";
		$this->_dbops->ExecuteSQuery($query);
		$num_users = $this->_dbops->GetNextResultRow()['count(*)'];
		$num_pages = ($num_users == 0 ? 0 : ceil($num_users/$num_users_in_one_page));

		return $num_pages;
	}

	public function GetUsers($page_no) {
		$users = [];
		$num_users_in_one_page = 20;
	
		$query = "SELECT user_id,user_full_name,user_picture_url,user_email,user_email_confirmed,registration_source,user_premium,user_available_credits FROM quizzio_users ORDER BY user_id DESC LIMIT " . ($num_users_in_one_page*($page_no - 1)) . "," . $num_users_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$users[] = $this->_dbops->GetNextResultRow();
		}

		return $users;
	}

	public function GetAllTransactionsPagesCount() {
		$num_transactions_in_one_page = 20;

		$query = "SELECT count(*) FROM quizzio_users_transactions";
		$this->_dbops->ExecuteSQuery($query);
		$num_users = $this->_dbops->GetNextResultRow()['count(*)'];
		$num_pages = ($num_users == 0 ? 0 : ceil($num_users/$num_transactions_in_one_page));

		return $num_pages;
	}

	public function GetAllTransactions($page_no) {
		$transactions = [];
		$user_ids = [];
		$users = [];
		$num_transactions_in_one_page = 20;
	
		$query = "SELECT transaction_id,user_id,payment_status,merchant_name,merchant_transaction_id,merchant_transaction_status,merchant_transaction_status_message,num_credits,transaction_amount,transaction_currency FROM quizzio_users_transactions ORDER BY transaction_id DESC LIMIT " . ($num_transactions_in_one_page*($page_no - 1)) . "," . $num_transactions_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$transactions[] = $row;
			$user_ids[] = $row['user_id'];
		}

		if(sizeof($transactions) > 0) {
			$query = "SELECT user_id,user_full_name,user_picture_url FROM quizzio_users WHERE user_id IN (" . implode(',', $user_ids) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$users[$row['user_id']] = [ 'user_full_name' => $row['user_full_name'], 'user_picture_url' => $row['user_picture_url'] ];
			}
		}

		return ['transactions' => $transactions, 'users' => $users];
	}

	public function GetPendingTransactions() {
		$transactions = [];
		$user_ids = [];
		$users = [];
	
		$query = "SELECT transaction_id,user_id,merchant_name,merchant_transaction_id,merchant_transaction_status,merchant_transaction_status_message,num_credits,transaction_amount,transaction_currency FROM quizzio_users_transactions WHERE payment_status='PENDING' ORDER BY transaction_id DESC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$transactions[] = $row;
			$user_ids[] = $row['user_id'];
		}

		if(sizeof($transactions) > 0) {
			$query = "SELECT user_id,user_full_name,user_picture_url FROM quizzio_users WHERE user_id IN (" . implode(',', $user_ids) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$users[$row['user_id']] = [ 'user_full_name' => $row['user_full_name'], 'user_picture_url' => $row['user_picture_url'] ];
			}
		}

		return ['transactions' => $transactions, 'users' => $users];
	}

	public function MarkTransactionSuccessful($transaction_id, $transaction_details) {
		$handle = $this->_dbops->GetDatabaseHandle();
		
		$this->_dbops->StartTransaction();

		$query = "UPDATE quizzio_users_transactions SET payment_status='COMPLETED',merchant_transaction_status='" . $handle->real_escape_string($transaction_details['payment_status']) . "',merchant_transaction_status_message='" . $handle->real_escape_string($transaction_details['transaction_status_message']) . "' WHERE transaction_id=" . $transaction_id;
		$this->_dbops->ExecuteUIDQuery($query);

		$query = "SELECT user_id,num_credits FROM quizzio_users_transactions WHERE transaction_id=" . $transaction_id;
		$this->_dbops->ExecuteSQuery($query);
		$transaction_credits_detail = $this->_dbops->GetNextResultRow();

		$query = "UPDATE quizzio_users SET user_premium=1,user_available_credits=user_available_credits+" . $transaction_credits_detail['num_credits'] . " WHERE user_id=" . $transaction_credits_detail['user_id'];
		$this->_dbops->ExecuteUIDQuery($query);

		$this->_dbops->EndTransaction();
	}

	public function MarkTransactionFailed($transaction_id, $transaction_details) {
		$handle = $this->_dbops->GetDatabaseHandle();

		$query = "UPDATE quizzio_users_transactions SET payment_status='FAILED',merchant_transaction_status='" . $handle->real_escape_string($transaction_details['payment_status']) . "',merchant_transaction_status_message='" . $handle->real_escape_string($transaction_details['transaction_status_message']) . "' WHERE transaction_id=" . $transaction_id;
		$this->_dbops->ExecuteUIDQuery($query);
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

	public function GetFeaturedPosts($frontend_request = 0, $language_code) {
		$featured_posts = [];
		$featured_posts_frontend = [];
	
		$query = "SELECT post_id FROM quizzio_posts_featured WHERE language_code='" . $language_code . "' ORDER BY featured_at_ts DESC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$featured_posts[] = $this->_dbops->GetNextResultRow()['post_id'];
		}

		if(sizeof($featured_posts) > 0) {
			$query = "SELECT post_id,post_type,post_title,post_image_id FROM quizzio_posts WHERE post_id IN (" . implode(',', $featured_posts) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);

				if($frontend_request == 0)
					$featured_posts[$i] = $row;
				else {
					$post_url = $this->CreatePostUrl($language_code, $row['post_id'], $row['post_title']);
					$featured_posts_frontend['p-' . $row['post_id']] = [ 'title' => $row['post_title'], 'image' => $row['post_image_id'], 'post_url' => $post_url ];
				}
			}
		}

		if($frontend_request == 0)
			return $featured_posts;
		else
			return $featured_posts_frontend;
	}

	public function GetPublishedPosts($page_no, $language_code) {
		$posts = [];
		$num_posts_in_one_page = 8;
		$users = [];
		$users_full = [];
	
		$query = "SELECT post_id,post_title,post_description,post_image_id,user_id,post_updated_ts FROM quizzio_posts WHERE language_code='" . $language_code . "' AND post_published=1 AND post_hidden=0 AND post_is_premium=0 ORDER BY post_id DESC LIMIT " . ($num_posts_in_one_page*($page_no - 1)) . "," . $num_posts_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);
			$row['post_description'] = htmlspecialchars($row['post_description'], ENT_NOQUOTES);
			
			$updated_time = GetTimeElapsed($row['post_updated_ts']);
		    $post_url = $this->CreatePostUrl($language_code, $row['post_id'], $row['post_title']);
		    $profile_url = $this->CreateProfileUrl($language_code, $row['user_id']);

			$posts['p-' . $row['post_id']] = [ 'title' => $row['post_title'], 'description' => $row['post_description'], 'image' => $row['post_image_id'], 'time' => $updated_time, 'user_id' => $row['user_id'], 'post_url' => $post_url, 'profile_url' => $profile_url ];
			$users[] = $row['user_id'];
		}

		if($this->_dbops->GetNumRows() > 0) {
			$query = "SELECT user_id,user_full_name FROM quizzio_users WHERE user_id IN (" . implode(',', $users) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$users_full[$row['user_id']] = $row['user_full_name'];
			}
		}

		return array('posts' => $posts, 'users' => $users_full, 'more_pages' => (sizeof($posts) < $num_posts_in_one_page ? 0 : 1));
	}

	public function GetPublishedPostsByTag($tag_id, $is_cat, $page_no, $language_code) {
		$posts = [];
		$post_ids = [];
		$tag_ids = [];
		$num_posts_in_one_page = 8;
		$users = [];
		$users_full = [];

		if($is_cat == 1) {
			$query = "SELECT tag_id FROM quizzio_language_categories_tags WHERE parent_tag_id=" . $tag_id . " AND language_code='" . $language_code . "'";
			$this->_dbops->ExecuteSQuery($query);
			if($this->_dbops->GetNumRows() == 0)
				return array('posts' => [], 'users' => [], 'more_pages' => 0);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$tag_ids[] = $row['tag_id'];
			}
		}
		else {
			$tag_ids[] = $tag_id;
		}

		$query = "SELECT post_id FROM quizzio_post_tags WHERE tag_id IN (" . implode(',', $tag_ids) . ") LIMIT " . ($num_posts_in_one_page*($page_no - 1)) . "," . $num_posts_in_one_page;
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() == 0)
			return array('posts' => [], 'users' => [], 'more_pages' => 0);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$post_ids[] = $row['post_id'];
		}
	
		$query = "SELECT post_id,post_title,post_description,post_image_id,user_id,post_updated_ts FROM quizzio_posts WHERE post_id IN (" . implode(',', $post_ids) . ") AND language_code='" . $language_code . "' AND post_published=1 AND post_hidden=0 AND post_is_premium=0 ORDER BY post_id DESC";
		$this->_dbops->ExecuteSQuery($query);
		for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
			$row = $this->_dbops->GetNextResultRow();
			$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);
			$row['post_description'] = htmlspecialchars($row['post_description'], ENT_NOQUOTES);
			
			$updated_time = GetTimeElapsed($row['post_updated_ts']);
			$post_url = $this->CreatePostUrl($language_code, $row['post_id'], $row['post_title']);
		    $profile_url = $this->CreateProfileUrl($language_code, $row['user_id']);
			
			$posts['p-' . $row['post_id']] = [ 'title' => $row['post_title'], 'description' => $row['post_description'], 'image' => $row['post_image_id'], 'time' => $updated_time, 'user_id' => $row['user_id'], 'post_url' => $post_url, 'profile_url' => $profile_url ];
			$users[] = $row['user_id'];
		}

		if($this->_dbops->GetNumRows() > 0) {
			$query = "SELECT user_id,user_full_name FROM quizzio_users WHERE user_id IN (" . implode(',', $users) . ")";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$users_full[$row['user_id']] = $row['user_full_name'];
			}
		}

		return array('posts' => $posts, 'users' => $users_full, 'more_pages' => (sizeof($post_ids) < $num_posts_in_one_page ? 0 : 1));
	}

	public function GetSimilarPostsByTag($post_id, $tag_ids, $language_code) {
		$posts = [];
		$post_ids = [];

		$query = "SELECT post_id FROM quizzio_post_tags WHERE tag_id IN (" . implode(',', $tag_ids) . ") AND post_id!=" . $post_id . " LIMIT 0,4";
		$this->_dbops->ExecuteSQuery($query);
		if($this->_dbops->GetNumRows() > 0) {
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$post_ids[] = $row['post_id'];
			}
		
			$query = "SELECT post_id,post_title,post_description,post_image_id FROM quizzio_posts WHERE post_id IN (" . implode(',', $post_ids) . ") AND language_code='" . $language_code . "' AND post_published=1 AND post_hidden=0 AND post_is_premium=0";
			$this->_dbops->ExecuteSQuery($query);
			for($i=0; $i<$this->_dbops->GetNumRows(); $i++) {
				$row = $this->_dbops->GetNextResultRow();
				$row['post_title'] = htmlspecialchars($row['post_title'], ENT_NOQUOTES);
				$row['post_description'] = htmlspecialchars($row['post_description'], ENT_NOQUOTES);

			    $post_url = $this->CreatePostUrl($language_code, $row['post_id'], $row['post_title']);
				
				$posts[] = [ 'title' => $row['post_title'], 'description' => $row['post_description'], 'image' => $row['post_image_id'], 'post_url' => $post_url ];
			}
		}

		return $posts;
	}
}

function SendMail($smtp_host, $smtp_port, $smtp_security, $smtp_username, $smtp_password, $from_email, $from_name, $email_to, $subject, $body) {
	$mail = new PHPMailer;
	$mail->isSMTP(); 
	$mail->Host = $smtp_host;
	$mail->Port = $smtp_port;
	$mail->SMTPSecure = $smtp_security;
	$mail->SMTPAuth = true;
	$mail->Username = $smtp_username;
	$mail->Password = $smtp_password;
	$mail->addAddress($email_to);
	$mail->isHTML(true);
	$mail->setFrom($from_email, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body;

	if(!$mail->Send())
		return $mail->ErrorInfo;
	else 
		return 1;
}

function GetTimeElapsed($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array( 'y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'min', 's' => 'sec' );
    foreach ($string as $k => &$v) {
        if ($diff->$k)
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        else
            unset($string[$k]);
    }
    $string = array_slice($string, 0, 1);
    $updated_time =  $string ? implode(', ', $string) : '1 min';

    return $updated_time;
}

?>