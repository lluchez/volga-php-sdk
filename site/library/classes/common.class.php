<?php

/******************************
 *        Class Common        *
 ******************************/

/**
 * @desc-start
 * Handles basic things.
 * This class is a static class. Hence no instance is needed and you can _
 *   directly calls functions by prefixing them with 'Common::'
 *
 * You can edit the function 'Common::isLocalContext()' to really match your dev environment. _
 *   I needed to test if the host was localhost and if I wan using a static DNS. _
 *   The up to you to change this !
 * The 'Common::isDebugContext()' function is also defined to returns true only _
 *   if 'SETTINGS_DEBUG_MODE' and 'Common::isLocalContext()' are true.
 * @desc-end
 *
 * Here is the list of functions:
 *  Common::init($isLocalContext, $mailResult, $alias, $displayRelativePath)
 *
 *  \Context Methods
 *		- Common::isLocalContext()
 *		- Common::isDebugContext()
 *		- Common::isAdminContext()
 *		- Common::print_r()
 *		- Common::reportRunningWarning($errorMessage)
 *		- Common::reportFatalError($errorMessage)
 *		- Common::getDomain()
 *		- Common::isRightReferer()
 *		- Common::getPageURL([$page, [$relativeLink]])
 *
 *  \Date Methods
 *		- Common::getDateUS([$dtime, [$fulltime]])
 *    - Common::getDateFr([$dtime, [$fulltime]])
 *    - Common::formatDateToSQL([$dtime, [$fulltime]])
 *    - Common::convertStringDate($datetime, $pattern)
 *
 *  \File and Path Methods
 *    - Common::normalizeFolder($path)
 *    - Common::getFileContent($filename)
 *    - Common::getDirname($filename)
 *    - Common::getBasename($filename)
 *    - Common::getExtention($filename)
 *    - Common::getFilename($filename)
 *    - Common::removeDirectory($path)
 *
 *  \Retrieve the File path
 *    - Common::getFilePath($fileName, $arr_dir, $arr_ext, [$spacer])
 *    - Common::getJSPath($fileName)
 *    - Common::getCSSPath($fileName)
 *
 *  \Maths Methods
 *		- Common::minmax($v, $min, $max)
 *		- Common::min($v1, $v2)
 *		- Common::max($v1, $v2)
 *		- Common::toFileSize($size)
 * 
 *  \Other Methods
 *		- Common::notNull()
 *		- Common::mail($to, $title, $subject, [$from])
 *		- Common::error404()
 *		- Common::assocSQLRow($sqlRow)
 *		- Common::assocSQLRows($sqlRows)
 */



/* Class definition */
class Common
{
	
	// private static members
	private static $localContext; // Local context ?
	private static $displayRelativePath; // display relative path or full (also containing domain)
	private static $defaultMailStatusResult; // status to generate when none Mail server is running
	private static $alias; // alias used to generate links
	
	// Initilisation function of this class. To be called before calling any function
	// @param $isLocalContext check if we are in a local context
	// @param $mailResult Define the value to return when trying to send an email when no mail server is running. You can change it to test your code
	// @param $alias Alias use to create links (JS/CSS/HTML,etc...)
	// @param $displayRelativePath Display relative path or full (also containing domain) for links/url
	public static function init($isLocalContext, $mailResult, $alias, $displayRelativePath = true)
	{
		self::$localContext = $isLocalContext;
		self::$displayRelativePath = $displayRelativePath;
		self::$defaultMailStatusResult = $mailResult;
		self::$alias = $alias;
		error_reporting($isLocalContext ? (E_ALL & ~E_NOTICE) : 0); // Disable error reports
	}

	/*********************************************************************
	 *                          Context Methods                          *
	 *********************************************************************/

	// Test if the server is running on a local context (localhost)
	// @return Returns true if running on a local machine 
	public static function isLocalContext()
	{
		return self::$localContext;
	}
	
	// Test if the debuging mode is activated (to display error warnings)
	// @return Returns true if Debugging mode is activated and we are in a local Context
	public static function isDebugContext()
	{
		return ( SETTINGS_DEBUG_MODE && self::isLocalContext() );
	}
	
	// Test if we are in the admin context (Back Office) or end-user section (simple view mode)
	// @return Returns whether we are on a Back Office page
	public static function isAdminContext()
	{
		return IS_ADMIN_CONTEXT;
	}
	
	// Add <pre> / </pre> tag to debug correctly arrays or PHP objects (debugging function)
	// @param $array Object/Array to debug
	// @param $name Optional to display before the Array
	public static function print_r($array, $name = null)
	{
		echo "<pre>" . (( $name ) ? "$name: " : "");
		print_r($array);
		echo "</pre>\n";
	}
	
	// Display debug warning messages (if Debug mode is ON and within local context)
	// @param $warningMessage Warning message to display
	public static function reportRunningWarning($warningMessage)
	{
		if( self::isDebugContext() )
			echo "<p><font style='color: #F84; font-size: 15px; font-weight: bold;'>$warningMessage</font></p>";
	}
	
	// Display debug fatal-error messages and terminate
	// @param $errorMessage Fatal error message to display
	public static function reportFatalError($errorMessage)
	{
		echo "<p><font style='color: #F33; font-size: 15px; font-weight: bold;'>$errorMessage</font></p>";
		die();
	}
	
	// Get the domain name prefixed by 'http://' and add the port if it's not 80 (default value)
	// @return Returns the domain name (url to the Website). Doesn't include the alias
	public static function getDomain()
	{
		$port = $_SERVER['SERVER_PORT'];
		$port = ( $port == 80 ) ? '' : ":$port";
		return 'http://' . $_SERVER['SERVER_NAME']. "$port/";
	}
	
	// Checks if $_SERVER['HTTP_REFERER'] is pointing on one of our pages
	// @return Returns true if the page referer is one of our page
	public static function isRightReferer()
	{
		if( $referer = $_SERVER['HTTP_REFERER'] )
			return strstr($referer, $_SERVER['SERVER_NAME']);
		return false;
	}
	
	// Compute the link to your page (domain+alias+page_name)
	// @param $page link of a page/css/js...
	// @param $relativeLink Boolean to say if full/relative path should be returned (if null return according to the initialization)
	// @return Returns the relative/full path to the page (includeing the alias)
	public static function getPageURL($page = '', $relativeLink = null)
	{
		if( is_null($relativeLink) )
			$relativeLink = self::$displayRelativePath;
		return ($relativeLink ? '/' : self::getDomain()) . self::$alias . $page;
	}



	/**********************************************************************
	 *                          DateTime Methods                          *
	 **********************************************************************/
	
	// Convert a date with the given pattern.
	// if 'dtime' is null, then the current datetime stamp is used
	private static function getDate($pattern, $dtime)
	{
		/*if( $fulltime )
		{
			$tPattern = 'H:i:s';
			$pattern .= ' '.$tPattern;
		}*/
		return date( $pattern, ($dtime ? $dtime : mktime()) );
	}
	
	// Convert a date into US format
	// @param $dtime A date, or the current datetime if null
	// @param $fulltime include time too or only the day ?
	// @param $longFormat Date in text format instead of only digits
	// @return Returns converted time onto US format
	public static function getDateUS($dtime = null, $fulltime = false, $longFormat = false)
	{
		$pattern = $longFormat ? ($fulltime ? 'D, jS \of M Y g:ia' : 'D, jS \of M Y') : ($fulltime ? 'm/d/Y H:i' : 'm/d/Y');
		return self::getDate($pattern, $dtime);
	}
	
	// Convert a date into French/European format
	// @param $dtime A date, or the current datetime if null
	// @param $fulltime include time too or only the day ?
	// @param $longFormat Date in text format instead of only digits
	// @return Returns converted time onto French/European format
	public static function getDateFr($dtime = null, $fulltime = false, $longFormat = false)
	{
		$pattern = $longFormat ? ($fulltime ? 'D jS M Y à G:i' : 'D jS M Y') : ($fulltime ? 'd/m/Y H:i' : 'd/m/Y');
		return self::getDate($pattern, $dtime);
	}

	// Convert a date into SQL format
	// @param $dtime A date, or the current datetime if null
	// @param $fulltime include time too or only the day ?
	// @return Returns converted time onto SQL format
	public static function formatDateToSQL($dtime = null, $fulltime = false)
	{
		return self::getDate('Y-m-d'.($fulltime ? ' H:i:s': ''), $dtime);
	}
	
	
	// Convert a string date onto another string with the given pattern
	// @param $datetime String reprensenting a datetime to convert
	// @param $pattern Pattern to the new datetime format
	// @return Returns a string representing the date into another format
	public static function convertStringDate($datetime, $pattern)
	{
		return date($pattern, strtotime($datetime));
	}



	/**********************************************************************
	 *                       File and Path Methods                        *
	 **********************************************************************/
	
	// Finish the path with a slash : '/'
	// @param $path Path to a folder
	// @return Returns $path and add a terminating slash (/) is missing
	public static function normalizeFolder($path)
	{
		return ( $path && substr($path, -1) != '/' ) ? "$path/" : $path;
	}
	
	// Open a file and get its content
	// @param $filename Filepath to read
	// @return Returns the file content
	public static function getFileContent($filename)
	{
		$buffer = '';
		if( $fd = @fopen($filename, 'r') )
		{
			while(! @feof ($fd))
				$buffer .= @fgets($fd, 4096); 
			@fclose($fd);
		}
		else
			self::reportRunningWarning("Common::getFileContent() Unable to read the file '$filename' !");
		return $buffer;
	}
	
	private static function pathInfo($filename, $key)
	{
		if( $path_parts = pathinfo($filename) )
			return $path_parts[$key];
		else
			return '';
	}
	
	// Get the path
	// @param $filename Filename
	// @return From '/www/htdocs/index.html', returns : '/www/htdocs'
	public static function getDirname($filename)
	{
		return self::pathInfo($filename, 'dirname');
	}
	
	// Get the filename (include extension)
	// @param $filename Filename
	// @return From '/www/htdocs/index.html', returns : 'index.html'
	public static function getBasename($filename)
	{
		return self::pathInfo($filename, 'basename');
	}
	
	// Get the extension
	// @param $filename Filename
	// @return From '/www/htdocs/index.html', returns : 'html'
	public static function getExtention($filename)
	{
		return self::pathInfo($filename, 'extension');
	}
	
	// Get the filename without extension
	// @param $filename Filename
	// @return From '/www/htdocs/index.html', returns : 'index'
	public static function getFilename($filename)
	{
		return self::pathInfo($filename, 'filename');
	}
	
	// Recursively remove a folder and its content (files and sub-folders)
	// @param $path Folder to remove
	public static function removeDirectory($path)
	{
		$path = self::normalizeFolder($path);
		if( is_dir($path) && $dir = @opendir($path) )
		{
			while( ($file = readdir($dir)) !== false )
			{
				if( $file != '.' && $file != '..' )
				{
					if(filetype($path.$file) == 'dir')
						self::removeDirectory($path.$file);
					else
						@unlink($path.$file);
				}
			}
			closedir($dir);
			@rmdir($path);
		}
	}
	
	
	
	/**********************************************************************
	 *                       Retrieve the File path                       *
	 **********************************************************************/
	
	// This method is used in many files.
	// Try to find an existing file combining given parameters: paths, basenames and extensions.
	// Get to a folder, try all extensions and then go to the next folder.
	// @param $fileName relative file name
	// @param $arr_dir array containg folders high-sorted (can be a string imploded with $spacer)
	// @param $arr_ext array containg file extensions high-sorted (can be a string imploded with $spacer)
	// @param $spacer string used to explode list of folders or list of extensions
	// @return Returns the first correct path, null if no match found
	public static function getFilePath($fileName, $arr_dir, $arr_ext, $spacer = ';')
	{
		if( ! is_array($arr_dir) )
			$arr_dir = explode($spacer, $arr_dir);
		if( ! is_array($arr_ext) )
			$arr_ext = explode($spacer, $arr_ext);
		foreach($arr_dir as $dir)
		{
			if( @file_exists($f = $dir . $fileName) )
				return $f;
			foreach($arr_ext as $ext)
				if( @file_exists($f = $dir . $fileName . '.' . $ext) )
					return $f;
		}
		return null;
	}
	
	// returns the basebane and the associated path
	private static function splitPath_getPath($page) /* WARNING: maybe we can simplify this fonction as dots are probably not used anymore */
	{
		if( ! strstr($page, '.') )
			return Array($page, "$page/");
		return Array(str_replace('.', '/', $page), '');
	}
	
	// Get the JS path of the corresponding file. The 'bo_' prefix (if in admin context) has already been removed by the Context class
	// It uses Common::getFilePath()
	// @param $page Page to find
	// @return Returns null if not found or the correct path
	public static function getJSPath($page)
	{
		if( self::isAdminContext() && ! preg_match('/^bo_/', $page) )
		{
			list($file, $path) = self::splitPath_getPath("bo_$page");
			$js = self::getFilePath($file, Array(DIR_JS, DIR_PHP.$path), 'js;js.php');
		}
		if( ! $js )
		{
			list($file, $path) = self::splitPath_getPath($page);
			$js = self::getFilePath($file, Array(DIR_JS, DIR_PHP.$path), 'js;js.php');
		}
		return $js;
	}
	
	// Get the CSS path of the corresponding file. The 'bo_' prefix (if in admin context) has already been removed by the Context class
	// It uses Common::getFilePath()
	// @param $page Page to find
	// @return Returns null if not found or an Array('page' => page_path, 'data' => data_file_path)
	public static function getCSSPath($page)
	{
		if( self::isAdminContext() && ! preg_match('/^bo_/', $page) )
		{
			$page2 = "bo_$page";
			$css = self::getFilePath($page2, Array(DIR_CSS, DIR_PHP."$page2/"), 'css;css.php');
		}
		if( ! $css )
		{
			$css = self::getFilePath($page, Array(DIR_CSS, DIR_PHP."$page/"), 'css;css.php');
		}
		return $css;
	}
	

	
	/*********************************************************************
	 *                           Maths Methods                           *
	 *********************************************************************/
	
	// The value cannot be above the upper bound and cannot be below the lower bound
	// @param $value Value to wrap
	// @param $min Min value accepted
	// @param $max Max value accepted
	// @return Returns the $value wrapped within bounds 
	public static function minmax($value, $min, $max)
	{
		if( $value > $max) return $max;
		if( $value < $min) return $min;
		return $value;
	}
	
	// Returns the lowest value
	// @param $v1 First value to compare
	// @param $v2 Second value to compare
	// @return Returns the minimum value of both parameters
	public static function min($v1, $v2)
	{
		return ($v2 > $v1) ? $v1 : $v2;
	}
	
	// Returns the hightes value
	// @param $v1 First value to compare
	// @param $v2 Second value to compare
	// @return Returns the maximum value of both parameters
	public static function max($v1, $v2)
	{
		return ($v2 > $v1) ? $v2 : $v1;
	}
	
	// Convert an integer into a file size (eg: "10 Ko", "1.5 Mo")
	// @param $size Size to convert
	// @return Returns the corresponding string
	public static function toFileSize($size)
	{
		$size2 = $size/1024;
		if ($size2 > 500)
			return (intval($size2/102.4)/10)." Mo";
		elseif ($size)
			return (intval($size2))." Ko";
		else
			return NULL;
	}
	
	
	/*********************************************************************
	 *                           Other Methods                           *
	 *********************************************************************/
	
	// Loops on args and return the first not null value.
	// Null means: empty string, false, null, 0
	// @param $args list of values to loop-on
	// @return Returns the first not null value, null otherwise
	public static function notNull()
	{
		foreach(func_get_args() as $key)
			if( $key || is_array($key) || $is_object($key) )
				return $key;
		return null;
	}
	
	// Send an email using HTML encodage type and ISO charset (Mime format activated)
	// @param $to Receiver
	// @param $title Email title
	// @param $subject Email content (in HTML format)
	// @param $from Sender
	// @return If a mail server is running returns the mail status, default mail status value otherwise (defined in the init)
	public static function mail($to, $title, $subject, $from = null)
	{
		$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=".ENCTYPE_CHARSET."\r\n";
		if( $from )
			$headers .= "From: $from\r\n";
		if( SETTINGS_IS_MAIL_SERVER_RUNNING )
			return @email($to, $subject, $message, $headers);
		else
			return self::$defaultMailStatusResult;
	}
	
	// Generate the 404 error headers and terminate the rendering
	public static function error404()
	{
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		exit();
	}
	
	
	// Convert an SQL row to have easier acces to keys and values.
	// $sqlRow->type will become: $sqlRow['type']
	// @param $sqlRow An SQL row
	// @return Returns the transformed SQL row
	public static function assocSQLRow($sqlRow)
	{
		$res = Array();
		foreach($sqlRow as $key => $val)
			$res[$key] = $val;
		return $res;
	}
	
	// Convert SQL rows to have easier acces to keys and values.
	// $row->type will become: $row['type']
	// @param $sqlRows An array of SQL rows
	// @return Returns the transformed SQL rows array
	public static function assocSQLRows($sqlRows)
	{
		$res = Array();
		foreach($sqlRows as $key => $val)
			$res[$key] = self::assocSQLRow($val);
		return $res;
	}
	
};
