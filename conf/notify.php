<?php
session_start();
ob_start();
require_once("../bootstrap.php");
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
$_SESSION['response'] = "failed";
if(class_exists("CONFIG")){
	$l2 = CONFIG::escape_query($_REQUEST['line2']);
	$md = CONFIG::escape_query($_REQUEST['method']);
	$l1 = CONFIG::escape_query($_REQUEST['line1']);
	$albumid = CONFIG::escape_query($_REQUEST['albumid']);
	$artistid = CONFIG::escape_query($_REQUEST['artistid']);
	$imdbid = CONFIG::escape_query($_REQUEST['imdbid']);
	$tvdbid = CONFIG::escape_query($_REQUEST['tvdbid']);
	$l = urlencode($_REQUEST['link']);
	$n = urlencode($_REQUEST['name']);
	$t = CONFIG::escape_query($_REQUEST['t']);
	$conf = new CONFIG;
	LOG::info(__FILE__." Line[".__LINE__."]"." notify script: in $md");	
	switch($md){
		case "sab":
			if($l != "" && $n != ""){	
				$resp = $conf->sendToSab($l, $n);
				$_SESSION['response'] = $resp;
			}
			else{
				LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
				$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			}
		break;
		case "email":
			if($l1 != "" && $l2 != ""){
				$resp = $conf->sendToMail($l1, $l2, $t);
				if($resp){					
					$_SESSION['response'] = "success";
				}
				else{
					$_SESSION['response'] = "failed with msg: $resp";
				}
			}
			else{
				LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
				$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;	
			}
		break;
		case "hp":
			if($artistid != ""){
				$resp = $conf->sendToHP($artistid);
				if($resp == "OK|OK"){				
					$_SESSION['response'] = "success";
				}
				else{
					$_SESSION['response'] = "failed with msg: $resp";
				}
				LOG::info(__FILE__." Line[".__LINE__."]"." notify script resp: ".$resp);
			}
			elseif($albumid != ""){
				$resp = $conf->sendToHPAlb($albumid);
				if($resp == "OK | OK"){				
					$_SESSION['response'] = "success";
				}
				else{
					$_SESSION['response'] = "failed with msg: $resp";
				}
				LOG::info(__FILE__." Line[".__LINE__."]"." notify script resp: ".$resp);
			}
			else{
				LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
				$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			}
		break;
		case "sb":
			if($tvdbid != ""){
				$resp = $conf->sendToSB($tvdbid);
				if($resp == "OK"){				
					$_SESSION['response'] = "success";
				}
				else{
					$_SESSION['response'] = "failed with msg: $resp";
				}
				LOG::info(__FILE__." Line[".__LINE__."]"." notify script resp: ".$resp);
			}
			else{
				LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
				$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			}
		break;
		case "cp":
			if($imdbid != ""){
				$resp = $conf->sendToCP($imdbid);
				if($resp == "OK"){				
					$_SESSION['response'] = "success";
				}
				else{
					$_SESSION['response'] = "failed with msg: $resp";
				}
				LOG::info(__FILE__." Line[".__LINE__."]"." notify script resp: ".$resp);
			}
			else{
				LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
				$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			}
		break;
		default:
			LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
		break;
	}
}
ob_end_clean();
exit;
?>