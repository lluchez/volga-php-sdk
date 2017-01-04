<?php

include DIR_CLASSES . 'browser.class.php';

/******************************
 *        Class Client        *
 ******************************/

/**
 * @desc-start
 * This class handle users' configuration and retrieves end-user side information.
 * This class is a static class. Hence no instance is needed and you can _
 *   directly calls functions by prefixing them with 'Client::'
 * @desc-end
 *
 * Here is the list of functions:
 *    - Client::init($key)
 *
 *    - Client::isFirstPageViewed()
 *    - Client::isMSIE()
 *    - Client::isOldMSIE()
 *    - Client::isOldBrowser()
 *    - Client::isCompliantWithCSS3()
 *
 *    - Client::getInfo()
 *    - Client::getBrowserName()
 *    - Client::getBrowserVersion([$short_version])
 *    - Client::getOS()
 *    - Client::getReferer()
 *    - Client::getSessionID()
 *    - Client::setSessionID($session_id)
 *    - Client::getSupportedLanguages()
 *    - Client::getSupportedImageTypes($dynamic_img = false)
 */
 

/* Class definition */
class Client
{
	const CLIENT_SESSION_ID = 'SessionID';
	const CLIENT_BROWSER = 'Browser';

	// is set to true if we need to store data in the SESSION (first page viewed)
	private static $firstPageViewed = false;
	
	// key used in the $_SESSION array
	private static $sessionKey = null;
	
	// supported images
	private static $supportedImages   = 'png;gif;jpeg;jpg;bmp;ico';
	private static $IEsupportedImages = 'gif;jpeg;jpg;png;bmp;ico';
	
	
	// Here is done the initilization. Then you can use the other methods
	// @param $session_key key used to store data in the $_SESSION var
	public static function init($session_key)
	{
		if( $session_key )
		{
			if( ! isset($_SESSION[$session_key]) )
			{
				$_SESSION[$session_key] = self::retrieveInformation($session_key);
				self::$firstPageViewed = true;
			}
			self::$sessionKey = $session_key;
		}
		else
		{
			Common::reportFatalError("[Class Client] The key to store client data into the session is null !");
		}
	}
	
	
	/******************************************
	 *            is_xxx functions            *
	 ******************************************/

	// @return Returns if this is the first page visited on our Website for the current session/context
	public static function isFirstPageViewed()
	{
		return self::$firstPageViewed;
	}
	
	// @return Returns true if the client browser is Microsoft Internet Explorer (any version)
	public static function isMSIE()
	{
		return ( self::getBrowserName() == 'MSIE' );
	}
	
	// @return Returns true if the client browser is an old IE (version < 7.0)
	public static function isOldMSIE()
	{
		return ( self::isMSIE() && (self::getBrowserVersion(true) < 7.0) );
	}

	// @return Returns true if the client browser is considered as old (ie: doesn't support display: inline-block property)
	public static function isOldBrowser()
	{
		$version = self::getBrowserVersion(true);
		return ( (self::isOldMSIE()) || (self::getBrowserName() == 'Firefox' && $version < 3.0) );
	}
	
	// Check if the browser is CSS3 compliant (not an old browser). Then you can use CSS3 features for better design (not using tables)
	// @return Returns true if the client browser is CSS3 compliant (not IE<8)
	public static function isCompliantWithCSS3()
	{
		if( $info = self::getInfo() )
			return ( $info[self::CLIENT_BROWSER]['CompliantCSS3'] );
	}
	

	
	/******************************************
	 *           Get_xxx functions            *
	 ******************************************/
	
	// @return Retrieve the $_SESSION array which contains the 'Browser' sub-array and other data
	public static function getInfo()
	{
		if( self::$sessionKey )
			return ( $_SESSION[self::$sessionKey] );
	}	
	
	// @return Returns the client browser name
	public static function getBrowserName()
	{
		if( $info = self::getInfo() )
			return ( $info[self::CLIENT_BROWSER]['Name'] );
	}
	
	// Get the client browser version.
	// Set the optional param to true if you want to get ride off sub-version digits (3.6 instead of 3.6.xxx)
	// @return Returns the Browser version
	public static function getBrowserVersion($short_version = true)
	{
		if( $info = self::getInfo() )
		{
			$key = $short_version ? 'Version2' : 'Version';
			return ( $info[self::CLIENT_BROWSER][$key] );
		}
	}
	
	// @return Get the client system OS name
	public static function getOS()
	{
		if( $info = self::getInfo() )
			return ( $info['OS'] );
	}
	
	// @return Get the referer (previous page)
	public static function getReferer()
	{
		if( $info = self::getInfo() )
			return ( $info['Referer'] );
	}
	
	// @return Get the current session ID
	public static function getSessionID()
	{
		if( $info = self::getInfo() )
			return ( $info[self::CLIENT_SESSION_ID] );
		return 0; // let it to Zero or edit DB::update(): log into DB part
	}
	
	// Set the current session ID
	// @param $session_id Session ID number
	public static function setSessionID($session_id)
	{
		if( self::$sessionKey )
			$_SESSION[self::$sessionKey][self::CLIENT_SESSION_ID] = $session_id;
	}
	
	// @return Returns an array containing language codes accepted by the browser (high-sorted list)
	public static function getSupportedLanguages()
	{
		if( $info = self::getInfo() )
			return ( $info['Languages'] );
	}
	
	// @return Returns an array containing accepted images, depending of the browser (high-sorted list)
	public static function getSupportedImageTypes($dynamic_img = false)
	{
		$exts = self::isCompliantWithCSS3() ? self::$supportedImages : self::$IEsupportedImages;
		if( $dynamic_img )
			$exts = preg_replace("/(^|;)(([a-z]){3,})/i", '${1}${2};${2}.php${4}', $exts);
		return $exts;
	}
	
	
	
	
	/******************************************
	 *           Private functions            *
	 ******************************************/
	 
	// create an array from accepted languages
	private static function findSupportedLanguages()
	{
		$langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$langs = preg_replace("/;q=0\.[0-9]/", '', $langs);
		return explode(',', $langs);
	}
	
	// Use the Browser class to collect information about end-user configuration
	private static function retrieveInformation()
	{
		$browser = new Browser();
		
		// compute a shorter version
		$bVers = str_replace(',', '.', $browser->Version);
		$parts = explode('.', $bVers);
		$bVers = floatval($parts[0].(count($parts)>1 ? '.'.$parts[1] : ''));
		$css3compliant = self::isCss3($browser->Name, $bVers, $browser->Platform);
		//!( ($browser->Name == 'MSIE' && $bVers < 8.0) || ($browser->Name == 'Firefox' && $bVers < 3.6) );
		$bAgent = (strlen($browser->UserAgent) <= 100) ? $browser->UserAgent : (substr($browser->UserAgent, 0, 100).'...');
		return Array
		(
			self::CLIENT_BROWSER => Array
			(
				'Name'      => $browser->Name,
				'Version'   => $browser->Version,
				'Version2'  => $bVers,
				'UserAgent' => $bAgent,
				'CompliantCSS3' => $css3compliant
			),
			'OS'          => $browser->Platform,
			'Referer'     => $_SERVER['HTTP_REFERER'],
			'Languages'   => self::findSupportedLanguages(),
			self::CLIENT_SESSION_ID => null
		);
	}
	
	// Compute if the browser is CSS3 compliant
	// Based on: http://www.findmebyip.com/litmus#target-selector
	// Doesn't include IE7 as it has many issues when displaying special boxes
	// Includes FF3.0 as only few tags are not supported
	private static function isCss3($name, $vers, $os)
	{
		if( preg_match("/Chrome|Safari|Opera/i", $name) )
			return true;
		return ($name == 'Firefox' && $vers >= 3.0 ) || ($name == 'MSIE' && $vers >= 8.0 );
	}
	
}
?>