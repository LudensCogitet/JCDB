<?php
if(!isset($GLOBALS['config'])){
	$GLOBALS['config'] = parse_ini_file(getenv('CONFIG_PATH'));
}
?>