<?php
session_start();
header('Content-type: application/json');
set_time_limit(0);

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/admin-model.php');
require_once('../classes/paypal-api.php');

$command = $_GET['command'];

try {
	if(!isset($_SESSION['admin']))
		throw new Exception('Unauthorized');
	
	$app = new AdminApplicationObject();

	switch($command) {
		case 'GetActivatedLanguages':
			$languages = $app->GetLanguages(1);
			
			echo json_encode(array('languages' => $languages));
			break;

		case 'GetStatistics':
			$statistics = $app->GetCompleteStatisticsByDate($_GET['search_date']);
			
			echo json_encode(array('statistics' => $statistics));
			break;

		case 'GetPostsPagesCount':
			$num_pages = $app->GetPostsPagesCount($_GET['language_code']);
			
			echo json_encode(array('num_pages' => $num_pages));
			break;

		case 'GetPosts':
			$posts = $app->GetPosts($_GET['page_no'], $_GET['language_code']);
			
			echo json_encode(array('posts' => $posts));
			break;

		case 'GetPost':
			$post = $app->GetPost($_GET['post_id']);
			
			echo json_encode(array('post' => $post));
			break;

		case 'FeaturePost':
			$app->SetPostFeatured($_POST['post_id'], $_POST['language_code']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'UnfeaturePost':
			$app->SetPostUnfeatured($_POST['post_id']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'GetPostStatistics':
			$statistics = $app->GetPostStatistics($_GET['post_id']);
			
			echo json_encode(array('statistics' => $statistics));
			break;

		case 'DeletePost':
			$app->DeletePost($_POST['post_id']);

			$email_template_settings = $app->GetLanguageEmail($_POST['language_code'], 'post_deleted');
			if($email_template_settings['email_send'] == 1) {
				require_once('../classes/user-model.php');
				require_once('../classes/class.phpmailer.php');
				require_once('../classes/class.smtp.php');

				$user_app = new UserApplicationObject();
				$user_details = $user_app->GetUser($_POST['user_id']);
	            $email_template_settings['email_body'] = str_replace(['DELETED_POST_TITLE', 'USER_FULL_NAME'], [$_POST['post_title'], $user_details['user_full_name']], $email_template_settings['email_body']);
            
	            if($user_details['user_email_confirmed'] == 1) {
		            set_time_limit(60);
		            if(USE_SMTP == 1) {
		                $email_sent = SendMail(SMTP_HOST, SMTP_PORT, SMTP_SECURITY, SMTP_USERNAME, SMTP_PASSWORD, FROM_EMAIL, FROM_EMAIL_NAME, $user_details['user_email'], $email_template_settings['email_subject'], '<div style="white-space:pre">' . $email_template_settings['email_body'] . '</div>');
		                if($email_sent != 1)
		                    throw new Exception($email_sent);
		            }
		            else {
		                mail($email, $email_template_settings['email_subject'], $email_template_settings['email_body']);
		            }
		        }
			}
			
			echo json_encode(array('deleted' => 1));
			break;

		case 'GetFeaturedPosts':
			$featured_posts = $app->GetFeaturedPosts(0, $_GET['language_code']);
			
			echo json_encode(array('featured_posts' => $featured_posts));
			break;

		case 'GetUsersPagesCount':
			$num_pages = $app->GetUsersPagesCount();
			
			echo json_encode(array('num_pages' => $num_pages));
			break;

		case 'GetUsers':
			$users = $app->GetUsers($_GET['page_no']);
			
			echo json_encode(array('users' => $users));
			break;

		case 'GetSiteSettings':
			$settings = $app->GetSettings('site');
			
			$themes_folder_path = '../themes';
			$themes_folder_contents = scandir($themes_folder_path);
			$themes = [];
			foreach($themes_folder_contents as $result) {
			    if($result === '.' or $result === '..') 
			    	continue;

			    if(is_dir($themes_folder_path . '/' . $result))
			        $themes[] = $result;
			}
			
			echo json_encode(array('settings' => $settings, 'themes' => $themes));
			break;

		case 'GetSoundSettings':
			$settings = [ 'sound_allowed' => SOUND_ALLOWED, 'correct_sound_present' => file_exists('../music-files/correct.mp3'), 'wrong_sound_present' => file_exists('../music-files/wrong.mp3') ];
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'GetEmailSettings':
			$settings = $app->GetSettings('email');
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'GetPremiumSettings':
			$settings = $app->GetSettings('premium');
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'GetLocaleSettings':
			$settings = $app->GetSettings('locale');
			$timezones_list = DateTimeZone::listIdentifiers();
			$languages_list = $app->GetQuizzioSupportedLanguages();
			
			echo json_encode(array('settings' => $settings, 'timezones' => $timezones_list, 'languages' => $languages_list));
			break;

		case 'GetAdsSettings':
			$settings = $app->GetAdsSettings();
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'GetLoginSettings':
			$settings = $app->GetSettings('login');
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'SetSiteSettings':
			if(array_key_exists('logo', $_FILES)) {
				$logo_file_name = 'logo.' . end((explode(".", $_FILES['logo']['name'])));
				move_uploaded_file($_FILES['logo']['tmp_name'], '../img/' . $logo_file_name);
				$_POST['settings']['logo_file_name'] = $logo_file_name;

				if($logo_file_name != LOGO_NAME)
					unlink('../img/' . LOGO_NAME);
			}

			if(array_key_exists('social', $_FILES)) {
				$social_file_name = 'social.' . end((explode(".", $_FILES['social']['name'])));
				move_uploaded_file($_FILES['social']['tmp_name'], '../img/' . $social_file_name);
				$_POST['settings']['social_file_name'] = $social_file_name;

				if($social_file_name != SHARE_IMAGE_NAME)
					unlink('../img/' . SHARE_IMAGE_NAME);
			}

			$app->SetSettings('site', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetSoundSettings':
			if(array_key_exists('correct_sound_file', $_FILES))
				move_uploaded_file($_FILES['correct_sound_file']['tmp_name'], '../music-files/correct.mp3');

			if(array_key_exists('wrong_sound_file', $_FILES))
				move_uploaded_file($_FILES['wrong_sound_file']['tmp_name'], '../music-files/wrong.mp3');

			$app->SetSettings('sound', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetEmailSettings':
			if($_POST['settings']['smtp_used'] == 1) {
				if($_POST['settings']['server_smtp_host'] != SMTP_HOST || $_POST['settings']['server_smtp_port'] != SMTP_PORT || $_POST['settings']['server_smtp_security'] != SMTP_SECURITY || $_POST['settings']['server_smtp_username'] != SMTP_USERNAME || $_POST['settings']['server_smtp_password'] != SMTP_PASSWORD) {
					require_once('../classes/class.phpmailer.php');
					require_once('../classes/class.smtp.php');

					set_time_limit(60);
					$email_sent = SendMail($_POST['settings']['server_smtp_host'], $_POST['settings']['server_smtp_port'], $_POST['settings']['server_smtp_security'], $_POST['settings']['server_smtp_username'], $_POST['settings']['server_smtp_password'], LOGIN_EMAIL, 'Quizzio Website',  'testquizio@testquizzio.com', 'Quizzio Test Mail', 'This is a test mail to check the entered SMTP settings. Entered STMP settings are correct');

					if($email_sent != 1)
						throw new Exception('A test mail was sent to ' . LOGIN_EMAIL . ' which failed to be delivered. Error reason - ' . $email_sent . '. Please check SMTP settings.');
				}
			}

			$app->SetSettings('email', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetPremiumSettings':
			$app->SetSettings('premium', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetLocaleSettings':
			$app->SetSettings('locale', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetAdsSettings':
			$app->SetAdsSettings($_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'SetLoginSettings':
			$app->SetSettings('login', $_POST['settings']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'GetLanguages':
			$languages = $app->GetLanguages();
			
			echo json_encode(array('languages' => $languages));
			break;

		case 'GetLanguageSettings':
			$settings = $app->GetLanguageSettings($_GET['language_code']);
			
			echo json_encode(array('settings' => $settings));
			break;

		case 'GetLanguagesCode':
			$languages = $app->GetLanguages(0, 1);
			$quizzio_supported_languages = $app->GetQuizzioSupportedLanguages();

			echo json_encode(array('languages' => $languages, 'supported_languages' => $quizzio_supported_languages));
			break;

		case 'AddLanguage':
			$app->AddLanguage($_POST['language_code'], $_POST['language_name'], $_POST['language_direction']);
			move_uploaded_file($_FILES['language_file']['tmp_name'], '../lang/' . $_POST['language_code'] . '.txt');
			
			echo json_encode(array('language_code' => $_POST['language_code']));
			break;

		case 'UpdateLanguage':
			$language_settings = array();
			$language_settings['language_activated'] = $_POST['language_activated'];
			$language_settings['send_email_confirm_email'] = $_POST['send_email_confirm_email'];
		    $language_settings['confirm_email_template_subject'] = $_POST['confirm_email_template_subject'];
		    $language_settings['confirm_email_template_body'] = $_POST['confirm_email_template_body'];
		    $language_settings['send_post_deleted_email'] = $_POST['send_post_deleted_email'];
		    $language_settings['post_deleted_email_template_subject'] = $_POST['post_deleted_email_template_subject'];
		    $language_settings['post_deleted_email_template_body'] = $_POST['post_deleted_email_template_body'];
		    $language_settings['send_credits_finished_email'] = $_POST['send_credits_finished_email'];
		    $language_settings['credits_finished_email_template_subject'] = $_POST['credits_finished_email_template_subject'];
		    $language_settings['credits_finished_email_template_body'] = $_POST['credits_finished_email_template_body'];

		    if($language_settings['language_activated'] == 1) {
		    	$activated_languages = $app->GetLanguages(1, 1);
		    	if(sizeof($activated_languages) >= 4 && !in_array($_POST['language_code'], $activated_languages))
		    		throw new Exception('Error: There can only be 4 languages activated');  		
		    }
			
			$added_tags = $app->UpdateLanguage($language_settings, json_decode($_POST['language_categories_tags'], true), $_POST['language_code']);

			if(array_key_exists('language_file', $_FILES))
				move_uploaded_file($_FILES['language_file']['tmp_name'], '../lang/' . $_POST['language_code'] . '.txt');

			$response = array('updated' => 1, 'added_tags' => $added_tags);

			$tags_to_delete = json_decode($_POST['tags_to_delete'], true);
			if(sizeof($tags_to_delete) > 0) {
				$tags_deleted = $app->DeleteLanguageTags($tags_to_delete);
				$response['deleted'] = $tags_deleted;
				if($tags_deleted == -1)
					$response['tags'] = $app->GetLanguageTags($_POST['language_code']);
			}
			
			echo json_encode($response);
			break;

		case 'AddTagToTaglessPosts':
			$app->DeleteLanguageTags(json_decode($_POST['tags_to_delete'], true), $_POST['replacement_tag']);
			
			echo json_encode(array('updated' => 1));
			break;

		case 'DeleteTag':
			$language_id = $app->AddLanguage($_POST['language_name'], $_POST['language_direction']);
			move_uploaded_file($_FILES['language_file']['tmp_name'], '../lang/' . $language_id . '.txt');
			
			echo json_encode(array('language_id' => $language_id));
			break;

		case 'GetTransactionsPagesCount':
			$num_pages = $app->GetAllTransactionsPagesCount();
			
			echo json_encode(array('num_pages' => $num_pages));
			break;

		case 'GetAllTransactions':
			$data = $app->GetAllTransactions($_GET['page_no']);
			
			echo json_encode($data);
			break;

		case 'GetPendingTransactions':
			$data = $app->GetPendingTransactions();
			
			echo json_encode($data);
			break;

		case 'CheckTransactionStatus':
			$is_sandbox = PAYPAL_SANDBOX_ACTIVATED;
		    if($is_sandbox == 1) {
		        $paypal_api_username = PAYPAL_SANDBOX_API_USERNAME;
		        $paypal_api_password = PAYPAL_SANDBOX_API_PASSWORD;
		        $paypal_api_signature = PAYPAL_SANDBOX_API_SIGNATURE;
		    }
		    else {
		        $paypal_api_username = PAYPAL_API_USERNAME;
		        $paypal_api_password = PAYPAL_API_PASSWORD;
		        $paypal_api_signature = PAYPAL_API_SIGNATURE;
		    }
		    $paypal = new PaypalApi($is_sandbox);
        	$transaction_details = $paypal->GetTransactionDetails($paypal_api_username, $paypal_api_password, $paypal_api_signature, $_GET['merchant_transaction_id']);
        	if(in_array($transaction_details['payment_status'], ['Denied', 'Failed'])) {
        		$app->MarkTransactionFailed($_GET['transaction_id'], $transaction_details);
        		$response = [ 'completed' => -1, 'details' => $transaction_details ];
        	}
        	else if($transaction_details['payment_status'] == 'Completed') {
        		$app->MarkTransactionSuccessful($_GET['transaction_id'], $transaction_details);
        		$response = [ 'completed' => 1];
        	}
        	else
        		$response = [ 'completed' => 0, 'details' => $transaction_details ];
			
			echo json_encode($response);
			break;

		case 'GetCronsStatus':
			$popular_posts_cron_time = file_get_contents("../cron/generate-popular-posts-time.txt");
			$refresh_csrf_cron_time = file_get_contents("../cron/refresh-csrf-token-time.txt");
			
			echo json_encode([ 'popular_posts_cron_time' => $popular_posts_cron_time, 'refresh_csrf_cron_time' => $refresh_csrf_cron_time ]);
			break;
	}
}
catch(Exception $e) {
	header("HTTP/1.1 404 Bad Request");
	echo json_encode(array('error' => 1, 'message' => $e->getMessage()));
	exit();
}

?>