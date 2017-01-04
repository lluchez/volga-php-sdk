<?php

/******************************
 *         Media Class        *
 ******************************/

/**
 * @desc-start
 * This class create string for HTML 'href'/'action' properties (ig: create links working with the URL Rewriting rules).
 * This class also create JavaScript command to add text to the JS Object: Messages.
 * @desc-end
 * 
 *
 * Here is the list of static functions:
 *    - Media::init($replace_char, $patterns, $mime_types)
 *
 *    - Media::linkWithParameters($page, $params)
 *    - Media::createLink($page, $pattern_key, $params)
 *
 *    - Media::HTML($filename, [$params])
 *    - Media::CSS($filename, [$params])
 *    - Media::JS($filename, [$params])
 *    - Media::IMAGE($filename, [$params])
 *    - Media::DATA($filename)
 *    - Media::AJAX($filename, [$params])
 *    - Media::IMAGE_($filename, $folders)
 *
 *    - Media::getMimeType($extension)
 *
 *    - Media::addMessage($key, $value, [$tab])
 *    - Media::addEscapedMessage($key, $value, [$tab])
 *    - Media::displayErrorMessages($keyPrefix, $settings, [$padding])
 */



class Media
{

	private static $replace_char = null;
	private static $mime_types = null;
	public static $patterns = Array();
	

	/******************************************
	 *            public functions            *
	 ******************************************/

	// Initilisation function of this class. To be called before calling any function
	// This class use URL Rewriting to create pretty links.
	// You have to provide a char that will be replaced in each pattern (to determine how links look like).
	// You can change it but you will have to edit the '.htaccess' file located at the root.
	// @param $replace_char Char searched in the patterns below (found char will be replaced by the file basename)
	// @param $patterns Url patterns for pages (contains the extension too)  expect for images, attachments and Ajax
	// @param $mime_types Assoc array to define mime type from extension
	public static function init($replace_char, $patterns, $mime_types)
	{
		self::$replace_char = $replace_char;
		self::$patterns     = $patterns;
		self::$mime_types   = $mime_types;
	}
	
	// Create a partial link by mixing the file name and the paramters given: '$page?$params'
	// The parameter $params can be a String or an Array.
	// If it's a String is should be this format: 'param1=val1&param2=val2'. '?' can be used too and will be remplaced if not correct. _
	//   In this case you better use by yourself urlencode() as it won't be called here...
	// If $params is an Array it should be compsed this way: Array('param1' => 'val1' 'param2' => 'val2'). _
	// If the associated value is null (eg: 'param1' => null), it will be as if you had: 'param1' => Vars::get('param1'). _
	// Then each couple is recomposed using urlencode() : $page.'?'.$key[0].'='.urlencode($val[0]).'&'.$key[1].'='.urlencode($val[1])...
	// If the multi session mode is activated (eg: SETTINGS_USE_MULTI_SESSIONS), then the session Id is added too.
	// @param $page Page name
	// @param $params Paramters to add to the link
	// @return Return a partial link with the file name and the parameters.
	public static function linkWithParameters($page, $params)
	{
		$parameters = '';
		if( is_array($params) )
		{
			$temp = Array();
			foreach($params as $key => $val)
			{
				if( is_null($val) )
					$val = Vars::get($key);
				$temp[] = $key.'='.urlencode($val);
			}
			$parameters = implode('&', $temp);
		}
		elseif( is_string($params) )
			$parameters = $params;
		
		if( $parameters )
			$parameters = (strstr($page, '?') ? '&' : '?') . $parameters;
		$page .= $parameters;
		if( SETTINGS_USE_MULTI_SESSIONS )
			$page .= (strstr($page, '?') ? '&' : '?') . Session::getSessionIdKey() .'='. session_id();
		return $page;
	}
	
	// Create a partial link for the Web site according to the type. 
	// For the parameters $params, see self::linkWithParameters(). _
	// It also accepts this kind of value: 'file|a=1|b=15' will be transformed into 'file?a=1&b=15'.
	// @param $page Page name
	// @param $pattern_key Key of the pattern to use: Page::KEY_HTML/Page::KEY_JS/Page::KEY_CSS/Page::KEY_IMAGE/Page::KEY_DATA/self::AJAX
	// @param $params Paramters to add to the link.
	// @return Return a partial link corresponding t the kind of media, with the right name and the parameters (eg: html/page.html?param1=val1)
	public static function createLink($page, $pattern_key, $params)
	{
		$page = self::linkWithParameters($page, $params);
		$page = str_replace('&', '|', $page);
		$page = str_replace('?', '|', $page);
		$parts = explode('|', $page);
		$link = str_replace(self::$replace_char, array_shift($parts), self::$patterns[$pattern_key]);
		if( count($parts) > 0 )
			$link .= '?' . implode('&', $parts);
		return $link;
	}
	
	// Create a relative/absolute HTML link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// @param $filename Page name
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute HTML link
	public static function HTML($filename, $params = null)
	{
		return self::createSmartLink($filename, Page::KEY_HTML, $params);
	}
	
	// Create a relative/absolute CSS link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// @param $filename Page name
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute CSS link
	public static function CSS($filename, $params = null)
	{
		return self::createSmartLink($filename, Page::KEY_CSS, $params);
	}
	
	// Create a relative/absolute JavaScript link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// @param $filename Page name
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute JavaScript link
	public static function JS($filename, $params = null)
	{
		return self::createSmartLink($filename, Page::KEY_JS, $params);
	}
	
	// Create a relative/absolute image link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// If the image is not at the root of the image directory, '/' are replaced by '.'
	// If the image file is a PHP file (eg: 'banner.png.php'), please add the final-image extention before the PHP extension to return right format.
	// [code]
	// self::IMAGE('menu/bkgd'); // returns: 'image/menu.bkgd.png', real file: {DIR_IMG}/menu/bkgd.png
	// self::IMAGE('menu/logo'); // returns: 'image/menu.logo.gif', real file: {DIR_IMG}/menu/logo.gif.php
	// [/code]
	// @param $filename Page name
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute JavaScript link
	public static function IMAGE($filename, $params = null)
	{
		return self::createSmartLink(self::IMAGE_($filename, Array(DIR_IMAGES)), Page::KEY_IMAGE, $params);
	}
	
	// Create a relative/absolute document link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// @param $filename Page name
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute document/attachment link
	public static function DATA($filename)
	{
		$filename = str_replace('/', '.', $filename);
		return self::createSmartLink($filename, Page::KEY_DATA, null);
	}
	
	// Create a relative/absolute AJAX link combining the file basename and the parameters (depending of the value of $displayRelativePath when Common::init())
	// @param $filename Page name. Can be null, then current page name is used
	// @param $params Paramters to add to the link.
	// @return Return a relative/absolute HTML link
	public static function AJAX($filename = null, $params = null)
	{
		if( is_null($filename) )
			$filename = Page::getPageCodeOrName();
		if( ! strstr($filename, '.') )
			$filename .= ".html";
		return self::createSmartLink($filename, Page::KEY_AJAX, $params);
	}


	// Search for an imagine matching the filename provided located in the list of folders. '/' are converted to '.'
	// Do not use this function (kind of private function).
	// [code]
	// Media::IMAGE_('menu/background', DIR_IMG); // returns: 'menu.background.png'
	// [/code]
	// @param $filename Page name
	// @param $folders List of folders where the image should be found. Can be an array or an exploded string(separtor: ';')
	// @return Return the image link completed by the right extension (extension are high sorted according to browser specifications).
	public static function IMAGE_($filename, $folders) // should be public because used by Lang class
	{
		$path = Common::getFilePath($filename, $folders, Client::getSupportedImageTypes(true));
		$filename = $path ? $path : $filename;
		if( Common::getExtention($filename) == 'php' )
			$filename = preg_replace('/\.php$/', '', $filename);
		foreach($folders as $folder)
			$filename = str_replace($folder, '', $filename);
		$filename = str_replace('/', '.', $filename);
		return $filename;
	}	
	
	/******************************************
	 *          Mime-types function           *
	 ******************************************/
	
	// Returns the corresponding mime type. Example: 'text/html', 'image/png', etc.
	// @param $extension File extension. Can also be a full file name.
	// @return Returns the corresponding Mime Type
	public static function getMimeType($extension)
	{
		if( strpos($extension, '.') !== false )
			$extension = Common::getExtention($extension);
		$extension = strtolower($extension);
		if( $mime = self::$mime_types[$extension] )
			return $mime;
		else
			return "text/{$extension}";
	}
	
	/******************************************
	 *          JavaScript functions          *
	 ******************************************/
	
	// Used by HTML and JavaScript pages. _
	// This function is usefull to add raw text to the JS Messages object to display texts. _
	// Not really usefull if the Website is not multilingual
	// See also: Lang::addMessage()
	// @param $key Text key
	// @param $value Text value, not escaped (raw text)
	// @param $tab Left padding for indentation
	// @return Write the JavaScript command to add text to the Js Messages object
	public static function addMessage($key, $value, $tab = '')
	{
		echo self::addEscapedMessage($key, String::toJS($value), $tab);
	}
	
	// Used by HTML and JavaScript pages. _
	// This function is usefull to add safe text to the JS Messages object to display texts. _
	// Not really usefull if the Website is not multilingual.
	// See also: Lang::addMessage(), String::toJS()
	// @param $key Text key
	// @param $value Text value, escaped (with String::toJS function)
	// @param $tab Left padding for indentation
	// @return Write the JavaScript command to add text to the Js Messages object
	public static function addEscapedMessage($key, $value, $tab = '')
	{
		echo $tab."Core.Messages.add('$key', '$value');\n";
	}
	
	
	// Create the lines used to report errors form forms.
	// It creates the reportError() and the showErrorPanel() calls.
	// $_GET[POST_ERROR] should be defined: contains errorID imploding with ','
	// @param $keyPrefix Prefix for resource keys. Then we only add the error ID
	// @param $settings Array for association error ID => {formID, fieldID}
	// @param $padding Left padding
	// @return Displays the lines and returns the number of errors displayed
	public static function displayErrorMessages($keyPrefix, $settings, $padding = "\t")
	{
		if( Vars::defined(POST_ERROR) )
		{
			$errors = Vars::get(POST_ERROR);
			if( is_string($errors) )
				$errors = explode(',', $errors);
			echo "\n$padding/** Display errors */\n";
			$forms = Array();
			foreach($errors as $error)
			{
				if( $msg = $settings[$error] )
				{
					$sender = $msg['sender'] ? ("$('".$msg['sender']."')") : 'null' ;
					if( $errID = intval($error) )
						echo "$padding".$msg['form'].".reportError(Core.Messages.get('$keyPrefix$errID'), $sender, true);\n";
					$forms[$msg['form']] = true;
				}
			}
			foreach($forms as $formId => $v)
				echo "$padding$formId.showErrorPanel();\n";
			return count($errors);
		}
		return 0;
	}
	
	
	/******************************************
	 *            private functions           *
	 ******************************************/
	
	private static function createSmartLink($link, $pattern_key, $params)
	{
		return Common::getPageURL(self::createLink($link, $pattern_key, $params));
	}
	
}

?>