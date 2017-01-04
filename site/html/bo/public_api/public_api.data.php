<?php

define('LINK_SUFFIX', 'Anchor');
define('TOP_LINK_SUFFIX', 'Top');
define('DIV_SUFFIX', 'Class');
define('API_SUFFIX', 'Volga_');


define('JS_PARAM_PHP_CLASSES', 'php_cls');
define('JS_PARAM_SECTIONS',    'sections');


$API_sections = Array('Introduction','Features','Installation','Description');

if( ! Common::isLocalContext() )
	array_pop($API_sections);

?>