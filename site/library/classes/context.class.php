<?php

interface IContextDAL
{
	public static function determineNameFromCode($code);
	public static function determineCodeFromName($name);
	public static function changeLanguageLink($code, $lang);
};

/** ------------ End Of Class ------------ */


/******************************
 *        Class Context       *
 ******************************/

/**
 * @desc-start
 * This class has many features related to the end-user context/session.
 * Here you can retrieve the current page name or code, convert page name to page code (and vice versa).
 * It also performs the inclusion of JS/CSS/Images/Attachments and handles auto-caching them or not (cache can be forced for PHP files)
 * You can also handle the viewing type (normnal page/popup/ajax-call) to include or not <meta /> and <body /> tags.
 * @desc-end
 *
 * Here is the list of functions:
 *    - Context::init($default_page, $pages_table_array, $sql_connecions, $sql_pages, $session_timeout)
 *
 *	  - Context::getPageCode()
 *	  - Context::getPageName()
 *
 *		- Context::HTML([$page, [$lang, [$params_keys, [$params_vals]]]])
 *		- Context::forward([$params, [$page, [$lang]]])
 *
 *    - Context::lastModificationDate([$file])
 *    - Context::loadInternalMedia($file, $content_type, [$data_file])
 *    - Context::loadExternalMedia($page, $folder, $content_type, $extensions)
 *    - Context::disableCache([$state])
 *
 *    - Context::updateViewingMode($viewingMode, [$toggleOn])
 *    - Context::isPopupWindow()
 *    - Context::isAjaxCall()
 *    - Context::isCasualPage()
 * 
 *    - Context::rememberPageHasBeenVisited($page, $type)
 */

 

class Context extends Page
{
	const AJAX_CALL = 1; // Viewing mode updated to AJAX_CALL
	const POPUP 		= 2; // Viewing mode updated to POPUP

	// Name of the current context
	private static $currentPageName = null, $currentPageCode = null;
	
	// SQL table name used to store each connection
	private static $sql_table_connections = null;
	
	// SQL table name used to store each page viewed
	private static $sql_table_pagesViewed = null;
	
	// Time (in hours) where the session is supposed to be keept open (no more SQL insert in the connction table)
	private static $session_timeout = 4;
	
	// if the media targeted is a PHP file we can try to activate cache for it
	private static $activateCache = true;
	
	// mode page viewing (only for HTML): popup mode or Ajax call
	private static $viewingMode = 0;

	
/*****************
 * Init function *
 *****************/
	
	// Initilisation function of this class. To be called before calling any function
	// @param $sql_connecions SQL table name to store connection rows
	// @param $sql_pages SQL table name to store pages viewed
	// @param $session_timeout Time where the connection is considered as open: no new connection saved into DB within this time (in hours)
	public static function init($sql_connecions, $sql_pages, $session_timeout)
	{
		self::$sql_table_connections = $sql_connecions;
		self::$sql_table_pagesViewed = $sql_pages;
		self::$session_timeout = intval($session_timeout); // time in hours
		
		self::determineWhetherNameOrCode();
		
		// protection from <page name to include> spoiling
		$code = self::getPageCode();
		$isHTML = self::isHTML() || self::isAJAX();
		if( ($isHTML && ! preg_match("#^[a-z0-1_]+$#", $code)) || (! $isHTML && preg_match("#(\.\.)|\\\#", $code)) )
			Common::reportFatalError("Incorrect file to include !");
		
		//echo self::$currentPageName."<br />\n";
		//echo self::$currentPageCode."<br />\n";
	}
	
	
	private static function determineWhetherNameOrCode()
	{
		$nameOrCode = self::$currentPageNameOrCode;
		if( preg_match('/^ISO-/', ENCTYPE_CHARSET) )
			$nameOrCode = utf8_decode($nameOrCode);
		if( ! (self::isHTML() || Page::isAJAX()) ) // Type not HTML/AJAX
		{
			self::$currentPageName = $nameOrCode;
			self::$currentPageCode = $nameOrCode;
		}
		elseif( preg_match("#([\x20-\x5E])+#", $nameOrCode) ) // contains special chars
		{
			self::$currentPageName = $nameOrCode;
			self::$currentPageCode = ContextDAL::determineCodeFromName($nameOrCode); // can be null...
		}
		elseif( $res = ContextDAL::determineCodeFromName($nameOrCode) ) // Try to find from Name
		{
			self::$currentPageName = $nameOrCode;
			self::$currentPageCode = $res;
		}
		else // Find from Code
		{
			self::$currentPageName = Common::notNull(ContextDAL::determineNameFromCode($nameOrCode), $nameOrCode);
			self::$currentPageCode = $nameOrCode;
		}
	}



/***************************
 * Get page/type functions *
 ***************************/
	
	// @return Returns the page code of the current page viewed
	public static function getPageCode()
	{
		return self::$currentPageCode;
	}
	
	// @return Returns the page name of the current page viewed, if page name not found return the page code
	public static function getPageName()
	{
		return Common::notNull(self::$currentPageName, self::$currentPageCode);
	}



/********************************
 * HTML/AJAX : link to Web Page *
 ********************************/
	
	// Create an HTML link
	// @param $page Page code for the link (current page if null)
	// @param $lang Include language code for the target link if not false
	// @param $params Parameters sent (array or string), cf Media::linkWithParameters()
	// @return the corresponding pretty path/link
	public static function HTML($page = null, $lang = true, $params = null)
	{
		$page = ( is_null($page) ) ? self::getPageName() : ContextDAL::determineNameFromCode($page);
		if( $lang && SETTINGS_MULTI_LINGUAGES )
			return Lang::HTML($page, $params);
		else
			return Media::HTML($page, $params);
	}
	
	// Create an AJAX link
	// @param $page Page code for the link (current page if null)
	// @param $lang Include language code for the target link if not false
	// @param $params Parameters sent (array or string), cf Media::linkWithParameters()
	// @return the corresponding pretty path/link
	public static function AJAX($page = null, $lang = true, $params = null)
	{
		$page = ( is_null($page) ) ? self::getPageName() : ContextDAL::determineNameFromCode($page);
		if( $lang && SETTINGS_MULTI_LINGUAGES )
			return Lang::AJAX($page, $params);
		else
			return Media::AJAX($page, $params);
	}


/***************************
 * HTML : link to Web Page *
 ***************************/
 
	// Redirect to another page and clear data containing in the $_POST array. Use Context::HTML()
	// @param $params Parameters sent (array or string), cf Media::linkWithParameters()
	// @param $page Page code for the link (current page if null)
	// @param $lang Include language code for the target link if not false
	public static function forward($params = null, $page = null, $lang = true)
	{
		Vars::clearPost();
		header("Location: ".self::HTML($page, $lang, $params));
		die();
	}



/***************************
 *    header functions     *
 ***************************/
	
	// Update the last modification date and set the cache-mode as disabled if file is null or dynamic (PHP)
	// You can use Context::disableCache() to disble cache on the current page
	// @param $file File to be included
	// @param $content $content of the file (use to check if already cached)
	// @return Nothing, but terminates if page status is 304 (Not Modified)
	private static function lastModificationDate($file = null, $content = null)
	{
		$last_modified_time = $file ? filemtime($file) : mktime();
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		if( is_null($file) || (Common::getExtention($file) == 'php' && !self::$activateCache) || Client::isOldMSIE() )
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
		}
		else
		{
			header("Expires: " . gmdate("D, d M Y H:i:s", filemtime($file) + 3600) . " GMT");
			header("Cache-Control: public");
			
			// Additional stuff: force the cache to be activated !!!!
			$etag = md5($content);
			if( @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time || trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag )
			{
				header("HTTP/1.1 304 Not Modified"); // File cached !!! No need to re-sent it !
				exit;
			}
		}
	}
	
	// include the media content and transform it with the callback function
	private static function getMediaContent($file, $callback = null)
	{
		$gzip_enabled = SETTINGS_ENABLE_GZIP_MODE && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && extension_loaded('zlib');
		ob_start();
		if( preg_match('#(.*\.)(js|css|png|gif|jpeg|jpg)\.php$#', $file, $matches) )
		{
			if( file_exists($data = $matches[1].'data.php') )
				include $data;
		}
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		if( $callback )
			$content = @call_user_func($callback, $content);
		self::lastModificationDate($file, $content); // needs to be call here has we can force the cache to be turned on
		
		if( $gzip_enabled )
		{
			ob_start("ob_gzhandler");
			echo $content;
			ob_end_flush();
		}
		else
		{
			header('Content-length: ' . strlen($content));
			echo $content;
		}
	}
	
	// Generate the page content for CSS and JS files
	// Try to compress the media if the browser supports GZIP
	// @param $file CSS/JS file
	// @param $data_file 'xxxx.data.php' file used to pre-compute data used by the HTML/CSS/Js files
	// @return Returns if the file exists
	public static function loadInternalMedia($file, $data_file = null)
	{
		if( $file )
		{
			if( @file_exists($data_file) ) // include shared data
				include $data_file;
			$comment = "/**\n * @context " . SITE_TITLE . "\n * @date #\n */\n\n";
			$date = Common::formatDateToSQL(Common::getExtention($file) == 'php' ? null : filemtime($file));
			$fct = create_function('$content', 'return str_replace(\'#\', \''.$date.'\', "'.$comment.'").$content;');
			self::getMediaContent($file, $fct);
			return true;
		}
		return false;
	}

	// Generate the page content for images and attachments
	// Try to compress the media if the browser supports GZIP
	// @param $page corresponding file
	// @param $folder default folder to search in (supports localized sub-folder: 'fr/','en/')
	// @param $extensions list of supported extensions of the image/file in lower case
	// @return Returns if the file exists
	public static function loadExternalMedia($page, $folder, $extensions)
	{
		// possible folders
		$folders = Array();
		if( SETTINGS_MULTI_LINGUAGES && Lang::isLanguageSet() )
			$folders[] = $folder.Lang::getCurrentLanguage().'/';
		$folders[] = $folder;
		// decode the path
		$parts = explode('.', $page);
		$ext = array_pop($parts);
		$page = implode('/', $parts);
		if( $file = Common::getFilePath($page, $folders, $extensions) )
		{
			$mime_type = Media::getMimeType($ext);
			$disposition = preg_match('#^(image|text)\/#', $mime_type) ? 'inline' : 'attachment';
			header('Content-Disposition: {'.$disposition.'; filename="' . Common::getBasename($file) . '"');
			header('Content-type: '.$mime_type);
			self::getMediaContent($file);
			return true;
		}
		return false;
	}
	
	// If the media targeted is a dynamic file linking to cache problem, we can disable it
	// Note: this command should be used when random used or dynamic content is linked to context _
	//  (pages viewed earlier, not linked to parameters given)
	// @param $state If true disable the cache for this file (only for dynamic files, as cache is activated by default)
	public static function disableCache($state = true)
	{
		self::$activateCache = !$state;
	}
	
	
/****************************
 *   page modes functions   *
 ****************************/
	
	// Define the type of page: 'popup' window, Ajax call or normal view.
	// if AJAX_CALL: won't add any HTML content: <head /> and <body /> tags (no meta, no header, no footer)
	// if POPUP: won't add the header and footer content
	// @param $viewingMode Combinaison of Context::AJAX_CALL and Context::POPUP
	// @param $toggleOn If true toggle on the mode sent by $viewingMode
	public static function updateViewingMode($viewingMode, $toggleOn = false)
	{
		if( ! is_int($viewingMode) )
			return;
		$viewingMode = $toggleOn ? ($viewingMode | self::$viewingMode) : $viewingMode;
		if( $viewingMode & self::AJAX_CALL )
			$viewingMode = self::AJAX_CALL | self::POPUP;
		self::$viewingMode = $viewingMode;
	}
	
	// Is this page in popup mode ?
	// @return Returns true if popup mode (so won't add header/footer content)
	public static function isPopupWindow()
	{
		return (bool)(self::$viewingMode & self::POPUP);
	}
	
	// Is this page an Ajax call ?
	// @return Returns true if Ajax call (so won't add head/header/footer content)
	public static function isAjaxCall()
	{
		return (bool)(self::$viewingMode & self::AJAX_CALL);
	}
	
	// Is this page in normal view ?
	// @return Returns true if normal page (head/header/footer content will be added)
	public static function isCasualPage()
	{
		return (bool)(self::$viewingMode == 0);
	}



/****************************
 *  pages viewed functions  *
 ****************************/
	
	// insert a row in the Connection table
	private static function trackConnection()
	{
		$localName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if( $robot_ereg = DataDAL::getDataRow('regexp_isRobot') ) // "(\.googlebot\.|msnbot\-|\.crawl\.yahoo\.|\.exabot\.|msnbot\-)"
		{
			if( preg_match('/'.$robot_ereg->value.'/', $localName) ) 
				return 0; // disable it for search engine bots
		}
		else
			Common::reportRunningWarning("[Class Context] Unable to retrieve the bots/crawlers list");
		
		$properties = Array
		(
			null, // id
			Common::formatDateToSQL(null, true), // date connection
			Common::formatDateToSQL(null, true), // data last page viewed
			$_SERVER['REMOTE_ADDR'], // ip address
			$localName, // host local name
			Client::getReferer(), // referer address
			Client::getBrowserName(), // browser
			Client::getBrowserVersion(true), // browser version
			Client::isMSIE() ? (Client::isOldMSIE() ? 'yes' : 'no') : null, // is old IE (ver<=6.xx)
			Client::getOS(), // OS of the client system
			Lang::getCurrentLanguage(), 1 // language and # pages viewed
		);
		if( DB::insert(self::$sql_table_connections, $properties) )
			return DB::getLastInsertedId();
		return 0;
	}
	
	private static function getConnectionID()
	{
		if( $id = Client::getSessionID() )
			return $id;
		$sql_where = " WHERE ADDDATE(`login_date`, INTERVAL ".self::$session_timeout." HOUR) >= NOW() "
				. " AND `ip_address`='".String::safeSQL($_SERVER['REMOTE_ADDR'])."'"
				. " AND `hostname`='".String::safeSQL(gethostbyaddr($_SERVER['REMOTE_ADDR']))."'";
		if( $browser = Client::getBrowserName() )
			$sql_where .= " AND `browser`='".String::safeSQL($browser)."'";
		else
			$sql_where .= " AND `browser` IS NULL";
		$sql_where .= " ORDER BY `login_date` DESC";
		if($row = DB::getOneRow("SELECT `session_id` as `id` FROM `".self::$sql_table_connections."` $sql_where"))
			return $row->id;
		return 0;
	}
	
	// Add a record in the DB about a page visited.
	// This feature has to be activated in 'conf/site.conf.php'.
	// Only applicable for HTML or Attachement page.
	public static function rememberPageHasBeenVisited()
	{
		if( SETTINGS_LOG_VISITS )
		{
			$page = self::getPageCode();
			$type = Page::getContentType();
			if( $type == Page::KEY_HTML || $type == Page::KEY_DATA )
			{
				if( $id = self::getConnectionID() )
				{
					DB::update("UPDATE `".self::$sql_table_connections."` SET `logout_date`='"
									.Common::formatDateToSQL(null, true)."', `pages_viewed`=`pages_viewed`+1 WHERE `session_id`=$id");
				}
				elseif( $id = self::trackConnection() )
				{
					Client::setSessionID($id);
				}
				
				// track the pages viewed
				if( $id && self::$sql_table_pagesViewed && self::isCasualPage() && ! Vars::isPostContext() )
				{
					DB::insert(self::$sql_table_pagesViewed, Array
						(
							$page, $type, // page name and type
							$_SERVER['REQUEST_URI'], // request string, or try $_SERVER['QUERY_STRING']
							$id, Common::formatDateToSQL(null, false) // session_id and current date
						) );
				}
			}
		}
	}

};

/**
CREATE TABLE IF NOT EXISTS `connection` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login_date` datetime NOT NULL,
  `logout_date` datetime NOT NULL,
  `ip_address` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `hostname` varchar(80) COLLATE latin1_general_ci NOT NULL,
  `referer` text COLLATE latin1_general_ci,
  `browser` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `browserVersion` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
  `oldIE` enum('yes','no') COLLATE latin1_general_ci DEFAULT NULL,
  `system_os` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `language` varchar(3) COLLATE latin1_general_ci DEFAULT NULL,
  `pages_viewed` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`session_id`),
  KEY `navigateur` (`browser`),
  KEY `system_os` (`system_os`),
  KEY `oldIE` (`oldIE`),
  KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1;
*/
?>