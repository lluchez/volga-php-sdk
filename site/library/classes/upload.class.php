<?php

/******************************
 *        Class Upload        *
 ******************************/

/**
 * Here is the list of functions:
 *    - Upload()
 *
 *  	- moveTo($folder, [$filename, [$index_length]])
 *  	- moveImageTo($folder, $thumb_folder, $large_prop, $thumb_prop, [$filename, [$index_length]])
 *  	- getErrorCode()
 *  	- getErrorMessage([$prefix])
 */
 
  define('UPLOAD_ERR_NO_ERROR',  0); // no error
  define('UPLOAD_ERR_UNDEFINED', 1); // key refers to an undefined input-file field
	define('UPLOAD_ERR_TOO_LARGE', 2); // file too large
//define('UPLOAD_ERR_PARTIAL',   3); // transfert stopped (partial file) /** already defined */
//define('UPLOAD_ERR_NO_FILE',   4); // no file selected                 /** already defined */
	define('UPLOAD_ERR_UNEXPECT',  5); // unexpected/generic error

class Upload
{
	const UPLOAD_ERR_PREFIX ='upload.error.';

	// not null if an error has occured
	private $error_code = UPLOAD_ERR_NO_ERROR;
	// file link
	private $file_info = null;
	
	// constructor, @param $file_input name of the <input>File
	public function Upload($file_input)
	{
		$this->file_info = $_FILES[$file_input];
		if( ! $this->file_info )
		{
			$this->error_code = UPLOAD_ERR_UNDEFINED;
		}
		elseif( $err_code = $this->info('error') )
		{
			switch ( $err_code )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$this->error_code = UPLOAD_ERR_TOO_LARGE; break;
				case UPLOAD_ERR_PARTIAL:
				case UPLOAD_ERR_NO_FILE:
					$this->error_code = $err_code; break;
				default:
					$this->error_code = UPLOAD_ERR_UNEXPECT;
			}
		}
		if( $this->error_code )
			Common::reportRunningWarning("The file hasn't been uploaded for the Upload object based on '$file_input'");
	}
	
	
	public function moveTo($folder, $filename = null, $index_length = 2)
	{
		if( $this->error_code == UPLOAD_ERR_NO_ERROR )
		{
			if( $filename = $this->generateOutputFilePath($folder, $filename, $index_length) )
				@move_uploaded_file($this->info('tmp_name'), $filename);
			return $filename;
		}
	}
	
	/**
	 * The uploaded file is supposed to be an image
	 * properties allowed: 'width' and 'height', 'max-width'/'max_w', 'max-height'/'max_h', 'quality'
	 */
	public function moveImageTo($folder, $thumb_folder, $large_prop, $thumb_prop, $filename = null, $index_length = 2)
	{
		if( $this->error_code == UPLOAD_ERR_NO_ERROR )
		{
			if( $filenames = $this->generateOutputFilePath($folder, $filename, $index_length, 'jpg', $thumb_folder) )
			{
				$img1 = Image::resizeAdvanced($this->info('tmp_name'), $filenames[0], $large_prop);
				$img2 = Image::resizeAdvanced($this->info('tmp_name'), $filenames[1], $thumb_prop);
				if( ! is_null($img1) && ! is_null($img2) )
					return Array($img1, $img2);
				if( ! is_null($img1) )
					@unlink($img1);
			}
		}
		return null;
	}
	
	// returns the error code. 0 means no error
	public function getErrorCode()
	{
		return $this->error_code;
	}
	
	// returns the error description stored in the DB
	public function getErrorMessage($prefix = null)
	{
		$prefix = $prefix || self::UPLOAD_ERR_PREFIX;
		if( $this->error_code )
			return Lang::translate(self::UPLOAD_ERR_PREFIX.$this->error_code);
	}
	
	
	// returns an array of full path if the second parameter is not null
	private function generateOutputFilePath($folder, $new_name, $index_length, $extension = null, $folder2 = null)
	{
		if( $filename = $this->info('name') )
		{
			$folder  = Common::normalizeFolder($folder);
			$folder2 = Common::normalizeFolder($folder2);
			$index = '';
			$base  = Common::getFilename($new_name ? $new_name : $filename);
			$ext   = '.' . ( $extension ? $extension : Common::getExtention(strstr($new_name, '.') ? $new_name : $filename));
			$file  = String::cleanFilename($base).$ext;
			if( $index_length = abs($index_length) && @file_exists($folder.$file) )
			{
				$i = 1;
				do
				{
					$index = '_'.str_pad($i++, $index_length, '0', STR_PAD_LEFT);
					$file = String::cleanFilename($base.$index) . $ext;
				}
				while( @file_exists( $folder.$file ) );
			}
			//else
			//	$file = String::cleanFilename($base).$ext;
			if( is_null($folder2) )
				return $folder.$file;
			else
				return Array($folder.$file, $folder2.$file);
		}
		return null;
	}
	
	private function info($key) // 'temp_name', 'name, 'size', 'error'
	{
		return $this->file_info[$key];
	}
	
	// destructor
	function __destruct()
	{
		if( @file_exists($this->info('temp_file')) )
		{
			@unlink( $this->info('temp_file') );
			echo "*** temp uploaded file deleted ***\n";
		}
	}
	
}

?>