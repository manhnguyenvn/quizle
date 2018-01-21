<?php
session_start();
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/image-cropper.php');
require_once('../classes/GifFrameExtractor.php');
require_once('../classes/GifCreator.php');
date_default_timezone_set(TIMEZONE);

try {
    if(!isset($_SESSION['user']))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    if(!in_array($_POST['post_type'], ['QUIZ']))
        throw new Exception('BAD_REQUEST', 1);

    if($_FILES['post_image']['error'] !== UPLOAD_ERR_OK)
        throw new Exception('Error : Upload Failed', 2);

    if(!isset($_SESSION['orphan_images']))
        $_SESSION['orphan_images'] = [];

    $uploaded_image_type = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
    if($uploaded_image_type == 'jpg' || $uploaded_image_type == 'jpeg') 
        $image_type = 'jpg';
    else if($uploaded_image_type == 'png')
        $image_type = 'png';
    else if($uploaded_image_type == 'gif')
        $image_type = 'gif';
    else 
        throw new Exception('BAD_REQUEST', 1);

    if($_FILES['post_image']['size'] > (MAX_IMAGE_SIZE_ALLOWED_MB*1024*1024)) 
        throw new Exception('BAD_REQUEST', 1);

    list($image_width, $image_height) = getimagesize($_FILES['post_image']['tmp_name']);
    if($_POST['post_type'] == 'QUIZ') {
        if($_POST['target'] == 'option') {
            $required_width = 180;
            $required_height = 180;
        }
        else {
            $required_width = 600;
            $required_height = 325;
        }

        if($_POST['target'] == 'quiz') {
            $keep_gif_animations = 0;
            $create_thumbnail = 1;
        }
        else {
            $keep_gif_animations = 1;
            $create_thumbnail = 0;
        }
        
        if($image_width < $required_width || $image_height < $required_height)
            throw new Exception('BAD_REQUEST', 1);

        $image_id = uniqid(NULL, true) . '.' . rand(111, 999) . '.' . $_SESSION['user']['user_id']; 
        $image_destination = '../img/' . $_POST['post_type'] . '/' . $_POST['target'] . '/' . $image_id . '.' . $image_type;
    }

    $image = new ImageCropper($_POST['image_data'], $_FILES['post_image'], $image_id, $image_destination, $image_type, $image_width, $image_height, $required_width, $required_height, $keep_gif_animations);
    $_SESSION['orphan_images'][] = $image_destination;

    if($create_thumbnail == 1) {
        $required_width = 370;
        $required_height = 200;
        $image_destination = '../img/' . $_POST['post_type'] . '/' . $_POST['target'] . '/m-' . $image_id . '.' . $image_type;

        $image = new ImageCropper($_POST['image_data'], $_FILES['post_image'], $image_id, $image_destination, $image_type, $image_width, $image_height, $required_width, $required_height, $keep_gif_animations);
        $_SESSION['orphan_images'][] = $image_destination;
    }

    echo json_encode(array('image_info' => array('image_id' => $image_id  . '.' . $image_type, 'image_destination' => 'img/' . $_POST['post_type'] . '/' . $_POST['target'] . '/' . $image_id . '.' . $image_type)));
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

?>