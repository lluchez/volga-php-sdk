<?php
	// MAX Key length
	define('MAX_KEY_LEN', 50);

	// Viewing mode
	define('GET_VIEWING_MODE',          'mode'); // $_GET key
	define('VIEWING_MODE_TEXT_SEARCH',  'searchText');
	define('VIEWING_MODE_ID_SEARCH',    'searchID');
	define('VIEWING_MODE_CREATION',     'create');
	define('VIEWING_MODE_VIEW_RESULTS', 'results');
	$panels = Array(VIEWING_MODE_TEXT_SEARCH, VIEWING_MODE_ID_SEARCH, VIEWING_MODE_CREATION, VIEWING_MODE_VIEW_RESULTS);
	
	
	// $_GET keys
	define('GET_TXTSEARCH_TEXT',     'txt_search_key');
	define('GET_TXTSEARCH_LANG',     'txt_search_lang');
	define('GET_TXTSEARCH_TYPE',     'txt_search_type');
	define('GET_TXTSEARCH_PART',     'txt_search_part');
	define('GET_TXTSEARCH_FULLTEXT', 'txt_search_fulltext');
	
	define('GET_IDSEARCH_KEY',  'id_search_key');
	define('GET_IDSEARCH_TYPE', 'id_search_type');
	define('GET_IDSEARCH_PART', 'id_search_part');
	
	define('GET_CREATE_KEY',     'create_key');
	define('GET_CREATE_TYPE',    'create_type');
	define('GET_CREATE_TINY',    'create_tiny');
	define('GET_CREATE_PART',    'create_part');
	define('GET_CREATE_PREFIX_TEXTAREA', 'create_text_');
	define('GET_CREATE_PREFIX_INPUT',    'create_input_');
	define('GET_RES_ID',         'res_id');
	
	define('GET_EDIT_KEY',     'edit_key');
	define('GET_EDIT_TYPE',    'edit_type');
	define('GET_EDIT_TINY',    'edit_tiny');
	define('HIDDEN_EDIT_IDX',  'edit_idx');
	define('HIDDEN_EDIT_NAME', 'edit_name');
	define('GET_EDIT_PREFIX_TEXTAREA', 'edit_text_');
	define('GET_EDIT_PREFIX_INPUT',    'edit_input_');
	define('GET_EDITFROM_KEY', 'editRes');
	define('GET_EDIT_LANGUAGE','editLang');
	
	define('GET_DEL_RESNAME',  'del_name');
	define('GET_DEL_RESIDX',   'del_idx');
	
	define('GET_AJAX_KEY',    'ajax_key');
	define('GET_AJAX_TYPE',   'ajax_type');
	define('GET_AJAX_PART',   'ajax_part');
	
	
	// PopUp Infos
	define('POPUP_SIZE_BORDERS',   72);
	define('POPUP_SIZE_FORM',     264);
	define('POPUP_SIZE_TEXTAREA', 104);
	define('POPUP_SIZE_INPUT',     27);
	
	
	// RegExp for input/textarea
	define('FIELD_REGEXP', '/^(create|edit)_(text|input)_([a-z]{2,3})$/');
	
	
	// Errors / Success
	define('GET_RESOURCE_CREATED', 'new_rsc');
	define('ERROR_NEW_INVALID_KEY',   1); // Wrong format (New Rsc)
	define('ERROR_NEW_DUPLICATE_KEY', 2); // Key already used (New Rsc)
	define('ERROR_NEW_INVALID_TYPE',  3); // Wrong type (New Rsc)
	define('ERROR_NEW_SQL_ERROR',     4); // SQL query has failed (at least once)
	define('ERROR_SEARCH_WRONG_LANG', 5); // 
	define('ERROR_SEARCH_NO_MATCH',   6); // 
	define('ERROR_NEW_WRONG_TINY',    7); // Tiny state to false for JS/int/float
	define('ERROR_EDIT_MATCHING_RSC', 8); // Can't find the resource to edit
	define('ERROR_EDIT_CHANGE_NAME',  9); // Can't change the same cause already in used
	define('ERROR_EDIT_CHANGE_PROP', 10); // Changing resource properties error
	define('ERROR_EDIT_SQL_ERROR',   11); // SQL query has failed (at least once)
	
	define('ERROR_RES_NOT_FOUND',  'RES_NOT_FOUND');
	define('ERROR_UNABLE_DEL_RES', 'UNABLE_DEL_RES');
	
	
	// Types
	define('SQL_QUERY_TYPES', "SELECT `key`,`value`,`order`,`tiny`,`validator` from `rsc_type` ORDER BY `order`");
	define('TYPE_ALL', 'all');
	
	
	// IDs
	define('MESSAGE_PANEL_ID', 'message');
	
	
	// languages
	$foLangs = $allLangs = Lang::getAvailableLanguages(false);
	$boLangs = Lang::getAvailableLanguages(true);
	foreach($boLangs as $lang)
		if( ! in_array($lang, $allLangs) )
			$allLangs[] = $lang;
	

	// Init for forms creation
	$yes = Lang::translate('options.yes');
	$no  = Lang::translate('options.no');
	$yes_no = Array(NO => $no, YES => $yes);
	$yes_no_all = Array(ALL => Lang::translate('options.all'), NO => $no, YES => $yes);
	$padding = "\t";
	

	// Handle Error Messages for JS
	$pref = "Data.Forms.";
	$error_messages = Array( null,
		Array('form' => $pref.'Create.form',   'sender' => GET_CREATE_KEY), // 1
		Array('form' => $pref.'Create.form',   'sender' => GET_CREATE_KEY),
		Array('form' => $pref.'Create.form',   'sender' => GET_CREATE_TYPE),
		Array('form' => $pref.'Create.form',   'sender' => null),
		Array('form' => $pref.'Search.byText', 'sender' => GET_TXTSEARCH_LANG), // 5
		Array('form' => $pref.'Search.byText', 'sender' => GET_TXTSEARCH_TEXT),
		Array('form' => $pref.'Create.form',   'sender' => GET_CREATE_TINY),
		Array('form' => $pref.'Edit.form',     'sender' => null), // 8
		Array('form' => $pref.'Edit.form',     'sender' => GET_EDIT_KEY),
		Array('form' => $pref.'Edit.form',     'sender' => null),
		Array('form' => $pref.'Edit.form',     'sender' => null)
	);
	
?>