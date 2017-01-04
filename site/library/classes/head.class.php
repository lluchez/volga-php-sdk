<?php

/******************************
 *         Head Class         *
 ******************************/

/**
 * @desc-start
 * This class create the <body /> and <head /> tags and also includes CSS and Javascript tags for your website. _
 * It doesn't need to include or init any other class.
 *
 * The medias (CSS and JS) are stored into a private static array. _
 * You can add medias before the meta tags are generated. _
 * The best place to add them is in the "{page_name}.conf.php" files related to a page.
 * The toString() function is called inside the <head> tag to easily add the _
 * include-medias tags to your Web pages.
 * This class also handle the page name: the Website title is given at class initialization _
 * and then you can add the current page name to generate a nice title.
 * @desc-end
 *
 * Here is the list of static functions:
 *    - Head::init($site_title, $defaultCssRenderingMode, $defaultJsSupportLanguage)
 *    - Head::setTitle($page_title)
 *    - Head::setFullTitle($page_fullTitle)
 *    - Head::getTitle()
 *
 *    - Head::addMetaData($key, $value)
 *    - Head::removeMetaData($key)
 *    - Head::setFavIcon($image = null)
 *
 *    - Head::activateErrorPanel($state = true, $id = null)
 *    - Head::generateErrorPanel($padding = '')
 *
 *    - Head::setLinkedCSSProperties($params, [$renderingMode])
 *    - Head::setLinkedJSProperties($params, [$useCurrentLanguage])
 *
 *    - Head::addCSS([list of files])
 *    - Head::addCSSForMedia($media, [list of files])
 *    - Head::addDefaultCSS($page, $dirs)
 *    - Head::addJS([list of files])
 *    - Head::addDefaultJS($page, $dirs)
 *    - Head::toString()
 */


class Head
{
	// array to store the CSS and JS to load
	private static $myCSS = Array();
	private static $myJS = Array();
	
	// default parameter added when including the default JS/CSS file
	// update these parameters only in the 'xxx.conf.php' file
	private static $defaultCssRenderingMode = null, $defaultJsSupportLanguage = null;
	private static $linkedCSS_DefaultParams = null, $linkedCSS_DefaultRendering = null;
	private static $linkedJS_DefaultParams  = null, $linkedJS_DefaultUseCurrentLanguage = null;
	
	// title to be displayed
	private static $pageTitle = null, $siteTitle = null;
	
	private static $meta_data = Array();
	private static $favicon = null;
	
	private static $alreadyGenerated = false;
	private static $errorPanelID = null, $errorPanelActivated = false;
	
	
	
	/******************************************
	 *             title functions            *
	 ******************************************/
	
	// Initilisation function of this class. To be called before calling any function
	// @param $site_title Title of this Web Site. This parameter is used to generate the name within the <title/> tag
	// @param $defaultCssRenderingMode Default CSS rendering mode: 'screen', 'all, 'print', etc.
	// @param $defaultJsSupportLanguage Default JS feature: add language parameter (localized JS)
	public static function init($site_title, $defaultCssRenderingMode, $defaultJsSupportLanguage, $errorPanelID)
	{
		self::$siteTitle = $site_title;
		self::$errorPanelID = $errorPanelID;
		self::$defaultCssRenderingMode  = self::$linkedCSS_DefaultRendering = $defaultCssRenderingMode;
		self::$defaultJsSupportLanguage = self::$linkedJS_DefaultUseCurrentLanguage = $defaultJsSupportLanguage;
	}
	
	// Set the page title. It's not the same parameter as the one given to the init() function. _
	// Both are joined to generate the final page title.
	// Best place to call this function is within the "{page_name}.conf.php" files
	// @param $page_title Title of the current page. This parameter is used to generate the name within the <title/> tag.
	public static function setTitle($page_title)
	{
		self::$pageTitle = $page_title;
	}
	
	// Set the page title. Similiar to Head::setTitle() but the site title is ignored and only using this parameter
	// Best place to call this function is within the "{page_name}.conf.php" files
	// @param $page_title Title of the current page. This parameter represents the name within the <title/> tag.
	public static function setFullTitle($page_fullTitle)
	{
		self::$siteTitle = null;
		self::$pageTitle = $page_fullTitle;
	}
	
	// Get the title of the page
	// @return Returns the title of the current page
	public static function getTitle()
	{
		return self::$siteTitle . (is_null(self::$siteTitle) || is_null(self::$pageTitle) ? '' : ' - ') . self::$pageTitle;
	}
	
	
	/*****************************************
	 *     meta-data and favicon function    *
	 *****************************************/
	
	// Add data to the <head /> section
	// Generate this: <meta name="$key" content="$value" />
	// @param $name attribute `name`
	// @param $name attribute `content`
	public static function addMetaData($name, $content)
	{
		self::$meta_data[$name] = $content;
	}
	
	// Remove a meta-data previously added
	// @param $name attribute `name`
	private static function removeMetaData($name)
	{
		unset(self::$meta_data[$name]);
	}
	
	// Create the Meta <meta /> tag
	private static function loadMetaData($key, $value, $left_padding)
	{
		echo "$left_padding<meta name=\"$key\" content=\"$value\" />\n";
	}
	
	// Set the favorite icon of the page. 
	// WARNING: Can be used only once !!! Other calls will be ignored.
	// @param $image link created with Media::IMAGE() or Lang::IMAGE(). If null no favicon.
	public static function setFavIcon($image = null)
	{
		if( ! self::$favicon )
			self::$favicon = $image;
	}
	
	
	// Create the Favicon tags
	private static function loadFavIcon($left_padding)
	{
		if( self::$favicon )
			echo "$left_padding<link rel=\"icon\" href=\"".self::$favicon."\" type=\"image/x-icon\">\n"
					."$left_padding<link rel=\"shortcut icon\" href=\"".self::$favicon."\" type=\"image/x-icon\">\n";
	}
	
	
	
	/*****************************************
	 *              Error panel              *
	 *****************************************/
	
	// Create the errorPanel
	// @param $state if true, the error panel will be created
	// @param $id If not null will be the new ID
	public static function activateErrorPanel($state = true, $id = null)
	{
		if( $id && is_string($id) )
			self::$errorPanelID = $id;
		self::$errorPanelActivated = $state;
	}
	
	// Create or not the Error Panel. Function called by pages_xxx/header.inc.php
	// @param $padding left padding for indentation
	public static function generateErrorPanel($padding = '')
	{
		if( self::$errorPanelActivated )
			echo "$padding<div id=\"".self::$errorPanelID."\"></div>\n";
	}
	
	/******************************************
	 *      default parameters functions      *
	 ******************************************/
	
	// Change the linked CSS properties
	// [code]
	// // current page: 'home'
	// Head::setLinkedCSSProperties(Array('id'=>5), 'print');
	// // make this tag: <link type="text/css" rel="stylesheet" href="/xxxx/home.css?id=5" media="print" />
	// [/code]
	// Advice: use this function in {page_name}.conf.php
	// @param $params If not null change parameters added to the call of the same-named CSS file
	// @param $renderingMode If not null change CSS rendering mode: 'screen', 'all, 'print', etc.
	public static function setLinkedCSSProperties($params = null, $renderingMode = null)
	{
		if( ! is_null($params) )
			self::$linkedCSS_DefaultParams = $params;
		if( ! is_null($renderingMode) )
			self::$linkedCSS_DefaultRendering = $renderingMode;
	}
	
	// Change the linked Javascript properties
	// [code]
	// // current page: 'home'
	// Head::setLinkedJSProperties(Array('id'=>5), true);
	// // make this tag: <script type="text/javascript" language="javascript" src="/xxx/en/javascript/home.js?id=5"></script>
	// [/code]
	// Advice: use this function in {page_name}.conf.php
	// @param $params If not null change parameters added to the call of the same-named JS file
	// @param $useCurrentLanguage If not null change JS support of language
	public static function setLinkedJSProperties($params = null, $useCurrentLanguage = null)
	{
		if( ! is_null($params) )
			self::$linkedJS_DefaultParams = $params;
		if( ! is_null($useCurrentLanguage) )
			self::$linkedJS_DefaultUseCurrentLanguage = $useCurrentLanguage;
	}

	
	
	/******************************************
	 *              CSS functions             *
	 ******************************************/
	
	// Add CSS into the internal array with the default rendering mode (the one specified at Head::init())
	// [code]
	// Head::addCSS('my_css|param1=val1|param2=val2', 'ie_display');
	// Head::addCSS(Array('my_css', Array('param1' => 'val1', 'param2' => 'val2')));
	// [/code]
	// You can add as much parameters as you want, loop on them
	// @param $args list of list strings or arrays
	public static function addCSS()
	{
		foreach(func_get_args() as $key)
			if( $key )
			{
				if( is_array($key) )
					$key = Media::linkWithParameters($key[0], $key[1]);
				self::$myCSS[] = Array($key, self::$defaultCssRenderingMode);
			}
	}
	
	// Add CSS into the internal array
	// [code]
	// Head::addCSSForMedia('print', 'my_css|param1=val1|param2=val2', 'ie_display');
	// Head::addCSSForMedia('print', Array('my_css', Array('param1' => 'val1', 'param2' => 'val2')));
	// [/code]
	// @param $rendering_mode 'media' property of the CSS tag <link type="text/css" rel="stylesheet" />
	// @param $next_args list of list strings or arrays
	public static function addCSSForMedia()
	{
		$files = func_get_args();
		$media = array_shift($files);
		if( is_string($media) )
		{
			foreach($files as $key)
				if( $key )
				{
					if( is_array($key) )
						$key = Media::linkWithParameters($key[0], $key[1]);
					self::$myCSS[] = Array($key, $media);
				}
		}
		else
			Common::reportRunningWarning("Head::addCSSForMedia() Unable to find the media for output !");
	}
	
	// Try to add the default CSS file (the one contained in the same folder: xxx.css(.php)? ) for inclusion into the <head /> tag
	// @param $page Current page code
	// @param $dirs Array containing directories to search in
	public static function addDefaultCSS($page, $dirs)
	{
		if( Common::getFilePath($page, $dirs, 'css;css.php') )
			self::$myCSS[] = Array(Media::linkWithParameters($page, self::$linkedCSS_DefaultParams), self::$linkedCSS_DefaultRendering);
	}
	
	// Create the CSS <link /> tag
	private static function loadCSS($file, $left_padding)
	{
		echo "$left_padding<link type=\"text/css\" rel=\"stylesheet\" href=\""
				. Media::CSS($file[0])."\"".($file[1] ? " media=\"$file[1]\"" : "")." />\n";
	}
	
	
	/******************************************
	 *              JS functions              *
	 ******************************************/
	
	// Add JavScript into the internal array with the default behavior related to languages feature (the one specified at Head::init())
	// [code]
	// Head::addJS('script|param1=val1|param2=val2', 'events');
	// Head::addJS(Array('script', Array('param1' => 'val1', 'param2' => 'val2')));
	// [/code]
	// You can add as much parameters as you want, loop on them
	// If language support is activated, this function will behave as Head::addTranslatedJS()
	// @param $args list of list strings or arrays
	public static function addJS()
	{
		foreach(func_get_args() as $key)
			if( $key )
			{
				if( is_array($key) )
					$key = Media::linkWithParameters($key[0], $key[1]);
				self::$myJS[] = Array($key, self::$defaultJsSupportLanguage);
			}
	}
	
	// Add JavScript into the internal array with languages feature activated
	// [code]
	// Head::addTranslatedJS('script|param1=val1|param2=val2', 'events');
	// Head::addTranslatedJS(Array('script', Array('param1' => 'val1', 'param2' => 'val2')));
	// [/code]
	// You can add as much parameters as you want, loop on them
	// @param $args list of list strings or arrays
	public static function addTranslatedJS()
	{
		foreach(func_get_args() as $key)
			if( $key )
			{
				if( is_array($key) )
					$key = Media::linkWithParameters($key[0], $key[1]);
				self::$myJS[] = Array($key, true);
			}
	}
	
	// Try to add the default JavaScript file (the one contained in the same folder: xxx.js(.php)? ) for inclusion into the <head /> tag
	// @param $page Current page code
	// @param $dirs Array containing directories to search in
	public static function addDefaultJS($page, $dirs)
	{
		if( Common::getFilePath($page, $dirs, 'js;js.php') )
			self::$myJS[] = Array(Media::linkWithParameters($page, self::$linkedJS_DefaultParams), self::$linkedJS_DefaultUseCurrentLanguage);
	}
	
	// create the JS <script> tag
	private static function loadJS($file, $left_padding)
	{
		echo "$left_padding<script type=\"text/javascript\" language=\"javascript\" src=\""
				. ($file[1] ? Lang::JS($file[0]) : Media::JS($file[0]) )
				. "\"></script>\n";
	}
	
	
	/******************************************
	 *            toString function           *
	 ******************************************/
	
	// Creates all tags: <html />, <head />, <meta />, <script />, <link />, etc.
	// It also closes the </head> tag and creates the begining of the <body />
	public static function toString()
	{
		if( ! self::$alreadyGenerated )
		{
			$class = BASE_NAME.' page_'.Context::getPageCode();
			if( SETTINGS_MULTI_LINGUAGES )
				$class .= ' lang_'.Lang::getCurrentLanguage();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo self::getTitle(); ?></title>
<?php
			$padding = "\t\t";
			foreach(self::$meta_data as $key => $value)
				self::loadMetaData($key, $value, $padding);
?>
		<meta http-equiv="REFRESH" content="n" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ENCTYPE_CHARSET; ?>" />
<?php
			self::loadFavIcon($padding);
			foreach(self::$myCSS as $key)
				self::loadCSS($key, $padding);
			foreach(self::$myJS as $key)
				self::loadJS($key, $padding);
			self::$alreadyGenerated = true;
?>
	</head>

	<body class="<?php echo $class; ?>">
		<a id="top" name="top"></a>

<?php
		}
	}
	
	
}
?>