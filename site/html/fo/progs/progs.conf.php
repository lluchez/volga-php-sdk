<?php
	Head::addCSS('form');
	Head::addJS( Array('scriptaculous.main?load=effects,dragdrop,controls') );
	
	include 'results.inc.php';
	if( Vars::pDefined(POST_CAT_ID, POST_LANG_ID, POST_SORT_ID) )
	{
		generateProgramPanelContent();
		die();
	}
?>