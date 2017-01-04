<?php

/******************************
 *    Class Authentication    *
 ******************************/

/**
 * @desc-start
 * This class is used to handle the connection to the back office and manage access restrictions.
 * @desc-end
 *
 * Here is the list of functions:
 *		- Authentication::init($session_key, $admin_login_sql_query, [$sql_mask_keys, $authentication_page])
 * 
 *		- Authentication::login($name, $pass)
 *		- Authentication::logout()
 *		- Authentication::isLogged()
 *    - Authentication::getAuthenticationPage();
 *
 *		- Authentication::getLoggedData([$key])
 *		- Authentication::getPrivileges([$key])
 *		- Authentication::isSuperAdmin()
 */


class Authentication
{
	// keys to acces to data
	const AUTH_KEY_ID = 					'id';
	const AUTH_KEY_LOGIN = 				'login';
	const AUTH_KEY_PASS = 				'pass';
	const AUTH_KEY_SUPER_ADMIN = 	'super_admin';
	const AUTH_KEY_PASS_ENCTYPE = 'pass_enctype';
	const AUTH_KEY_SQL_TABLE = 		'table';
	

	// Default SQL encryption for passwords
	const AUTH_PASS_ENCRYPTION_DEFAULT = 'MD5';
	
	// Sub-key for session to save privileges
	const AUTH_SESSKEY_GRANTS = 'privileges'; 

	// key used to store data in the $_SESSION var
	private static $sessionKey = null;
	
	// SQL information used to connect as administrator
	// note: every field in the 'SELECT' clause will be store in the session
	private static $account_info = null;
	
	// SQL information to retrieve privileges (administrator)
	private static $privileges_info = null;
	
	// page to login. Redirect to this page if trying to get to an admin page while not logged-in
	private static $login_page = null;
	
	
	// Initilisation function of this class. To be called before calling any function
	// @param $session_key key used to store data in the $_SESSION var
	// @param $account_info SQL information to connect as administrator
	// @param $privileges_info SQL information to retrieve privileges (administrator)
	// @param $authentication_page authentication/login page
	public static function init($session_key, $account_info, $privileges_info, $authentication_page = 'login')
	{
		if( $session_key )
		{
			if( empty($account_info[self::AUTH_KEY_PASS_ENCTYPE]) )
				$account_info[self::AUTH_KEY_PASS_ENCTYPE] = self::AUTH_PASS_ENCRYPTION_DEFAULT;
			self::$sessionKey      = $session_key;
			self::$account_info    = $account_info;
			self::$privileges_info = $privileges_info;
			self::$login_page      = $authentication_page;
			
		}
		else
			Common::reportFatalError("[Class Authentication] The key to store authentication data into the session is null !");
	}
	
	
	// Returns the page-code corresponding to the page for logging-in users (containing the Back Office login form)
	// @return the page use for log-in
	public static function getAuthenticationPage()
	{
		return self::$login_page;
	}
	
	// Try to log-in.
	// @param $name Name/Pseudo/Email of the person trying to connect
	// @param $pass associated password
	// @return If the couple name/pass matches a row in the DB, returns true and save data in the session
	public static function login($name, $pass)
	{
		$privs = self::AUTH_SESSKEY_GRANTS;
		$tSQL1 = self::$account_info;
		$tSQL2 = self::$privileges_info;
		$sql   = "SELECT * FROM `".$tSQL1[self::AUTH_KEY_SQL_TABLE]."` WHERE `"
					.$tSQL1[self::AUTH_KEY_LOGIN]."`='".String::safeSQL(Vars::get($name))."' AND `"
					.$tSQL1[self::AUTH_KEY_PASS] ."`=".$tSQL1[self::AUTH_KEY_PASS_ENCTYPE]."('".String::safeSQL(Vars::get($pass))."')";
		if( $row = DB::getOneRow($sql) )
		{
			unset( $row->$tSQL1[self::AUTH_KEY_PASS] ); // remove encrypted password field
			if( ! $row->$tSQL1[self::AUTH_KEY_SUPER_ADMIN] )
				unset( $row->$tSQL1[self::AUTH_KEY_SUPER_ADMIN] ); // remove super admin field if not true
			$sql2 = "SELECT * FROM `".$tSQL2[self::AUTH_KEY_SQL_TABLE]
						."` WHERE `".$tSQL2[self::AUTH_KEY_ID]."`=".$row->$tSQL1[self::AUTH_KEY_ID];
			if( $row_grants = DB::getOneRow($sql2) ) // retrieve the access
			{
				unset( $row_grants->$tSQL2[self::AUTH_KEY_ID] ); // remove the duplicated Id field
				foreach( $row_grants as $key => $grant )
					if( ! $grant )
						unset($row_grants->$key); // remove the fields with no grant
			}
			$row->$privs = $row_grants;
			$_SESSION[self::$sessionKey] = $row;
			return true;
		}
		else
			return false;
	}
	
	
	// logout: unset the data from the session and forward the user to another page (default behavior brings to the login page)
	public static function logout($redirect_to = null, $params = null)
	{
		unset($_SESSION[self::$sessionKey]);
		if( is_null($redirect_to) )
			$redirect_to = self::$login_page;
		Context::forward($params, $redirect_to);
	}
	
	
	// @return Returns true if the user is already logged as an administrator
	public static function isLogged()
	{
		return isset($_SESSION[self::$sessionKey]);
	}
	
	
	// get the the corresponding item or the full array of stored information
	// @return Returns the full array if no parameter is sent, and only the corresponding value if a key is given
	public static function getLoggedData($key = null)
	{
		if( $key )
			return $_SESSION[self::$sessionKey]->$key;
		else
			return $_SESSION[self::$sessionKey];
	}
	
	// Retrieve privileges about the administrator
	// @return Returns the full array of privileges if no parameter is sent, and only the corresponding bool-value if a key is given (null, if user not logged)
	public static function getPrivileges($key = null)
	{
		if( $privileges = self::getLoggedData(self::AUTH_SESSKEY_GRANTS) )
		{
			if( $key )
				return (bool)$privileges->$key;
			else
				return $privileges;
		}
		return null;
	}
	
	// @return Returns true if this is a super admin
	public static function isSuperAdmin()
	{
		return (bool)self::getLoggedData(self::$account_info[self::AUTH_KEY_SUPER_ADMIN]);
	}
	
}


/**


-- 
-- Table structure for table `account`
-- 

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL, -- save it into MD5 format
  `superAdmin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `privileges`
-- 

DROP TABLE IF EXISTS `privileges`;
CREATE TABLE IF NOT EXISTS `privileges` (
  `account_id` mediumint(9) NOT NULL,
  `editAccount` tinyint(1) NOT NULL default '0',
  `editData` tinyint(1) NOT NULL default '0',
  `editTexts` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

*/

?>