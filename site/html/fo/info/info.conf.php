<?php
/** Control the access to this page */
if( ! (Common::isLocalContext() || Authentication::isLogged()) )
	Common::error404();


Head::addJS( Array('scriptaculous.main', Array('load' => 'effects')) );
	
?>