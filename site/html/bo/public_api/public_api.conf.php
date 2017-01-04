<?php
	Context::updateViewingMode(Context::POPUP);
	Head::setFullTitle('Volga API');
	Head::setFavIcon(Media::IMAGE('favicon_info'));
	
	
	include "API.classes.php";

	// Method used to sort the classes
	function cmp($class_a, $class_b)
	{
		return ($class_a->name < $class_b->name) ? -1 : 1;
	}
	
	// Return the file of files (PHP classes files)
	function getClassFiles()
	{
		$files = Array();
		if( $dir = @opendir(DIR_CLASSES) )
		{
			while (($file = readdir($dir)) !== false)
				if( preg_match("/\.class\.php$/", $file))
					$files[] = $file;
			closedir($dir);
		}
		if( count($files) )
			sort($files);
		return $files;
	}
	
	
	// Read 
	function getClasses()
	{
		$files = getClassFiles();
		$classes = Array();
		foreach($files as $file)
		{
			if( $fd = @fopen(DIR_CLASSES.$file, 'r') )
			{
				$buffer = Array();
				while(! feof($fd) )
				{
					$line = fgets($fd, 4096);
					if( preg_match("#(-){5,} End of Class (-){5,}#i", $line) )
					{
						$classes[] = new APIClass($file, $buffer, &$classes);
						$buffer = Array();
					}
					else
						$buffer[] = $line;
				}
				if( count($buffer) )
					$classes[] = new APIClass($file, $buffer, &$classes);
				fclose($fd);
			}
		}
		
		usort($classes, 'cmp');
		
		return $classes;
	}
	

	$VolgaClasses = getClasses();

	
	$list = Array();
	foreach($VolgaClasses as $class)
		$list[] = $class->name;
	Head::setLinkedJSProperties(Array( JS_PARAM_PHP_CLASSES => implode(',', $list) ));
	Head::addJS('scriptaculous.main');
?>