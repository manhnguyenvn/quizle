<?php
session_start();sleep(5);
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/user-model.php');
require_once('../classes/admin-model.php');
date_default_timezone_set(TIMEZONE);

$command = $_GET['command'];

try {
	if(!isset($_SESSION['user']))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    if(!in_array($_POST['post_type'], ['QUIZ']))
        throw new Exception('BAD_REQUEST', 1);

    $user_app = new UserApplicationObject();
    $admin_app = new AdminApplicationObject();

    if(isset($_POST['images_to_delete'])) {
        for($i=0; $i<sizeof($_POST['images_to_delete']); $i++) {
            if($_SESSION['user']['user_id'] == array_reverse(explode('.', $_POST['images_to_delete'][$i]))[1])
                unlink('../' . str_replace(LOCATION_SITE, '', $_POST['images_to_delete'][$i]));
        }
    }

	switch($command) {
		case 'SaveDraft':
			if(isset($_SESSION['orphan_images']))
                unset($_SESSION['orphan_images']);

            if(!isset($_POST['post_properties']['tags']))
				$_POST['post_properties']['tags'] = array();

			if($_POST['post_properties']['post_id'] == NULL) {
				$post_id = $user_app->CreatePost($_SESSION['user']['user_id'], $_POST['post_type'], $_POST['post_properties'], $_POST['post_data'], 0);
				echo json_encode(array('post_id' => $post_id));
			}
			else {
				$user_app->UpdatePost($_POST['post_properties']['post_id'], $_SESSION['user']['user_id'], $_POST['post_properties'], $_POST['post_data'], 0);
				echo json_encode(array('updated' => 1));
			}

			break; 

		case 'PublishPost':
			if(isset($_SESSION['orphan_images']))
                unset($_SESSION['orphan_images']);

            if($_POST['post_type'] == 'QUIZ') {
                $activated_languages = $admin_app->GetLanguages(1, 1);
                if(!in_array($_POST['post_properties']['language_code'], $activated_languages))
                    throw new Exception('BAD_REQUEST', 1);

                $language_tags = $admin_app->GetLanguageTags($_POST['post_properties']['language_code'], 1);
                for($i=0; $i<sizeof($_POST['post_properties']['tags']); $i++) {
                    if(!in_array($_POST['post_properties']['tags'][$i], $language_tags))
                        throw new Exception('BAD_REQUEST', 1);
                }

                $validate = ValidateQuiz($_POST['post_properties'], json_decode($_POST['post_data'], TRUE), $activated_languages, $language_tags);
    			if($validate != 1)
    				throw new Exception('BAD_REQUEST', 1);
            }
			
			if($_POST['post_properties']['post_id'] == NULL) {
				$post_id = $user_app->CreatePost($_SESSION['user']['user_id'], $_POST['post_type'], $_POST['post_properties'], $_POST['post_data'], 1);
                echo json_encode(array('post_id' => $post_id));
            }
			else {
				$user_app->UpdatePost($_POST['post_properties']['post_id'], $_SESSION['user']['user_id'], $_POST['post_properties'], $_POST['post_data'], 1);
                echo json_encode(array('published' => 1));
            }

			break;
			
		case 'ActivatePremium':
            $user_available_credits = $user_app->GetUserCredits($_SESSION['user']['user_id']);
            $post_was_premium = $user_app->WasPostPremium($_POST['post_id']);
            if($user_available_credits <= ($post_was_premium == 0 ? PREMIUM_QUIZ_CREDITS : 0))
                throw new Exception('NOT_ENOUGH_CREDITS', 1);

            $url_reg_exp = '/^(http:\/\/|https:\/\/){0,1}(www\.){0,1}(([\w-]{1,}(?:([\.]{1}[a-zA-Z-]{2,}))+))(?:([\/]{1}[\w\?\=\-\)\(\&\%\$\_\.]{0,})){0,}$/i';
            if(!preg_match($url_reg_exp, $_POST['premium_domain']) && $_POST['premium_domain'] != 'localhost')
                throw new Exception('BAD_REQUEST', 1);

            if($_POST['post_id'] == NULL) {
                $_POST['post_properties']['tags'] = array();
                $post_id = $user_app->CreatePost($_SESSION['user']['user_id'], $_POST['post_type'], $_POST['post_properties'], $_POST['post_data'], 0);
                $new_quiz = 1;
            }
            else {
                $post_id = $_POST['post_id'];
                $new_quiz = 0;
            }

            $credits_used = $user_app->PostPremiumActivate($_SESSION['user']['user_id'], $post_id, $_POST['premium_domain'], PREMIUM_QUIZ_CREDITS, $post_was_premium);
            $user_available_credits = $user_available_credits - $credits_used;
            if($new_quiz == 1)
                echo json_encode(array('post_id' => $post_id, 'activated' => 1, 'credits_remaining' => $user_available_credits));
            else
                echo json_encode(array('activated' => 1, 'credits_remaining' => $user_available_credits));

            break;

		case 'DeactivatePremium':
            $user_app->PostPremiumDeactivate($_SESSION['user']['user_id'], $_POST['post_id']);

            echo json_encode(array('deactivated' => 1));

            break;

        case 'PremiumDomainEdit':
            $url_reg_exp = '/^(http:\/\/|https:\/\/){0,1}(www\.){0,1}(([\w-]{1,}(?:([\.]{1}[a-zA-Z-]{2,}))+))(?:([\/]{1}[\w\?\=\-\)\(\&\%\$\_\.]{0,})){0,}$/i';
            if(!preg_match($url_reg_exp, $_POST['premium_domain']) && $_POST['premium_domain'] != 'localhost')
                throw new Exception('BAD_REQUEST', 1);

            $user_app->PostPremiumDomainEdit($_SESSION['user']['user_id'], $_POST['post_id'], $_POST['premium_domain']);

            echo json_encode(array('updated' => 1));

            break;

        case 'GetUserCredits':
            $user_available_credits = $user_app->GetUserCredits($_SESSION['user']['user_id']);
            
            echo json_encode(array('credits_remaining' => $user_available_credits));

            break;

        case 'GetLanguageTags':
            $language_tags = $admin_app->GetLanguageTags($_POST['language_code']);
            
            echo json_encode(array('language_tags' => $language_tags));

            break;

        case 'DeleteOrphanImages':
            ignore_user_abort(true);
            if(isset($_SESSION['orphan_images'])) {
                for($i=0; $i<sizeof($_SESSION['orphan_images']); $i++) {
                    unlink($_SESSION['orphan_images'][$i]);
                }
                unset($_SESSION['orphan_images']);
            }

            break;

        case 'HidePost':
            $user_app->HidePost($_SESSION['user']['user_id'], $_POST['post_id']);

            echo json_encode(array('updated' => 1));

            break;

        case 'ShowPost':
            $user_app->ShowPost($_SESSION['user']['user_id'], $_POST['post_id']);

            echo json_encode(array('updated' => 1));

            break;

        case 'DeletePost':
            $user_app->DeletePost($_SESSION['user']['user_id'], $_POST['post_id']);

            echo json_encode(array('deleted' => 1));
	}
}
catch(Exception $e) {
	$LANGUAGE_STRINGS = json_decode(file_get_contents('../lang/' . $_COOKIE['language_code'] . '.txt'), TRUE);

    if($e->getCode() == 2) {
        header('Internal Server Error', true, 500);
        echo json_encode(array( 'error' => 1, 'message' => (DEBUG_MODE == 0 ? $LANGUAGE_STRINGS['general']['SERVER_FAILED'] : $e->getMessage()) ));
    }
    else {
        header('Bad Request', true, 400);
        echo json_encode(array( 'error' => 1, 'message' => $LANGUAGE_STRINGS['general'][$e->getMessage()] ));
    }

	exit();
}

function ValidateQuiz($quiz_properties, $quiz_data) {
	$blank_reg_exp = '/^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/';
    $digits_reg_exp = '/^[0-9]{1,}$/';

    if($quiz_properties['image_id'] == NULL)
        return -1;

    if(!preg_match($blank_reg_exp, $quiz_properties['title']))
        return -1;

    if(!preg_match($blank_reg_exp, $quiz_properties['description']))
        return -1;

    if(!($quiz_properties['type'] == 1 || $quiz_properties['type'] == 2))
        return -1;

    if(sizeof($quiz_data['questions']) < 2 || sizeof($quiz_data['questions']) > 18)
        return -1;

    for($i=0; $i<sizeof($quiz_data['questions']); $i++) {
    	if($quiz_data['questions'][$i]['image_id'] == NULL && !preg_match($blank_reg_exp, $quiz_data['questions'][$i]['text']))
    		return -1;

    	if(sizeof($quiz_data['questions'][$i]['options']) < 2 || sizeof($quiz_data['questions'][$i]['options']) > 5)
    		return -1;

    	$correct_option_count = 0;
		for($j=0; $j<sizeof($quiz_data['questions'][$i]['options']); $j++) {
            if($quiz_data['questions'][$i]['options'][$j]['image_id'] == NULL && !preg_match($blank_reg_exp, $quiz_data['questions'][$i]['options'][$j]['text']))
            	return -1;

            if($quiz_properties['type'] == 1) {
            	if($quiz_data['questions'][$i]['options'][$j]['correct'] == 1) {
                    $correct_option_count++;
                }
            }
            else if($quiz_properties['type'] == 2) {
            	if(!preg_match($digits_reg_exp, $quiz_data['questions'][$i]['options'][$j]['weight']))
            		return -1;
            }
        }
        if($quiz_properties['type'] == 1) {
            if($correct_option_count != 1) 
            	return -1;
        }
    }

    for($i=0; $i<sizeof($quiz_data['results']); $i++) {
    	if(!preg_match($blank_reg_exp, $quiz_data['results'][$i]['title']))
        	return -1;
    }

    if(sizeof($quiz_properties['tags']) == 0 || sizeof($quiz_properties['tags']) > 3)
    	return -1;

    return 1;
}

?>