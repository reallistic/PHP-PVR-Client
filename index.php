<?php
session_start();
$response = false;
$config = false;
$query = false;
require_once("bootstrap.php");
if(class_exists("CONFIG")){
	$config = true;
	if(isset($_REQUEST['t']) && $_REQUEST['t'] != ""){
		$t = $_REQUEST['t'];
		$conf= new CONFIG;
		$hp = $conf->getHP();
		$sb = $conf->getSB();
		$cp = $conf->getCP();
		$email = $conf->getEmail();
		if($t == "hp" && ((isset($_REQUEST['artist']) && $_REQUEST['artist'] != "") || ( isset($_REQUEST['album']) && $_REQUEST['album'] != ""))){
			$query = true;
			$q = array(
					'artist'=> CONFIG::escape_query($_REQUEST['artist']),
					'album'=> CONFIG::escape_query($_REQUEST['album'])
			);
			LOG::info(__FILE__." Line[".__LINE__."]"."searching for artist/album ".$q["artist"]."/".$q["album"]);
			$lastfmRes = $conf->getMusicResults($q);
		}
		elseif($t == "cp" && isset($_REQUEST['movie']) && $_REQUEST['movie'] != ""){
			$query = true;
			$q = CONFIG::escape_query($_REQUEST['movie']);
			$couchpRes = $conf->getMovieResults($q);
		}
		elseif($t == "sb" && isset($_REQUEST['show']) && $_REQUEST['show'] != ""){
			$query = true;
			$q = CONFIG::escape_query($_REQUEST['show']);
			$sickbRes = $conf->getTvResults($q);
		}
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo CONFIG::$APPNAME; ?> | Request</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet"></link>
<link href="<?php echo $root.CONFIG::$STYLE; ?>" rel="stylesheet" type="text/css"></link>
<script type="text/javascript">
	function doSubmit(fm){
		var valid = false;
		switch(fm){
			case "hp":
				valid = ($("input[name=artist]").val() != "" || $("input[name=album]").val() != "");
			break;
			case "sb":
				valid = ($("input[name=show]").val() != "");
			break;
			case "cp":
				valid = ($("input[name=movie]").val() != "");
			break;
		}
		if(valid === true){
			$("form#"+fm).submit();
		}
		else{
			$("#info").html("Please enter a value in at least one field");
		}
	}
	function ajaxSendEmail(art, alb, t, btn){
		//define your send email function here
		if(btn != null){
			btn.disabled = true;
		}
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"line1":art,"line2":alb, "t":t, "method":"email"},
			type:"POST",
			complete: function(jqXHR, status){
				$("#info").html(status);
				$("#info").show();
				$( "#info" ).dialog();
				if(status != "success" && btn != null){
					btn.disabled = false;
				}
				else if(btn != null){
					$(btn).val("added");
				}
			}
		});
	}
	function ajaxAddMovie(imdbid, name,btn){
		//define your send email function here
		btn.disabled = true;
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"imdbid":imdbid, "name":name, "method":"cp"},
			type:"POST",
			complete: function(jqXHR, status){
					$("#info").html(status);
					$("#info").show();
					$( "#info" ).dialog();
					if(status != "success"){
						btn.disabled = false;
					}
					else{
						$(btn).val("added");
					}
			}
		});
		<?php if(isset($email) && $email["enabled"]){ ?>
			ajaxSendEmail(name, imdbid, "movie", null);
		<?php } ?>
	}
	function ajaxAddTV(tvdbid, name,btn){
		//define your send email function here
		btn.disabled = true;
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"tvdbid":tvdbid, "name":name, "method":"sb"},
			type:"POST",
			complete: function(jqXHR, status){
					$("#info").html(status);
					$("#info").show();
					$( "#info" ).dialog();
					if(status != "success"){
						btn.disabled = false;
					}
					else{
						$(btn).val("added");
					}
			}
		});
		<?php if(isset($email) && $email["enabled"]){ ?>
			ajaxSendEmail(name, tvdbid, "TV Show", null);
		<?php } ?>
	}
	function ajaxAddAlbum(alb, albname, artnm, artid,btn){
		//define your send email function here
		btn.disabled = true;
		document.getElementById(artid).disabled = true;
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"albumid":alb, "method":"hp"},
			type:"POST",
			complete: function(jqXHR, status){
					$("#info").html(status);
					$("#info").show();
					$( "#info" ).dialog();
					if(status != "success"){
						btn.disabled = false;
						document.getElementById(artid).disabled = false;
						return;
					}
					$("#"+artid).val("added");
					$(btn).val("added");
				$.ajax({
					url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
					data:{"artistid":artid, "method":"hp"},
					type:"POST",
					complete: function(jqXHR, status){
					}
				});
			}
		});
		<?php if(isset($email) && $email["enabled"]){ ?>
			ajaxSendEmail(artnm, albname, "music", null);
		<?php } ?>
	}
	function ajaxAddArtist(art, artnm, btn){
		//define your send email function here
		btn.disabled = true;
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"artistid":art, "method":"hp"},
			type:"POST",
			complete: function(jqXHR, status){
				$("#info").html(status);
				$("#info").show();
				$( "#info" ).dialog();
				if(status == "success"){
					$(btn).val("added");
				}
				else{
					btn.disabled = false;
				}
			}
		});
		<?php if(isset($email) && $email["enabled"]){ ?>
			ajaxSendEmail(artnm, "*add artist*", "music", null);
		<?php } ?>
	}
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
            <h3>Request</h3>
        </div>
        <div class="subhead">
            <a class="button" href="<?php echo $root.CONFIG::$MGMT; ?>">Manage</a>
        </div>
        <div class="subhead">
            <a class="button" href="<?php echo $root.CONFIG::$QUEUE; ?>">Status</a>
        </div>
        <div class="subhead">
        	<select name="pvrType" size="1" id="pvrType">
            	<option value="cp" <?php if($query === false || ($query === true && $t =="cp")) echo "selected"; ?>>Movie</option>
                <option value="hp" <?php if($query === true && $t =="hp") echo "selected"; ?>>Music</option>
                <option value="sb" <?php if($query === true && $t =="sb") echo "selected"; ?>>Tv Show</option>
            </select>
         </div>
        <div style="clear:both"></div>
        <hr />
        <div>
            <form <?php if($query === true && $t !="cp") echo 'style="display:none;"'; ?> id="cp" enctype="application/x-www-form-urlencoded" method="post">
            	<input type="hidden" value="cp" name="t" />
                <label>Find a Movie?
                <input type="text" name="movie" value="<?php if(isset($_REQUEST['movie'])) echo $_REQUEST['movie']; ?>" /></label>
                <button onClick="doSubmit('cp');" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>
            </form>
            
            <form <?php if($query === false || ($query === true && $t !="hp")) echo 'style="display:none;"'; ?> id="hp" enctype="application/x-www-form-urlencoded" method="post">
            	<input type="hidden" value="hp" name="t" />
                <label>Find an Artist?
                <input type="text" name="artist" value="<?php if(isset($_REQUEST['artist'])) echo $_REQUEST['artist']; ?>" /></label>
                and/or
                <label>Find an Album?
                <input type="text" name="album" value="<?php if(isset($_REQUEST['album'])) echo $_REQUEST['album']; ?>" /></label>
                <button onClick="doSubmit('hp');" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>
            </form>
            
            <form <?php if($query === false || ($query === true && $t !="sb")) echo 'style="display:none;"'; ?> id="sb" enctype="application/x-www-form-urlencoded" method="post">
                <input type="hidden" value="sb" name="t" />
                <label>Find a Tv Show?
                <input type="text" name="show" value="<?php if(isset($_REQUEST['show'])) echo $_REQUEST['show']; ?>" /></label>
                <button onClick="doSubmit('sb');" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>
            </form>
            <br />
            <div id="results">
            <?php
                if($query === false){
                    echo "<h3>Enter values above and click search</h3>";
                }
				elseif(count($lastfmRes) >0){
					$i =0;
					foreach($lastfmRes as $album){
						echo "<div class=\"result\">";
							echo "<h3>" ."<a target=\"new\" href=\"" . $album->getUrl() . "\" >". $album->getName()." - All Albums</a>". "</h3>";
							echo '<div style="clear:both"></div>';
							echo "<div class=\"resultData\">";
							echo '<div class="resultImg"><img src="' . $album->getArtistImg() . '" />';
							echo "<ul><li>Artist: ";
							if($album->isAdded()){
								echo '<input type="button" value="Added" disabled />';
							}
							elseif($hp["enabled"]){
								echo '<input id="'. $album->getArtistId().'" type="button" onClick="ajaxAddArtist(\''. $album->getArtistId().'\',\''. $album->getName() .'\', this)" value="Add" />';
							}
							elseif($email["enabled"]){
								echo '<input id="'. $album->getArtistId().'" type="button" onClick="ajaxSendEmail(\''. $album->getName().'\',\''. "general" .'\', this)" value="Add" />';
							}
							echo "</li>";
							echo "<li>Search Match: <strong>".$album->getScore()."</strong></li></ul></div>";
							
								$albums = $album->getAlbums();
								$k=0;								
								foreach ($albums as $alb){
									echo "<div>";									
									if($album->getAvailable($k) != "Skipped"){										
										echo '<div class="addBtn"><input type="button" value="Added" disabled />'.'</div>';
									}
									elseif($hp["enabled"]){
										echo '<div class="addBtn"><input type="button" onClick="ajaxAddAlbum(\''.$album->getAlbumsId($k).'\',\''. $alb .'\',\''. $album->getName() .'\',\''. $album->getArtistId() .'\', this)" value="Add" />'.'</div>';
									}
									elseif($email["enabled"]){
										echo '<div class="addBtn"><input type="button" onClick="ajaxSendEmail(\''. $album->getName().'\',\''. $alb .'\',\'Music\', this)" value="Add" />'.'</div>';
									}
									echo "<img src=\"".$album->getAlbumsArt($k)."\" />".$alb."</div>";
									$k++;
								}
							echo "</div>";
							
						echo "</div>";
						$i++;
					}
					echo '<div style="clear:both"></div>';
				}
                elseif(count($couchpRes) >0){
					echo "<table style=\"border:0; padding:0;\" border=\"0\" cellspacing=\"0\" class=\"couchpotato\">";
						?>
                        		<tr>
                                	<th><h3>Thumb</h3></th>
                                    <th><h3>Action</h3></th>
                                    <th><h3>Movie Name</h3></th>
                                    <th><h3>Year</h3></th>
                                    <th><h3>Genres</h3></th>
                                </tr>
                        <?php	
					foreach($couchpRes as $movie) {
							// show Results
							echo "<tr>";
									echo "<td><img style=\"height:100px;\" src='".$movie->getImg()."' /></td>";
									echo "<td>";
									if($movie->isAdded()){
										echo '<input type="button" value="Added" disabled />';
									}
									elseif($cp["enabled"]){
										echo "<input onclick=\"ajaxAddMovie('".$movie->getImdbId()."','".$movie->getName()."', this);\" type=\"button\" value=\"Add Movie\">";
									}
									elseif($email["enabled"]){
										echo '<input type="button" onClick="ajaxSendEmail(\''. $movie->getName().'\',\''. $movie->getImdbId() .'\',\'Movie\', this)" value="Add Movie" />';
									}
									echo "</td>";
									echo "<td><a target=\"_new\" href=\"".$movie->getUrl()."\" ><b>" . $movie->getName() . "</b></a></td>";
									echo "<td>".$movie->getStarted()."</td>";
									echo "<td>".$movie->getGenre()."</td>";
									
							echo "</tr>";
					}
					echo "</table>";
				}
				elseif(count($sickbRes) >0){
					echo "<table style=\"border:0; padding:0;\" border=\"0\" cellspacing=\"0\" class=\"sickbeard\">";
					?>
                        		<tr>
                                    <th><h3>Action</h3></th>
                                    <th><h3>Show Name</h3></th>
                                    <th><h3>Show Started</h3></th>
                                </tr>
                        <?php
					foreach($sickbRes as $show) {
						$tvdbAirDate=$show->getStarted();
							// show Results
						echo "<tr>";								
								echo "<td><img src='".$show->getImg()."' />";
								if($show->isAdded()){
									echo '<input type="button" value="Added" disabled />';
								}
								elseif($sb["enabled"]){
									echo "<input onclick=\"ajaxAddTV('".$show->getTvdbId()."','".$show->getName()."', this);\" type=\"button\" value=\"Add Show\">";
								}
								elseif($email["enabled"]){
									echo '<input type="button" onClick="ajaxSendEmail(\''. $show->getName().'\',\''. $show->getTvdbId() .'\',\'TV Show\', this)" value="Add Show" />';
								}
								echo "</td>";
								echo "<td><a target=\"_new\" href=\"".$show->getUrl()."\" ><b>" . $show->getName() . "</b></a></td>";
								echo "<td>";
										echo ( $tvdbAirDate == NULL ? '' : ' (started on: ' . $tvdbAirDate . ")" );
								echo "</td>";
						echo "</tr>";
					}
					echo "</table>";
				}
                elseif($response === false) {
                    echo "<h3>No Results</h3>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div id="info" style="display:none;" title="Notifications">
<?php
	$notify=false;
	if(isset($_SESSION['response'])){
		echo "<p>".$_SESSION['response']."</p>";
		unset($_SESSION['response']);
		$notify=true;
	}
	if($config === false){
		LOG::error(__FILE__." Line[".__LINE__."]"."config.php missing");
		echo "<h3>Improper installation. Missing config.php</h3>";
		$notify=true;
	}
	
	if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php }	?>
</div>
<script type="text/javascript">
$(function() {
	$( "input[type=button],input[type=submit], a.button, button" )
	  .button();
	$("button").click(function( event ) {
		event.preventDefault();
	});
	$("#pvrType").bind("change", function(e){
		$("form").each(function(i, elm){
			$(this).hide(300);
		});
		$("#results").hide(300);
		setTimeout(function(){
			$("#"+$("#pvrType").val()).show(300);
                        <?php if(isset($t)){ ?>
			if($("#pvrType").val() == "<?php echo $t; ?>"){
				$("#results").show(300);
			}
                        <?php } ?>
		},300);
	});
	
 });
</script>
</body>
</html>
<?php exit; ?>
