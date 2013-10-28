<?php
session_start();
$config = false;
require_once("../bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	$conf = new CONFIG;
	$history = $conf->getHPHistory();
	$history = json_decode($history);
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
    <input type="text" value="Search" id="historySearch" />
    <hr />
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
                <td>Date Added</td>
                <td>Status</td>
            </tr>
			<?php
			foreach($history as $hitm){ ?>
				<tr class="historyTr">
                	
                	<td class="historyTitle"><?php echo $hitm->Title; ?></td>
                    <?php if(round(intval($hitm->Size)/(1024.0*1024.0),2) >1024){ ?>
                    <td><?php echo round(intval($hitm->Size)/(1024.0*1024.0*1024.0),2); ?>GB</td>
                    <?php }else{ ?>
                    <td><?php echo round(intval($hitm->Size)/(1024.0*1024.0),2); ?>MB</td>
                    <?php } ?>
                    <td><?php echo $hitm->DateAdded; ?></td>
                    <td><?php echo $hitm->Status; ?></td>
                </tr>
	  <?php } ?>
            </table> <?php
		}
		?>
    </div>
</div>
<script type="text/javascript">
	(function(){
		$("#historySearch").bind("keyup change",function(e){
			setTimeout(function(){
				var val = $("#historySearch").val();
				if(val != ""){
					$(".historyTitle").each(function(index, element) {
						if($(element).text().toLowerCase().indexOf(val.toLowerCase()) ==-1){
							$($(".historyTr")[index]).hide();
						}
						else{
							$($(".historyTr")[index]).show();
						}
					});
				}
			},0);
		});
		$("#historySearch").bind("blur focus",function(e){
			setTimeout(function(){
				var val = $("#historySearch").val();
				if(val == ""){
					$("#historySearch").val("Search");
					$(".historyTr").each(function(index, element) {
                    	$(element).show();
                	});
				}
				else if(val == "Search"){
					$("#historySearch").val("");
				}
			},0);
		});
	})();
</script>
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