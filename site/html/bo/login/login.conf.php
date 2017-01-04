<?php
	
	if( Vars::pDefined(POST_FORGET_PASSWORD) )
	{
		/** ----------------- NOT DONE: TODO !!!! ----------------- */
		// STEP1: check if PSSWD in DB
		// STEP2: send mail if STEP1 OK
		// STEP3: OK: if STEP2 OK, KO otherwise
		// STEP4: define type/content for Email...
		$title = "Title";
		$content = "...";
		$res = Common::mail(Vars::get(POST_FORGET_PASSWORD), $title, $content); // send email to reset password...
		// create a hash key: MD5(Email+(MD5(pass)))
		echo $res ? 'ok' : 'ko';
		die();
	}
	elseif( Vars::pDefined(POST_EMAIL, POST_PASSWORD) )
	{
		if( Authentication::login(POST_EMAIL, POST_PASSWORD) )
		{
			if( Vars::defined(POST_REDIRECT_TO) )
			{
				Vars::clearPost();
				header("Location: ".Vars::get(POST_REDIRECT_TO));
			}
			else
				Context::forward(null, PAGE_ENTRY_POINT);
			die();
		}
		else
		{
			Head::setLinkedJSProperties(Array(POST_AUTHENTICATION_ERROR => true));
		}
	}

	Context::updateViewingMode(Context::POPUP); // don't include header.inc.php : no additional layout
?>