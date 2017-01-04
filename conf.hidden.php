<?php
/** Allow acces to the site only if some required constants have been defined */
if( defined('IS_ADMIN_CONTEXT') && defined('HTML_FOLDER') && defined('PAGE_ENTRY_POINT') && defined('LOCAL_ALIAS') ) 
{
	
/** Define the files structure and paths */
	// --------------------------------
	define('DIR_SITE',            'site/');
		define('DIR_DATA',          DIR_SITE.'data/');
			define('DIR_ATTACHMENTS', DIR_DATA.'attachments/');
			define('DIR_CACHE',       DIR_DATA.'cache/');
			define('DIR_CONF',        DIR_DATA.'conf/');
		define('DIR_DISPLAY',       DIR_SITE.'display/');
			define('DIR_CSS',         DIR_DISPLAY.'css/');
			define('DIR_FONTS',       DIR_DISPLAY.'fonts/');
			define('DIR_IMAGES',      DIR_DISPLAY.'images/');
		define('DIR_LIBRARY',       DIR_SITE.'library/');
			define('DIR_CLASSES',     DIR_LIBRARY.'classes/');
			define('DIR_JS',          DIR_LIBRARY.'javascript/');
			define('DIR_FCT',         DIR_LIBRARY.'php/');
		define('DIR_HTML',          DIR_SITE.'html/');
			define('DIR_PHP',        	DIR_HTML.HTML_FOLDER.'/'); // defined in '/xxx.php';
	// --------------------------------
	

/** We need to start/retrieve a session */
	//@session_start();
	
	

/** Include classes and data */

	// include all classes
	// --------------------------------
		include DIR_CLASSES . 'string.class.php';
		include DIR_CLASSES . 'common.class.php'; // requires String
		include DIR_CLASSES . 'vars.class.php'; // requires String, Common
		include DIR_CLASSES . 'page.class.php'; // requires Common, Vars
		include DIR_CLASSES . 'session.class.php'; // requires Common, Vars
		include DIR_CLASSES . 'client.class.php';
		include DIR_CLASSES . 'media.class.php'; // requires Common, Vars, Session
		include DIR_CLASSES . 'database.class.php'; // requires String, Common
		include DIR_CLASSES . 'language.class.php'; // requires String, Common, Vars, Database, Media
		include DIR_CLASSES . 'context.class.php'; // requires String, Common, Vars, Lang, Database, Media, Client, Page, DataDAL
		include DIR_CLASSES . 'authentication.class.php'; // required String, Database
		include DIR_CLASSES . 'head.class.php'; // requires Media, Language
		include DIR_CLASSES . 'image.class.php'; // requires Common
		include DIR_CLASSES . 'upload.class.php'; // requires String, Common, Lang
		include DIR_CLASSES . 'form.class.php'; // requires Common
		include DIR_CLASSES . 'menu.class.php'; // requires Lang, Context
	// --------------------------------
	
	// define pre-data
		include DIR_CONF . 'data.conf.php';
		

/** Settings to enable/disable features */
	// Settings default values
	// --------------------------------
	$SETTINGS_OPTIONS = Array
	(
		// enable browsing your web site on the same computer/browser with different sessions
			'SETTINGS_USE_MULTI_SESSIONS' => false,
		// enable display of warning messages
			'SETTINGS_DEBUG_MODE' => false,
		// clean the session after displaying the page
			'SETTINGS_CLEAN_SESSION' => false,
		// create stats about pages viewing
			'SETTINGS_LOG_VISITS' => true,
		// define if a Mail server is running
			'SETTINGS_IS_MAIL_SERVER_RUNNING' => true,
		// Active GZIP compression for files
			'SETTINGS_ENABLE_GZIP_MODE' => true,
		// Active the Multi languages mode
			'SETTINGS_MULTI_LINGUAGES' => true
	);
	// --------------------------------
	
	
	// Settings functions and defines
	// --------------------------------
		function changeSettings($key, $val) { global $SETTINGS_OPTIONS; $SETTINGS_OPTIONS[$key] = (bool)$val; }
		function applySettings() { global $SETTINGS_OPTIONS;	foreach($SETTINGS_OPTIONS as $key => $val) { define($key, $val); } } // create global var for the settings
		// include the server/site settings
		include DIR_CONF . 'site.conf.php';
		applySettings();
	// --------------------------------
	
/** Initilization of classes and additional data  */

	// initialize each class
		include DIR_CONF . 'classes.conf.php';
	
	// define post-data (includes the file a second time)
		include DIR_CONF . 'data.conf.php';
	

/** Call to the engine to render files */

	// run the rendering engine for any type of document/page
		include DIR_SITE . 'engine.php';

}
?>