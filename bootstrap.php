<?php
	$root = "";
	$root = $_SERVER['DOCUMENT_ROOT'];
	$root = substr(dirname(__FILE__),strlen($root));
	$root =$root."/";
	$sroot = dirname(__FILE__)."/";
	
	//CHANGE ME: Path to config.php
	$CONFIGFILE = "lib/config.php";
	require($sroot.$CONFIGFILE);
	
	if(class_exists(CONFIG)){
		if ($handle = opendir($sroot.CONFIG::$CLASSES)) {
			while (false !== ($file = readdir($handle))) {
				if(is_file($sroot.CONFIG::$CLASSES.$file)
					&& $sroot.CONFIG::$CLASSES.$file != $sroot.$CONFIGFILE
					&& $file != ".htaccess"){
					require($sroot.CONFIG::$CLASSES.$file);
				}
			}
		}
	}
?>