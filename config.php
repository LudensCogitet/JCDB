<?php
  if(!isset($GLOBALS['_JCDB_config'])){
    set_include_path(get_include_path().PATH_SEPARATOR.$_SERVER['DOCUMENT_ROOT']);
  	set_include_path(get_include_path().PATH_SEPARATOR.getenv("JCDB_CONFIG_PATH"));
  	$GLOBALS['_JCDB_config'] = parse_ini_file("JCDBconfig.ini");
  }
?>
