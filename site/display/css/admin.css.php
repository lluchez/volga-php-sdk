<?php
	//Context::disableCache();
?>

body { background: #E0E0E0 url('<?php echo Media::IMAGE('bg_body_login_admin'); ?>') repeat-x; }

#frame
{
	position: relative;
	width: 996px;
	margin-left: auto;
	margin-right: auto;
	text-align: left;
	border: 1px solid #888;
	background-color: white;
}

div#banner
{
	height: 115px;
	background: #447DD8 url('<?php echo Lang::IMAGE('bo_banner'); ?>') no-repeat;
	border-bottom: 1px solid #888;
}

<?php
	foreach(Lang::getAvailableLanguages() as $langCode)
		echo "body.lang_$langCode div#banner { background-image: url('".Lang::IMAGE('bo_banner', null, $langCode)."'); }\n";
?>


#set_language { float: right; margin: 3px; list-style-type: none; }
#set_language li { float: left; margin-right: 7px; }

div#menu { padding: 85px 5px 0; }
div#body { padding: 5px; }
div#body a { color: #555; }

