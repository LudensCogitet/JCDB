<?php
if(!isset($GLOBALS['config'])){
	set_include_path(get_include_path().PATH_SEPARATOR.getenv("JCDB_CONFIG_PATH"));
	$GLOBALS['config'] = parse_ini_file("JCDBconfig.ini");
}
?>