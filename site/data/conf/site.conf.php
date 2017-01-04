<?php

	// -------- List of available settings with default value ---------
	// Enable browsing your web site on the same computer/browser with different sessions
	//	'SETTINGS_USE_MULTI_SESSIONS' => false
	// Enable display of warning messages
	//	'SETTINGS_DEBUG_MODE' => false
	// Clean the session after displaying the page
	//	'SETTINGS_CLEAN_SESSION' => false
	// Create stats about pages viewing
	//	'SETTINGS_LOG_VISITS' => true
	// Define if a Mail server is running
	//	'SETTINGS_IS_MAIL_SERVER_RUNNING' => true
	// Active GZIP compression for files
	//	'SETTINGS_ENABLE_GZIP_MODE' => true
	// Active the Multi languages mode
	//	'SETTINGS_MULTI_LINGUAGES' => true
	// ------------------------- End of list --------------------------



/** Here you can change the settings */
		
		// use the changeSettings function with the right parameter
		changeSettings('SETTINGS_DEBUG_MODE', true);
		changeSettings('SETTINGS_IS_MAIL_SERVER_RUNNING', false);
		//changeSettings('SETTINGS_USE_MULTI_SESSIONS', true);
		
		//changeSettings('SETTINGS_CLEAN_SESSION', true);
		
		
		if( IS_ADMIN_CONTEXT ) // force the back office to accept multi languages
			changeSettings('SETTINGS_MULTI_LINGUAGES', true);

?>