<?php
if(!isset($GLOBALS['config'])){
	$nonPublic = explode("/",$_SERVER['DOCUMENT_ROOT']);
	array_pop($nonPublic);
	$nonPublic = implode("/",$nonPublic);
	$GLOBALS['config'] = parse_ini_file($nonPublic."/JCDBconfig.ini");
}
?>