<?php

// Access rights
	$superAdmin = Authentication::isSuperAdmin();
	if( ! $superAdmin && ! Authentication::getPrivileges('editTexts') )
		Common::error404();
	$_rights = Array(
		'create' => $superAdmin || Authentication::getPrivileges('addTexts'),
		'delete' => $superAdmin || Authentication::getPrivileges('delTexts')
	);
	
// Create the Error panel
	Head::activateErrorPanel();
	
	
// Compute the list of resource types
	$types = Array(TYPE_ALL => Lang::translate('rsc.type.all'));
	$tiny = Array();
	$isTiny = Array();
	$db_types = DB::select(SQL_QUERY_TYPES);
	foreach($db_types as $db_type)
	{
		$types[$db_type->key] = Lang::translate($db_type->value);
		$tiny[] = $db_type->tiny;
		$isTiny[$db_type->key] = $db_type->tiny;
	}
	
	
	
	
/** ======================= Form Return ======================= */

	/** - - - - Init - - - - */
	$errors = Array();
	$panel_to_display = VIEWING_MODE_TEXT_SEARCH;
	$display_result_panel = false;
	$searchLanguage = null;
	
	
	/** - - - - Check Functions - - - - */
	
	function checkFormCreation($rscKey, $type, $tiny)
	{
		global $types;
		$errors = Array();
		if( ! preg_match("/^([a-z0-9\xC0-\xFF\@_\-\.]{5,50})$/i", $rscKey) )
			$errors[] = ERROR_NEW_INVALID_KEY;
		if( ! in_array($type, array_keys($types)) )
			$errors[] = ERROR_NEW_INVALID_TYPE;
		return $errors;
	}
	
	
	function checkSearchByTextForm($lang, $type)
	{
		global $types, $foLangs, $boLangs;
		$errors = Array();
		
		if( ! in_array($type, array_keys($types)) )
			$errors[] = ERROR_NEW_INVALID_TYPE;
		return $errors;
	}
	
	
	function checkSearchByIDForm($type)
	{
		global $types;
		if( ! in_array($type, array_keys($types)) )
			return Array(ERROR_NEW_INVALID_TYPE);
		return Array();
	}
	

	/** - - - - include the file handeling forms' return ! - - - - */
	include "form_returns.inc.php";
	
	
	
	/** - - - - Additional JS and parameters to add to the linked JS - - - - */
	
	$jsParams = Array(GET_VIEWING_MODE => $panel_to_display);
	if( count($errors) )
		$jsParams += Array(POST_ERROR => implode(',',$errors));
	if( $searchLanguage )
		$jsParams += Array(GET_EDIT_LANGUAGE => $searchLanguage);
	
	Head::addJS( Array('scriptaculous.main?load=effects,dragdrop,controls') );
	Head::setLinkedJSProperties($jsParams);
?>