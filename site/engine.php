<?php

$type = Page::getContentType(); // Retrieve page type
$page = Context::getPageCode(); // Retrieve page code


// local dir and path to data file (sharing data with PHP/CSS/JS files)
$dir = (DIR_PHP."$page/");
$data = "$dir$page.data.php";


switch( $type )
{
	// ----------------- AJAX Context -----------------
	case Page::KEY_AJAX:
		
		Vars::cleanAfterAjaxCall();
		Context::updateViewingMode(Context::AJAX_CALL);
		//break; // no break clause to get into HTML case (Page::KEY_HTML)
	
	
	// ----------------- HTML Context -----------------
	case Page::KEY_HTML:
		
		// for session cleaning if activated
		$__SESSION = new Session(); // the destructor function will be automatically called later
		
		if( Common::isAdminContext() && ! Authentication::isLogged() )
		{
			// Prevent acces to admin page if this is not the login page and if the page is not considered as public
			if( ! preg_match("/^public_/", $page) && ($loginPage = Authentication::getAuthenticationPage()) != $page )
				Context::forward(Array(POST_REDIRECT_TO => Context::HTML(null, true, $_GET)), $loginPage);
		}
		
		if( is_dir($dir) )
		{
			// includes the config
			include DIR_CONF.BASE_NAME.'.conf.php';
			
			// include the data file
			if( file_exists($data) ) /* shared data with the PHP/JS/CSS files */
				include $data;
			
			// include the configuration file
			if( file_exists($conf = "$dir$page.conf.php") ) /* used for security and handling form returns/submitions */
				include $conf;
			
			if( Page::isAJAX() )
				die(); // terminate here for Ajax Calls
			
			// save pages viewed/history into DataBase
			if( ! Common::isAdminContext() )
				Context::rememberPageHasBeenVisited();
			
			// include the CSS file, if it exists
			Head::addDefaultCSS($page, Array($dir));
			
			// include the Javascript file, if it exists
			Head::addDefaultJS($page, Array($dir));
			
			// add the html.head part (meta + css + js)
			if( ! Context::isAjaxCall() )
				include DIR_PHP . 'meta.inc.php';
			
			// add the header: div containers and layout
			if( Context::isCasualPage() )
				include DIR_PHP . 'header.inc.php';
			
			// include the page, finally
			if( file_exists($main = "$dir$page.php") )
				include $main;
			
			// close the layout (if needed) and the body+html tags
			include DIR_PHP . 'footer.inc.php';
			
			// terminate
			die();
		}
		break;
	
	
	// ---------------- JAVASCRIPT Context ----------------
	case Page::KEY_JS:
		$js_page = Common::getJSPath($page);
		Context::loadInternalMedia($js_page, $data);
		die(); // terminate
		break;
	
	
	// ------------------- CSS Context --------------------
	case Page::KEY_CSS:
		include DIR_PHP . 'css.conf.php';
		$css_page = Common::getCSSPath($page);
		Context::loadInternalMedia($css_page, $data);
		die(); // terminate
		break;
	
	
	// ------------------ IMAGE Context -------------------
	case Page::KEY_IMAGE:
		$extensions = Client::getSupportedImageTypes(true);
		if( Context::loadExternalMedia($page, DIR_IMAGES, $extensions) )
			die(); // terminate
		break;
	
	
	// ------------------- DATA Context -------------------
	case Page::KEY_DATA:
		$extension = Common::getExtention($page);
		if( Context::loadExternalMedia($page, DIR_ATTACHMENTS, $extension) )
		{
			// save pages viewed into DataBase
			Context::rememberPageHasBeenVisited();
			include DIR_ATTACHMENTS.BASE_NAME.'.do.php';
			die(); // terminate
		}
		break;
	
	
}

// Unable to find the file or unknow file type
Common::error404();
?>