<?php

$extensions = get_loaded_extensions();
sort($extensions);
$php_info = Array (
	'php_version' => phpversion(),
	'extensions'  => $extensions
);

$items = Array
(
	Array (
		'elt' => $_SESSION,
		'key' => 'session',
		'title' => '$_SESSION - '.session_id()
	),

	Array (
		'elt' => $_SERVER,
		'key' => 'server',
		'title' => '$_SERVER'
	),
	
	Array (
		'elt' => $php_info,
		'key' => 'phpinfolight',
		'title' => 'PHP Info light'
	),
	
	Array (
		'elt' => 'phpinfo();',
		'key' => 'phpinfo',
		'title' => 'phpinfo()'
	)
);

?>