<?php

include DIR_CONF . 'DAL.conf.php';
include DIR_CONF . 'alias.conf.php';


// The constants used below are defined in '/conf.hidden.php' or in '/site/data/conf/data.conf.php'


/********************************
 *         Class Common         *
 ********************************/

	Common::init
	(
		// check whether this site is diployed or not (local context)
				IS_LOCAL_CONTEXT, // defined in 'site/data/conf/data.conf.php'
		// define the value to be returned when no Mail server is running
				true,
		// alias for link creation
				(IS_LOCAL_CONTEXT ? LOCAL_ALIAS : DEPLOYED_ALIAS), // defined in '/xxxx.php' where is stands for 'index' or 'admin'
		// create relative URL
				true
	);



/********************************
 *          Class Vars          *
 ********************************/

	Vars::init();



/********************************
 *         Class Session        *
 ********************************/
 
	Page::init
	(
		// Array containing associated Url keys to determine what kind of content it is
				Array( // POST_xxx_FILE defined in './alias.conf.php'
					Page::KEY_HTML  => POST_HTML_FILE,
					Page::KEY_JS    => POST_JS_FILE, 
					Page::KEY_CSS   => POST_CSS_FILE, 
					Page::KEY_IMAGE => POST_IMAGE_FILE, 
					Page::KEY_DATA  => POST_DATA_FILE,
					Page::KEY_AJAX  => POST_AJAX_FILE
				), 
		// define the default pages to be redirected on it when location param is not defined 
				PAGE_ENTRY_POINT // 'home', 'login', etc...
	);



/********************************
 *         Class Session        *
 ********************************/
 
	Session::init
	(
		// Key used for multi session ($_GET key to send the Session ID)
				'sid',
		// Force session_regenerate_id() if site not handling multi-sessions
				true,
		// array containing every session keys used
				$_conf_all_session_keys
	);
 
	
	
/********************************
 *         Class Client         *
 ********************************/

	Client::init
	(
		// define the session index that will contain client side information
				SESSION_KEY_CLIENT
	);



/********************************
 *         Class Media          *
 ********************************/
	
	$REPLACE = '#';
	Media::init
	(
		$REPLACE, // Pattern for name file replacements
		Array
		(
			Page::KEY_HTML  => "html/$REPLACE.html",				// HTML pattern
			Page::KEY_CSS   => "style/$REPLACE.css",				// CSS pattern
			Page::KEY_JS    => "javascript/$REPLACE.js",		// JS pattern
			Page::KEY_IMAGE => "images/$REPLACE",					  // IMG pattern
			Page::KEY_DATA  => "attachments/$REPLACE",			// DATA pattern
			Page::KEY_AJAX  => "ajax/$REPLACE"							// Ajax pattern
		),
		$mime_types
	);



/********************************
 *        Class Database        *
 ********************************/

	//$db = new Database
	DB::init
	(
		// Local connexion settings
				Array( 'host' => 'localhost', 'base' => 'sdk', 'user' => 'root', 'pass' => ''),
		// Web connexion settings (when the site is deployed: not running on localhost)
				Array( 'host' => 'localhost', 'base' => 'sdk', 'user' => 'root', 'pass' => ''),
		// define the SQL Log table to list SQL query errors
		// if not null, it should be an existing table.
		// look at the end of the Database class to find the SQL script to generate it
				SQL_TABLE_LOG,
		// define the destination email address to receive alerts concerning SQL query errors 
		// if not null, it should be a valid email (that match casual RegEx)
				null
	);
	$useless_db = new DB(); // just to have the destructor working...
	DataDAL::init
	(
		// define the SQL `data` table which stores main data
		// if not null, it should be an existing table.
		// look at the end of the Database class to find the SQL script to generate it
				SQL_TABLE_DATA,
				Array()
	);

	
/********************************
 *        Class Languages       *
 ********************************/

if( SETTINGS_MULTI_LINGUAGES )
{
	LangSQL::init
	(
		// MySQL table name listing every language available for this Website
				SQL_TABLE_LANG,
		// MySQL table name listing every localized resource
				SQL_TABLE_LOC_KEY,
		// MySQL table name containing text/content for localized resource related to languages
				SQL_TABLE_LOC_TEXT,
		// MySQL view name containing the associed text/content for localized resources
				SQL_VIEW_TEXTS
	);
	
	// Disable the following line if you don't want the Back Office to accept different languages
	Lang::localizeBackOffice(true);
	
	Lang::init
	(
		// define the session index that will contain client side information
				SESSION_KEY_LANGUAGE,
		// default language to be applied if none other way works
				DEFAULT_LANGUAGE
	);
	
	Lang::computeLanguage
	(
		// set the index of the $_GET var to change the language
				POST_SET_LANG,
		// set the index of the $_GET var to change the language context
				POST_GET_LANG
	);
}



/********************************
 *         Class Context        *
 ********************************/

	include DIR_CONF . 'pages_table.conf.php';
	
	ContextDAL::init
	(
		getPagesTable()
	);
	
	Context::init
	(
		// SQL table name to store connection rows
				SQL_TABLE_CONNECTION,
		// SQL table name to store pages viewed
				SQL_TABLE_VIEWEDPAGES,
		// Time where the connection is considered as open: no new connection saved into DB within this time (in hours)
				2
	);



/********************************
 *     Class Authentication     *
 ********************************/

	Authentication::init
	(
		// key used to store data in the $_SESSION var
				SESSION_KEY_AUTHENTICATION,
		// SQL information to connect as administrator
				Array(
					Authentication::AUTH_KEY_SQL_TABLE => SQL_TABLE_ACCOUNT, 
					Authentication::AUTH_KEY_ID    => 'id',
					Authentication::AUTH_KEY_LOGIN => 'email',
					Authentication::AUTH_KEY_PASS  => 'password',
					Authentication::AUTH_KEY_SUPER_ADMIN  => 'superAdmin',
					Authentication::AUTH_KEY_PASS_ENCTYPE => 'MD5'
				),
		// SQL information to retrieve privileges (administrator)
				Array(Authentication::AUTH_KEY_SQL_TABLE => SQL_TABLE_PRIVILEGES, Authentication::AUTH_KEY_ID => 'account_id'),
		// authentication/login page
				'login'
	);



if( Page::isHTML() )
{
/********************************
 *        Class Languages       *
 ********************************/

	Head::init
	(
		// default title
				Common::isAdminContext() ? BO_SITE_TITLE : SITE_TITLE,
		// default CSS rendering mode
				'', //'screen',
		// default JS behavior: localized Javascript file (with language parameter) ?
				false,
		// Error Panel ID
				'errorPanel'
	);
	Head::addMetaData('AUTHOR', AUTHOR);
	Head::addMetaData('LANGUAGE', SETTINGS_MULTI_LINGUAGES ? Lang::getCurrentLanguage() : 'en');
	Head::addMetaData('COPYRIGHT', SITE_COPYRIGHT);
	Head::addMetaData('REVISIT-AFTER', Common::isAdminContext() ? 'Never' : '15 DAYS');
	Head::addMetaData('ROBOTS', Common::isAdminContext() ? 'noindex,nofollow' : 'All');



/********************************
 *          Class Image         *
 ********************************/

	Image::init
	(
		// define the default image quality when creating a new image
				70
	);



/********************************
 *          Class Form          *
 ********************************/
	
	Form::init
	(
		// define whether the browser handles CCS3
				Client::isCompliantWithCSS3(),
		// define the rollover text for mandatory fields
				//(SETTINGS_MULTI_LINGUAGES ? Lang::translate('forms.requiredField') : 'Required field')
				'TO DO: mandatoty field !!!!'
	);

}
?>