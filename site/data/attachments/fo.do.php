<?php
/** Be carefull to clean your cache for debugging, 
	otherwise files won't be downloaded again, but taken from cache */

$page = Context::getPageCode();

if( preg_match("#^progs\.([^\.]+\.[[:alnum:]]+)$#", $page, $res) )
{
	$file = String::safeSQL($res[1]);
	DB::update("UPDATE `program` SET `pDownloads`=`pDownloads`+1 WHERE `pFilename`='$file'");
}
?>