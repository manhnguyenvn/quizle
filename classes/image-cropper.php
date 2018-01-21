<?php

class ImageCropper
{
    private $file;
    private $image_type;
    private $image_width;
    private $image_height;
    private $data;
    private $image_id;
    private $image_destination;
    private $required_width;
    private $required_height;
    private $keep_gif_animations;

    function __construct($data, $file, $image_id, $image_destination, $image_type, $image_width, $image_height, $required_width, $required_height, $keep_gif_animations = 1) {
        $this -> file = $file;
        $this -> data = json_decode(stripslashes($data));
        $this -> image_id = $image_id;
        $this -> image_destination = $image_destination;
        $this -> image_type = $image_type;
        $this -> image_width = $image_width;
        $this -> image_height = $image_height;
        $this -> required_width = $required_width;
        $this -> required_height = $required_height;
        $this -> keep_gif_animations = $keep_gif_animations;

        $this -> CropImage();
    }

    public function CropImage() {
    	switch($this -> image_type) {
			case 'jpg':
				$resized_image = $this -> CropImageInternal(imagecreatefromjpeg($this -> file['tmp_name']));
				$success = imagejpeg($resized_image, $this -> image_destination, 100);
                imagedestroy($resized_image);
                break;

			case 'png':
				$resized_image = $this -> CropImageInternal(imagecreatefrompng($this -> file['tmp_name']));
                $success = imagepng($resized_image, $this -> image_destination);
                imagedestroy($resized_image);
                break;

            case 'gif':
                if($this -> keep_gif_animations == 0 || !GifFrameExtractor::isAnimatedGif($this -> file['tmp_name'])) {
                    $resized_image = $this -> CropImageInternal(imagecreatefromgif($this -> file['tmp_name']));
                    $success = imagegif($resized_image, $this -> image_destination);
                    imagedestroy($resized_image);
                }
                else {
                    $gfe = new GifFrameExtractor();
                    $gfe->extract($this -> file['tmp_name']);
                    $durations = $gfe->getFrameDurations();
                    $resized_frames = array();
                    foreach($gfe->getFrames() as $frame) {
                        $resized_image = $this -> CropImageInternal($frame['image']);
                        $resized_frames[] = $resized_image;
                    }

                    $gc = new GifCreator();
                    $gc->create($resized_frames, $durations, 0);
                    $success = file_put_contents($this -> image_destination, $gc->getGif());
                }
        }

        if(!$success)
            throw new Exception('Error : Failed to save the cropped image file', 2);
    }

    public function CropImageInternal($src_img) {
        $src_img_w = $this -> image_width;
        $src_img_h = $this -> image_height;

        $tmp_img_w = $this -> data -> width;
        $tmp_img_h = $this -> data -> height;
        $dst_img_w = $this -> required_width;
        $dst_img_h = $this -> required_height;

        $src_x = $this -> data -> x;
        $src_y = $this -> data -> y;

        if($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } 
        else if($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } 
        else if($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } 
        else if ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } 
        else if ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;

        if($this -> image_type == 'gif')
            $dst_img = imagecreate($dst_img_w, $dst_img_h);
        else
            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 255, 255, 255, 127));
        imagesavealpha($dst_img, true);

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        if(!$result)
            throw new Exception('Error : Failed to crop the image file', 2);

        imagedestroy($src_img);

        return $dst_img;
    }
}

?>