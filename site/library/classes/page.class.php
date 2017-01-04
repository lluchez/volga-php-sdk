<?php

/******************************
 *         Page Class         *
 ******************************/

/**
 * @desc-start
 * This class searches throught $_GET vars to find the type of the page to display and which files to include
 * @desc-end
 * 
 *
 * Here is the list of static functions:
 *    - Page::init($default_page, $charset)
 *
 *    - Page::getPageCodeOrName()
 *    - Page::getContentType()
 */

class Page
{
	const KEY_HTML  = 0;
	const KEY_JS    = 1;
	const KEY_CSS   = 2;
	const KEY_IMAGE = 3;
	const KEY_DATA  = 4;
	const KEY_AJAX  = 5;
	const COUNT_KEY = 6;

	// Default page name to call (only used when 'content type = web_page')
	protected static $defaultWebPage = null;
	
	// Name of the current context
	protected static $currentPageNameOrCode = null;
	// Type of the current context 
	protected static $currentContentType = null;


	// Initilisation function of this class. To be called before calling any function
	// @param $types array containing url names for pages to call
	// @param $default_page define the default pages to be redirected on it when location param is not defined (the Home page)
	public static function init($types, $default_page)
	{
		if( ! is_array($types) || count($types) != self::COUNT_KEY )
			Common::reportFatalError("[Class Page] The firts parame should be an array containing ".self::COUNT_KEY." elements !");
			
		if( is_null($default_page) )
			Common::reportFatalError("[Class Page] The default page name could not be null !");
		
		self::$defaultWebPage = $default_page;
		
		// create a temp array for Content-Type attribute
		$content_types = Array
		(
			self::KEY_HTML  => 'text/html',
			self::KEY_JS    => 'text/javascript',
			self::KEY_CSS   => 'text/css',
			self::KEY_AJAX  => 'text/html'
		);
		
		
		$content_type = self::KEY_HTML;
		$pageCodeOrName = self::$defaultWebPage;
		foreach($types as $typeIdx => $url_type )
		{
			if( Vars::defined($url_type) )
			{
				$content_type = $typeIdx;
				$pageCodeOrName = Vars::get($url_type);
				Vars::remove($url_type);
				break;
			}
		}
		
		self::setContentType($content_type);
		self::setPageCodeOrName($pageCodeOrName);
		if( $cType = $content_types[$content_type] )
			self::setContentTypeHeader($cType);
	}
	
	// Set the Content-Type value with header() function
	// @param $cType Content-Type value. Example: 'text/html', 'text/css', 'image/png', etc...
	public static function setContentTypeHeader($cType)
	{
		header("Content-Type: $cType; charset=".ENCTYPE_CHARSET);
	}
	
	// @return Returns true if the page viewed is an HTML page
	public static function isHTML()
	{
		return (self::$currentContentType == self::KEY_HTML);
	}
	
	// @return Returns true if the page viewed is a JS page
	public static function isJavascript()
	{
		return (self::$currentContentType == self::KEY_JS);
	}
	
	// @return Returns true if the page viewed is a CSS page
	public static function isCSS()
	{
		return (self::$currentContentType == self::KEY_CSS);
	}
	
	// @return Returns true if the page viewed is an Image
	public static function isImage()
	{
		return (self::$currentContentType == self::KEY_IMAGE);
	}
	
	// @return Returns true if the page viewed is a Data/attachment page
	public static function isAttachment()
	{
		return (self::$currentContentType == self::KEY_DATA);
	}
	
	// @return Returns true if the page viewed is an Ajax call
	public static function isAJAX()
	{
		return (self::$currentContentType == self::KEY_AJAX);
	}
	
	
	// Do nothing if not an HTML page, echo() the text otherwise
	// @param $text Message to display if the page viewed is a Web page (not CSS/JS/img...)
	public static function echoHTML($text, $addNewLine = false)
	{
		if( ! self::isHTML() ) return;
		echo $text;
		if( $addNewLine ) echo "<br />\n";
	}

	// Do nothing not an HTML page, Common::print_r() the array otherwise
	// @param $array Array to display if the page viewed is a Web page (not CSS/JS/img...)
	public static function print_rHTML($array)
	{
		if( ! self::isHTML() ) return;
		Common::print_r($text);
	}

/***************************
 * Get page/type functions *
 ***************************/
	
	// @return Returns the page code of the current page viewed
	public static function getPageCodeOrName()
	{
		return self::$currentPageNameOrCode;
	}
	
	// @return Returns the page type of the current page viewed (WEB_PAGE, JAVASCRIPT, CSS, etc.)
	public static function getContentType()
	{
		return self::$currentContentType;
	}

	

/***************************
 * Set page/type functions *
 ***************************/
 
	private static function setPageCodeOrName($page)
	{
		$cType = self::getContentType();
		if( Common::isAdminContext() && preg_match("#^bo_#", $page) && ($cType == self::KEY_JS || $cType == self::KEY_CSS) )
			$page = preg_replace("#^bo_#", "", $page);
		self::$currentPageNameOrCode = $page;
	}
	
	private static function setContentType($content_type)
	{
		self::$currentContentType = $content_type;
	}
};

?>