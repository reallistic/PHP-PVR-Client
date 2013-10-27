<?php
session_start();
$config = false;
require_once("../bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	$conf = new CONFIG;
	$sab = $conf->getSab();
	$sabqueue = $conf->getSabQueue();
	$sabhist = $conf->getSabHistory();
	$qxml = simplexml_load_string($sabqueue);
	$hxml = simplexml_load_string($sabhist);
	if(count($qxml->slots->slot) !== 0){
		$queue = array();
		foreach ($qxml->slots->slot as $item):
			if(strtolower($item->cat) == strtolower($sab["category"]) || $item->cat == "*"){
				$qitm["status"] = $item->status;
				$qitm["eta"] = $item->eta;
				$qitm["size"] = $item->size;
				$qitm["percent"] = $item->percentage;
				$qitm["name"] = $item->filename;
				array_push($queue, $qitm);
			}
		endforeach;
	}
	if(count($hxml->slots->slot) !== 0){
		$history = array();
		foreach ($hxml->slots->slot as $item):
			if(strtolower($item->category) == strtolower($sab["category"]) || $item->category == "*"){
				$hitm["status"] = $item->status;
				$hitm["msg"] = $item->fail_message;
				$hitm["size"] = $item->size;
				$hitm["name"] = $item->name;
				array_push($history, $hitm);
			}
		endforeach;
	}
	$error = false;	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo CONFIG::$APPNAME; ?> | Status</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet" type="text/css"></link>
<link href="<?php echo $root.CONFIG::$STYLE; ?>" rel="stylesheet" type="text/css"></link>
<script>
  $(function() {
		$("input[type=submit], button, a.button" )
		  .button();
	 });
</script>
</head>

<body>
<div class="outerHead">
	<div class="head">
    	<?php echo CONFIG::$APPNAME; ?>
    </div>
</div>
<div class="mainCont">
	
<div class="innerCont">
	<div class="subhead">
    	<h3>Config</h3>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$REQ; ?>">Request</a>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$MGMT; ?>">Manage</a>
    </div>
    <div style="clear:both"></div>
    <hr />
    <div>
        <a name="queue"></a>
        <h4>Queued:</h4>
        <?php
		if(count($queue) ==0){
			echo "Nothing in queue";
		}
		else{ ?>
			<table> 
			<tr>
            	<td>Name</td>
                <td>Size</td>
                <td>ETA</td>
                <td>Percent</td>
                <td>Status</td>
            </tr>
			<?php
			foreach($history as $qitm){ ?>
				<tr>
                <td><?php echo $qitm["name"]; ?></td>
                    <td><?php echo $qitm["size"]; ?></td>
                    <td><?php echo $qitm["eta"]; ?></td>
                    <td><?php echo $qitm["percent"]; ?></td>
                    <td><?php echo $qitm["status"]; ?></td>
                </tr>
	  <?php } ?>
            </table> <?php
		}
		?>
    </div>
     <div>
        <a name="history"></a>
        <h4>History:</h4>
        <?php
		if(count($history) ==0){
			echo "Nothing in History";
		}
		else{ ?>
			<table> 
			<tr>
            	<td>Name</td>
                <td>Size</td>
                <td>Message:</td>
                <td>Status</td>
            </tr>
			<?php
			foreach($history as $hitm){ ?>
				<tr>
                	
                	<td><?php echo $hitm["name"]; ?></td>
                    <td><?php echo $hitm["size"]; ?></td>
                    <td><?php echo $hitm["msg"]; ?></td>
                    <td><?php echo $hitm["status"]; ?></td>
                </tr>
	  <?php } ?>
            </table> <?php
		}
		?>
    </div>
</div>
</div>
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