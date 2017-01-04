<?php
/** 
 * This file we be included twice:
 * The first time it will declare only the required data,
 *   then all the other stuffs will be declared and computed
 *   thanks to the classes created and initialized
 */

 
 /* ----------------- Second time we include this data page ----------------- */

if( defined('IS_LOCAL_CONTEXT') ) /* Second call to this page */
{

/** $_GET keys (for Vars::get()/Vars::defined()) */


	// email address
			define('EMAIL_ADDRESS', DataDAL::getDataValue('email'));
			Head::addMetaData('OWNER', EMAIL_ADDRESS);
	
	
}
else /* ----------------- First time we include this data page ----------------- */
{
	
/** Define if we are in a local context (developer mode, on the local server) */
		define('IS_LOCAL_CONTEXT', (bool)($_SERVER['SERVER_ADDR'] == '127.0.0.1' || strpos($_SERVER['SERVER_NAME'], 'dns.')) );
	
	
	
/** Sessions keys for $_SESSION[] */

	define('SESSION_KEY_AUTHENTICATION', 'authentication_configuration_data');	// Key for the authentication (admin context)
	define('SESSION_KEY_CLIENT',   'client_configuration_data'); 								// Key for the browser, referer and so on
	define('SESSION_KEY_LANGUAGE', 'languages_configuration_data');							// Key for the language configuration
	
	// Make a list of all sessions objects used.
	// If you set the above parameter 'SETTINGS_CLEAN_SESSION' to true, it will erase all their values
		$_conf_all_session_keys = Array
		(
			// SESSION_KEY_AUTHENTICATION,
			SESSION_KEY_CLIENT,
			SESSION_KEY_LANGUAGE
		);



/** $_GET keys (for Vars::get()/Vars::defined()) */

	define('POST_SET_LANG', 		'set_core_lang');			// $_GET keys to change the language
	define('POST_GET_LANG', 		'get_core_lang');						// $_GET keys to set the language in the current context
	define('POST_REDIRECT_TO', 	'redirect_to');	// $_GET keys to store the URI to go after login
	define('POST_ERROR', 				'error');							// $_GET keys to display errors (forms validation)



/** MySQL Database tables */

	define('SQL_TABLE_LANG', 			'language'); 	// MySQL table name listing every language available for this Website
	define('SQL_TABLE_LOC_KEY', 	'loc_key'); 	// MySQL table name listing every localized resource
	define('SQL_TABLE_LOC_TEXT', 	'loc_text'); 	// MySQL table name containing text/content for localized resource related to languages
	define('SQL_VIEW_TEXTS', 			'loc_view'); 	// MySQL view name containing the associed text/content for localized resources
	
	define('SQL_TABLE_DATA', 				'data'); 				// MySQL data table (that can be edited in the BO)
	define('SQL_TABLE_LOG',  				'sql_log'); 		// MySQL log table (save SQL auery errors)
	define('SQL_TABLE_CONNECTION',  'connection'); 	// MySQL connection rows table (to keep trace of visits and user params)
	define('SQL_TABLE_VIEWEDPAGES', 'viewed_page'); // MySQL pages viewed table (to track pages viewed)
	
	define('SQL_TABLE_ACCOUNT',    	'account'); 		// MySQL administrators table
	define('SQL_TABLE_PRIVILEGES', 	'privileges'); 	// MySQL admin-privileges table


/** Other variables */
	
	// Author name
			define('AUTHOR', 'Lionel Luchez');
	
	// Web site default title
			define('SITE_TITLE',     'Blank SDK');
			define('BO_SITE_TITLE',  SITE_TITLE.' - Back Office');
	
	// Copyright
			define('SITE_COPYRIGHT', '&copy; 2009 '.SITE_TITLE);
	
	// Encoding type (charset)
			define('ENCTYPE_CHARSET', 'ISO-8859-1'); // 'UTF-8'); // Used by many classes
			define('DEFAULT_LANGUAGE', 'fr');
	
	// Options/Choice
			define('YES', 'yes');
			define('NO',  'no');
			define('ALL', 'all');
}

?>