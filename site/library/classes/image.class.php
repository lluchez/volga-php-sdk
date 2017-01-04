<?php

/******************************
 *         Class Image        *
 ******************************/

/**
 * @desc-start
 * This class is static so you just need to call init($image_quality)
 * This class supports many features to easily compress or resize an uploaded image
 * @desc-end
 *
 * Here is the list of functions:
 *    - Image::init($image_quality)
 *
 *  	- Image::resizeAdvanced($src_file, $dst_file, [$options])
 *  	- Image::resize($src_file, $dst_file, $dest_w, $dest_h, [$format, [$quality]])
 *
 *  	- Image::getHeightFromMaxWidth($image, $width)
 *  	- Image::getWidthFromMaxHeight($image, $height)
 *  	- Image::getMaxSize($image, $max_width, $max_height)
 */


class Image
{
	
	// JPEG quality
	private static $image_quality = 0;
	
	// Initilisation function of this class. To be called before calling any function
	// @param $image_quality Default quality level when saving an image. Value between 0 and 100
	public static function init($image_quality)
	{
		self::$image_quality = intval($image_quality ? self::quality($image_quality) : 70);
	}
	
	
	// Resize an image with advanced options (check $options description)
	// To specify teh rendering quality set the value of $options['quality'] (ignored for GIF files)
	// @param $src_file Filename of the source image
	// @param $dst_file Filename to the image to create
	// @param $options Array having some of these keys: 'width', 'height', 'max-width'/'max_w', 'max-height'/'max_h', 'quality'
	// @param $format image extension. Supports: 'jpg, 'png' and 'gif'
	// @return Returns the path of image created
	public static function resizeAdvanced($src_file, $dst_file, $options = null, $format = 'jpg')
	{
		if( $info = @getimagesize($src_file) )
		{
			$width   = intval($options['width']);
			$height  = intval($options['height']);
			$quality = $options['quality'] ? self::quality($options['quality']) : self::$image_quality;
			if( ! $width || ! $height )
			{
				$width  = $max_w = intval(Common::notNull($options['max-width'],  $options['max_w']));
				$height = $max_h = intval(Common::notNull($options['max-height'], $options['max_h']));
				if( $max_w && $max_h )
					list($width, $height) = self::getMaxSize($info, $max_w, $max_h);
				elseif( $max_w )
					$height = self::getHeightFromMaxWidth($info, $width);
				elseif( $max_h )
					$width  = self::getWidthFromMaxHeight($info, $height);
				else
					list($width, $height) = $info;
			}
			return self::resize($src_file, $dst_file, $width, $height, $format, $quality);
		}
		return null;
	}
	
	// Resize an image and save at at the destination defined with the specific new size
	// @param $src_file Filename of the source image
	// @param $dst_file Filename to the image to create
	// @param $dest_w Created image width
	// @param $dest_h Created image height
	// @param $format Format/extension of the created image. Supports: 'jpg, 'png' and 'gif'
	// @param $quality Output quality (not supported by GIF images: will be ignored)
	// @return Returns the path of image created (add the right extension if not well set)
	// Can generate Warning messages
	public static function resize($src_file, $dst_file, $dest_w, $dest_h, $format = 'jpg', $quality = null)
	{
		if     ( $source = @imagecreatefromjpeg($src_file) ) {}
		elseif ( $source = @imagecreatefrompng( $src_file) ) {}
		elseif ( $source = @imagecreatefromgif( $src_file) ) {}
		else return null;
		
		$dest_w = intval($dest_w);
		$dest_h = intval($dest_h);
		
		if( $dest_w < 1 || $dest_h < 1 )
		{
			Common::reportRunningWarning("Invalid size for Image::resize() for image '$src_file' to '$dst_file'");
			return null;
		}
		
		$quality = $quality ? self::quality($quality) : self::$image_quality;
		if ( $dest = @imagecreatetruecolor ($dest_w, $dest_h) )
		{
			if ( @imagecopyresized($dest, $source, 0,0, 0,0,	$dest_w, $dest_h, imagesx($source), imagesy($source)) )
			{
				$success = false;
				if( ! preg_match("/\.$format$/i", $dst_file) )
					$dst_file .= '.' . $format;
				@unlink($dst_file);
				switch( $format )
				{
					case 'png': $success = @imagepng($dest, $dst_file, $quality); break;
					case 'gif': $success = @imagegif($dest, $dst_file); break;
					default: $success = @imagejpeg($dest, $dst_file, $quality);
				}
				if( $success )
				{
					@imagedestroy($dest);
					return $dst_file;
				}
			}
		}
		if( extension_loaded('gd2') )
			Common::reportRunningWarning("Image::resize() failed for image '$src_file'. Incorrect path or incorrect file !");
		else
			Common::reportRunningWarning("Image::resize() failed for image '$src_file'. Check if GD2 is enabled !");
		return null;
	}

	// Compute the height of the scalled image base on a new width
	// @param $image Image path
	// @param $width define the new width
	// @return Returns the height related to the new width (keeping image-size ratio)
	public static function getHeightFromMaxWidth($image, $width)
	{
		if( is_string($image) )
			$image = @getimagesize($image);
		if( ! is_array($image) )
			return NULL;
		$h = $image[1];
		$w = $image[0];
		return $h * ($width / $w);
	}
	
	// Compute the width of the scalled image base on a new height
	// @param $image Image path
	// @param $height define the new height
	// @return Returns the width related to the new height (keeping image-size ratio)
	public static function getWidthFromMaxHeight($image, $height)
	{
		if( is_string($image) )
			$image = @getimagesize($image);
		if( ! is_array($image) )
			return NULL;
		$h = $image[1];
		$w = $image[0];
		return $w * ($height / $h);
	}
	
	// Compute the width and the height of the scalled image base on the max width and max height given.
	// Used to get the sizes an image to fit within a sized box
	// @param $image Image path
	// @param $max_width define the max width
	// @param $max_height define the max height
	// @return Returns an array([0] => width, [1] => height) corresponding to computed sizes
	public static function getMaxSize($image, $max_width, $max_height)
	{
		if( is_string($image) )
			$image = @getimagesize($image);
		if( ! is_array($image) )
			return NULL;
		$h = $image[1];
		$w = $image[0];
		$h2 = $h * ($max_width / $w);
		if( $h2 > $max_height)
			return Array(intval($w * ($max_height / $h)), $max_height);
		else
			return Array($max_width, intval($h2));
	}
	
	// Keep the quality value between 0 and 100 and cast it into an integer
	private static function quality($val)
	{
		return Common::minmax(intval($val), 0, 100);
	}
	
}
?>