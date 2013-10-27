<?php
session_start();
ob_start();
require_once("../bootstrap.php");
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
$_SESSION['response'] = "failed";
if(class_exists(CONFIG)){
	LOG::info(__FILE__." Line[".__LINE__."]"." notify script: ".$_REQUEST['method'].$_REQUEST['artist'].$_REQUEST['album']);
	if(isset($_REQUEST['link']) && isset($_REQUEST['name']) && isset($_REQUEST['method'])){
		LOG::info(__FILE__." Line[".__LINE__."]"." notify script: in sabnzbd");
		$l = urlencode($_REQUEST['link']);
		$n = urlencode($_REQUEST['name']);
		$md = CONFIG::escape_query($_REQUEST['method']);
		$conf = new CONFIG;
		if($md == "sabnzbd"){			
			$resp = $conf->sendToSab($l, $n);
			$_SESSION['response'] = $resp;
		}
		elseif($md == "email"){
			echo $conf->sendToMail($n, $l);
		}
		else{
			LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			//header("location: $url");

		}
	}
	elseif(isset($_REQUEST['method']) && isset($_REQUEST['artist']) && isset($_REQUEST['album'])){
		LOG::info(__FILE__." Line[".__LINE__."]"." notify script: in email");
		$md = CONFIG::escape_query($_REQUEST['method']);
		$artist = CONFIG::escape_query($_REQUEST['artist']);
		$album = CONFIG::escape_query($_REQUEST['album']);
		
		if($md == "email"){
			$conf = new CONFIG;
			$resp = $conf->sendToMail($artist, $album);
			if($resp){
				
				$_SESSION['response'] = "success";
			}
			else{
				$_SESSION['response'] = "failed";
			}
			LOG::info(__FILE__." Line[".__LINE__."]"." notify script resp: ".$resp);
		}
		else{
			
			LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			//header("location: $url");
		}
	}
	else{
		LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
		$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
		///header("location: $url");

	}
}
ob_end_clean();
exit;
?>