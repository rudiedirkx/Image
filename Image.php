<?php

class Image {

	public $original_image; // GD resource
	public $image; // GD resource
	public $image_type; // Int
	//public $background_color; // Array(R, G, B)

	public function __construct($filename = null) {
		$filename && $this->load($filename);
	}

	public function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];

		if ( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		}
		else if ( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		}
		else if ( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}

		$this->original_image = $this->image;
	}

	public function getWidth() {
		return imagesx($this->image);
	}

	public function getHeight() {
		return imagesy($this->image);
	}

	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width, $height);
	}

	public function scale($scale) {
		$width = $this->getWidth() * $scale;
		$height = $this->getheight() * $scale;

		$this->resize($width, $height);
	}

	public function resize($width, $height = 0) {
		if ( !$height ) {
			return $this->resizeToWidth($width);
		}
		else if ( !$width ) {
			return $this->resizeToHeight($height);
		}

		$new_image = imagecreatetruecolor($width, $height);
		$this->ensureTransparency($new_image);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}

	public function ensureTransparency(&$new_image) {
		if ( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {
			// set bg color
			/*if ( $this->background_color && is_array($this->background_color) ) {
				$args = $this->background_color;
				array_unshift($args, $new_image);
				$bgcolor = call_user_func_array('imagecolorallocate', $args);
				imagefill($new_image, 0, 0, $bgcolor);
			}*/

			// fix transparency
			$transparency = imagecolortransparent($new_image);
			if ( $transparency >= 0 ) {
				$trnprt_color = imagecolorsforindex($image, $transparency);
				$transparency = imagecolorallocate($new_image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($new_image, 0, 0, $transparency);
				imagecolortransparent($new_image, $transparency);
			}
			else if ( $this->image_type == IMAGETYPE_PNG ) {
				imagealphablending($new_image, false);
				$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
				imagefill($new_image, 0, 0, $color);
				imagesavealpha($new_image, true);
			}
		}
	}

	public function outputPrep($image_type) {
		if ( $image_type == IMAGETYPE_PNG ) {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
		}
	}

	public function save($filename, $image_type = IMAGETYPE_JPEG, $options = array()) {
		// options
		isset($options['quality']) or $options['quality'] = 75;
		isset($options['chmod']) or $options['chmod'] = 0;

		$saved = false;

		$this->outputPrep($image_type);

		if ( $image_type == IMAGETYPE_JPEG ) {
			$saved = imagejpeg($this->image, $filename, $options['quality']);
		}
		else if ( $image_type == IMAGETYPE_GIF ) {
			$saved = imagegif($this->image, $filename);
		}
		else if ( $image_type == IMAGETYPE_PNG ) {
			$saved = imagepng($this->image, $filename);
		}

		if ( $saved ) {
			if ( $options['chmod'] ) {
				chmod($filename, $options['chmod']);
			}
		}

		return $saved;
	}

	public function output($image_type = IMAGETYPE_JPEG, $options = array()) {
		// options
		isset($options['quality']) or $options['quality'] = 75;

		$this->outputPrep($image_type);

		if ( $image_type == IMAGETYPE_JPEG ) {
			header('Content-type: image/jpeg');
			imagejpeg($this->image, null, $options['quality']);
		}
		else if ( $image_type == IMAGETYPE_GIF ) {
			header('Content-type: image/gif');
			imagegif($this->image);
		}
		else if ( $image_type == IMAGETYPE_PNG ) {
			header('Content-type: image/png');
			imagepng($this->image);
		}
	}

}


