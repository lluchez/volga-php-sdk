<?php

/******************************
 *        Class Session       *
 ******************************/

/**
 * @desc-start
 * This static class is used to create a new session or use the previously created.
 * It also handles multi-session.
 * You can also create a Session object. Then when the destructor will be called, _
 *   every $_SESSION keys (given at Session::init()) will be deleted.
 * This class is session hijacking proof.
 * @desc-end
 *
 * Here is the list of functions:
 *		- Session::init($key_session_id, $sessions = Array())
 *		- Session::getSessionIdKey()
 * 
 *		- Session()
 *		- __destruct() // automatically called by the useless object created for HTML pages (cf engine.php);
 */


/* Class definition */
class Session
{
	const SESSION_CHECKSUM = 'session_security_check';
	private static $list_session_keys = null; // for session cleaning
	private static $key_session_id = null;
	private static $session_regenerated = false;

	// Initilisation function of this class. To be called before calling any function
	// @param $key_session_id $_GET key used when multi session mode is turned ON
	// @param $forceSessionIdRegeneration Force session_regenerate_id() if site not handling multi-sessions
	// @param $sessions Array of session keys used by other classes. When SETTINGS_CLEAN_SESSION is activated, it will clean all these sub-arrays
	public static function init($key_session_id, $forceSessionIdRegeneration = false, $sessions = Array())
	{
		// Create the session
		self::$key_session_id = $key_session_id;
		self::$list_session_keys = $sessions; // save session keys, for cleanup
		
		if( SETTINGS_USE_MULTI_SESSIONS )
		{
			$session_id = Vars::get(self::$key_session_id);
			if( $session_id && preg_match("/^([a-z0-9])+$/i", $session_id) && @session_id($session_id) )
			{
				session_start();
			}
			else
			{
				self::start_new_session(false);
				$_SESSION = Array();
			}
		}
		else
			self::start_new_session($forceSessionIdRegeneration);
		
		// perform checks about a hash saved to be sure someone is not trying to rob data
		self::security_check();
	}
	
	// Get the $_GET key used when multi session mode is turned ON
	// @return Returns the Session key
	public function getSessionIdKey()
	{
		return self::$key_session_id;
	}
	
	// Void constructor
	// Creating a Session object that will trigger the SETTINGS_CLEAN_SESSION feature when destructor will be called
	public function Session()
	{}
	
	// Overrides the default destructor
	// When the Session object will be destroyed, if the SETTINGS_CLEAN_SESSION mode is ON _
	// it willl unset $_SESSION sub-arrays listed at Session::init()
	public function __destruct()
	{
		// Clean the global var $_SESSION
		if( SETTINGS_CLEAN_SESSION && is_array(self::$list_session_keys) )
		{
			Common::reportRunningWarning("[Class Common] The cleaning session mode is enabled !");
			foreach(self::$list_session_keys as $key)
				unset($_SESSION[$key]);
			//unset(self::SESSION_CHECKSUM);
		}
	}
	
	
	/** ---------------------- Private functions ---------------------- */
	
	// Creates a hash control for session security (Session Hijacking) and returns it
	private static function generate_control_value()
	{
		return md5($_SERVER['HTTP_USER_AGENT'].'---'.$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
	
	// Starts the session and regenerate the session_id if necessary
	// @param $force_regenerate_id Force regenerating the session_id if page viewed is HTML
	private static function start_new_session($force_regenerate_id = false)
	{
		// create a new session
		session_start();
		if( ($force_regenerate_id && Page::isHTML()) || (! $_SESSION) )
			self::session_regenerate_id(true); // will fails if text is already displayed
	}
	
	// Make some checks and regenerate a session_id if something is wrong
	private static function security_check()
	{
		$md5 = self::generate_control_value();
		if( isset($_SESSION[self::SESSION_CHECKSUM]) || $_SESSION )
		{
			if( $_SESSION[self::SESSION_CHECKSUM] != $md5 ) // someone is trying to rob/infiltrate the session
			{
				self::session_regenerate_id();
				$_SESSION = Array();
			}
		}
		else
			$_SESSION[self::SESSION_CHECKSUM] = $md5;
	}
	
	// Do not regenerate another session if already done...
	// @param $delete_old_session Boolean: delete old session?
	private static function session_regenerate_id($delete_old_session = false)
	{
		if( self::$session_regenerated )
			return
		self::$session_regenerated = true;
		@session_regenerate_id($delete_old_session);
	}

};

?>