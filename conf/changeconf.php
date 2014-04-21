<?php
session_start();
ob_start();
$config = false;
$indexers = false;
require_once("../bootstrap.php");
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
if(class_exists("CONFIG")){
	$config = true;
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() || !isset($_POST['t'])){
			LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			header("location: $url");
		}
		else{
			$t = CONFIG::escape_query($_POST['t']);
			$n = CONFIG::escape_query($_POST['name']);
			$u = CONFIG::escape_query($_POST['url']);
			$a = CONFIG::escape_query($_POST['apikey']);
			$nu = CONFIG::escape_query($_POST['usr']);
			$np = CONFIG::escape_query($_POST['pwd']);
			$ind = intval(CONFIG::escape_query($_POST['index']));
			$mthd = CONFIG::escape_query($_POST['method']);
			$port = CONFIG::escape_query($_POST['port']);
			$https = (CONFIG::escape_query($_POST['https']) == "true");
			$enabled = (CONFIG::escape_query($_POST['enabled']) == "true");
			$category = CONFIG::escape_query($_POST['cat']);
			$to = CONFIG::escape_query($_POST['to']);
			$from = CONFIG::escape_query($_POST['from']);
			$sub = CONFIG::escape_query($_POST['subject']);
			$bklg = CONFIG::escape_query($_POST['bklog']);
			$error = false;
			switch($t){
				case "hp":
					if($mthd == "edit"){						
						$s = array(
							"server"=>$u,
							"apikey"=>$a,
							"port"=>$port,
							"enabled"=>$enabled,
							"https"=>$https,
							"bklog"=>$bklg
						);
						$conf = new CONFIG;
						$conf->saveHPConfig($s);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed HP config");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				case "cp":
					if($mthd == "edit"){						
						$s = array(
							"server"=>$u,
							"apikey"=>$a,
							"port"=>$port,
							"enabled"=>$enabled,
							"https"=>$https
						);
						$conf = new CONFIG;
						$conf->saveCPConfig($s);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed CP config");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				case "sb":
					if($mthd == "edit"){						
						$s = array(
							"server"=>$u,
							"apikey"=>$a,
							"port"=>$port,
							"enabled"=>$enabled,
							"https"=>$https,
							"bklog"=>$bklg
						);
						$conf = new CONFIG;
						$conf->saveSBConfig($s);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed SB config");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				case "email":
					if($mthd == "edit"){						
						$s = array(
							"enabled"=>$enabled,
							"to"=>$to,
							"from"=>$from,
							"subject"=>$sub
						);
						$conf = new CONFIG;
						$conf->saveMailConfig($s);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed email config");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				case "credentials":
					if($mthd == "edit"){
						$_SESSION['response'] = $at->changeAuth($nu, $np);
						$_SESSION['authtoken'] = serialize($at);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed login credentials");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				case "lastfm":
					if($mthd == "edit"){
						$conf = new CONFIG;
						$conf->saveLastfmApikey($a);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed last.fm apikey");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
						header("location: $url");

					}
				break;
				default:
					LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
					$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
					header("location: $url");

				break;
			}
		}
	}
	else{
		LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
		$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
		header("location: $url");

	}
}
$url = $root.CONFIG::$MGMT;
header("location: $url");
ob_end_clean();
exit;
?>