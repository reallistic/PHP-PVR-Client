<?php
session_start();
$config = false;
$indexers = false;
$indexersprop = false;
require_once("../bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
	}
	if(isset($_POST['usr']) && isset($_POST['pwd'])){
		$u = CONFIG::escape_query($_POST['usr']);
		$p = CONFIG::escape_query($_POST['pwd']);
		$at = new AUTH($u,$p);
		if($at->checkToken(true)){
			$_SESSION['authtoken'] = serialize($at);
		}
	}
	elseif(isset($_POST['cpwd']) && isset($at)){
		$p = CONFIG::escape_query($_POST['cpwd']);
		$ret = $at->confirm($p);
	}
	
	if(isset($at)){
		if(!$at->checkToken() && $at->info[1] !="confirm"){
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			header("location: $url");

		}
		elseif($at->checkToken()){
			if(is_file($sroot.CONFIG::$DBS.INDEXSITE::$dbfile)){
				$indexers = true; //check for indexers was good
				$inxs = file_get_contents($sroot.CONFIG::$DBS.INDEXSITE::$dbfile);
				$indexsites = unserialize($inxs);
				if(is_array($indexsites)){
					$indexersprop = true; //check for indexsites class was good
				}
			}
			
			$conf = new CONFIG;
			$sab = $conf->getSab();
			$email = $conf->getEmail();
			$error = false;
		}
	}
	else{
		$error = true;
	}
	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo CONFIG::$APPNAME; ?> | Manage</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet" type="text/css"></link>
<link href="<?php echo $root.CONFIG::$STYLE; ?>" rel="stylesheet" type="text/css"></link>
<script>
  $(function() {
    $( "#dialog" ).dialog();
  });
  $(function() {
		$("input[type=submit], button, a.button" )
		  .button();
	 });
</script>
</head>

<body>
<?php 
if($at ==NULL || (!$at->checkToken() && $at->info[1]!="confirm")){ ?>
	<div id="dialog" title="Login">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input type="text" name="usr" value="" />
            </label>
            <br />
            <label>Password:<br />
                <input type="password" name="pwd" value="" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
}
elseif(isset($at) && $at->info[1]=="confirm"){ ?>
	<div id="dialog" title="Confirm new credentials">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input disabled type="text" name="usr" value="<?php echo $at->getUsername(); ?>" />
            </label>
            <br />
            <label>Confirm Password:<br />
                <input type="password" name="cpwd" value="" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
} 
else{
	include("settings.php");
} ?>
<div id="info" title="Notifications">
<?php
	$notify=false;
	if(isset($_SESSION['response'])){
		echo "<p>".$_SESSION['response']."</p>";
		unset($_SESSION['response']);
		$notify=true;
	}
	if($config === false){
		$notify=true;
		echo "<p>Improper installation. Missing config.php</p>";
	}
	if(isset($at) && $indexersprop === false){
		$notify = true;
		echo "<p>Please add an index site</p>";
	}
if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php } ?>
</div>
</body>
</html>