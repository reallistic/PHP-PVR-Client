<?php
session_start();
$response = false;
$indexers = false;
$indexersprop = false;
$config = false;
$query = false;
require_once("bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	if(is_file(CONFIG::$DBS.INDEXSITE::$dbfile)){
		$indexers = true; //check for indexers was good
		$inxs = file_get_contents(CONFIG::$DBS.INDEXSITE::$dbfile);
		$indexsites = unserialize($inxs);
		$indexersprop = is_array($indexsites); //check for indexsites class was good
	}
	if((isset($_REQUEST['artist']) && $_REQUEST['artist'] != "") || ( isset($_REQUEST['album']) && $_REQUEST['album'] != "")){
		$query = true;
		$q = array(
				'artist'=> CONFIG::escape_query($_REQUEST['artist']),
				'album'=> CONFIG::escape_query($_REQUEST['album'])
		);
		LOG::info(__FILE__." Line[".__LINE__."]"."searching for artist/album ".$q["artist"]."/".$q["album"]);
	}
	
	if($query === true){					 
		// search for an artist
		$lastfmRes = array();
		$conf= new CONFIG;
		$hp=$conf->getHP();
		$email = $conf->getEmail();
		$lastfmapikey=$conf->getLastfmApiKey();
		if($q["artist"] != "" && $q["album"] == ""){
			$cmd = "findArtist&name=".urlencode($q["artist"]);
			if($hp["https"] === true){
				$getArtistUrl = "https://";
			}
			else{
				$getArtistUrl = "http://";
			}
			$getArtistUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
			$ch = curl_init($getArtistUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$results = curl_exec($ch);
			curl_close($ch);
			$results = json_decode($results);
			foreach($results as $resultObj) {
				if(intval($resultObj->score) <75){
					continue;
				}
				$cmd = "getArtist&id=".$resultObj->id;
				if($hp["https"] === true){
					$getAlbumsUrl = "https://";
				}
				else{
					$getAlbumsUrl = "http://";
				}
				$getAlbumsUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
				$ch = curl_init($getAlbumsUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$artistinfo = curl_exec($ch);
				curl_close($ch);
				$artistinfo = json_decode($artistinfo);
				
				if(count($artistinfo->artist) > 0){
					$artist = $artistinfo->artist[0];
					$albums = $artistinfo->albums;
					$added = true;
					$lfmr = new MBRESULT($artist->ArtistName, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($artist->ThumbURL);
					$lfmr->setName($artist->ArtistName);
					$lfmr->setArtistId($resultObj->id);
					foreach ($albums as $album){
						$lfmr->addAlbum($album->AlbumTitle, $album->Status, $album->ThumbURL, $album->AlbumID);
					}
				}
				else{
					$added = false;
					$cmd = "artist.getinfo&mbid=".$resultObj->id;
					$curl = curl_init();
					$getArtistInfo = "http://ws.audioscrobbler.com/2.0/?method=$cmd&api_key=$lastfmapikey&format=json";
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $getArtistInfo,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$artistinfo = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$artist = json_decode($artistinfo)->artist;
					$cmd = "artist.gettopalbums&mbid=".$resultObj->id;
					$curl = curl_init();
					$getArtistInfo = "http://ws.audioscrobbler.com/2.0/?method=$cmd&api_key=$lastfmapikey&format=json";
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $getArtistInfo,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$artistinfo = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$albums = json_decode($artistinfo)->topalbums->album;
					$lfmr = new MBRESULT($artist->name, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($artist->image[3]->{'#text'});
					$lfmr->setName($artist->name);
					$lfmr->setArtistId($resultObj->id);
					foreach ($albums as $album){
						if($album->mbid != ""){						
							$lfmr->addAlbum($album->name, "Skipped", $album->image[3]->{'#text'}, $album->mbid);
						}
					}
				}
				if(count($lfmr->getAlbums())>0){
			 		array_push($lastfmRes, $lfmr);
				}
			}
		}
		else if($q["album"] != "" && $q["artist"] == ""){
			$cmd = "findAlbum&name=".urlencode($q["album"]);
			if($hp["https"] === true){
				$getArtistUrl = "https://";
			}
			else{
				$getArtistUrl = "http://";
			}
			$getArtistUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
			$ch = curl_init($getArtistUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$results = curl_exec($ch);
			curl_close($ch);
			$results = json_decode($results);
			foreach($results as $resultObj) {
				
				if(intval($resultObj->score) <80){
					continue;
				}
				$cmd = "getAlbum&id=".$resultObj->albumid;
				if($hp["https"] === true){
					$getAlbumsUrl = "https://";
				}
				else{
					$getAlbumsUrl = "http://";
				}
				$getAlbumsUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
				$ch = curl_init($getAlbumsUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$artistinfo = curl_exec($ch);
				curl_close($ch);
				$artistinfo = json_decode($artistinfo);
				
				if(count($artistinfo->album) > 0){
					$album = $artistinfo->album[0];
					$added = true;
					$lfmr = new MBRESULT($album->ArtistName, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($artist->ThumbURL);
					$lfmr->setName($album->ArtistName);
					$lfmr->setArtistId($resultObj->id);
					$lfmr->addAlbum($album->AlbumTitle, $album->Status, $album->ThumbURL, $album->AlbumID);
				}
				else{
					$added = false;
					$cmd = "album.getinfo&mbid=".$resultObj->albumid;
					$curl = curl_init();
					$getAlbumInfo = "http://ws.audioscrobbler.com/2.0/?method=$cmd&api_key=$lastfmapikey&format=json";
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $getAlbumInfo,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$albuminfo = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$album = json_decode($albuminfo)->album;
					$lfmr = new MBRESULT($album->artist, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($album->image[3]->{'#text'});
					$lfmr->setName($album->artist);
					$lfmr->setArtistId($resultObj->id);					
					$lfmr->addAlbum($album->name, "Skipped", $album->image[3]->{'#text'}, $album->mbid);
				}
				if(count($lfmr->getAlbums())>0){
			 		array_push($lastfmRes, $lfmr);
				}
			}
		}
		else{
			$cmd = "findArtist&name=".urlencode($q["artist"]);
			if($hp["https"] === true){
				$getArtistUrl = "https://";
			}
			else{
				$getArtistUrl = "http://";
			}
			$getArtistUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
			$ch = curl_init($getArtistUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$results = curl_exec($ch);
			curl_close($ch);
			$results = json_decode($results);
			foreach($results as $resultObj) {
				if(intval($resultObj->score) <75){
					continue;
				}
				$cmd = "getArtist&id=".$resultObj->id;
				if($hp["https"] === true){
					$getAlbumsUrl = "https://";
				}
				else{
					$getAlbumsUrl = "http://";
				}
				$getAlbumsUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
				$ch = curl_init($getAlbumsUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$artistinfo = curl_exec($ch);
				curl_close($ch);
				$artistinfo = json_decode($artistinfo);
				
				if(count($artistinfo->artist) > 0){
					$artist = $artistinfo->artist[0];
					$albums = $artistinfo->albums;
					$added = true;
					$lfmr = new MBRESULT($artist->ArtistName, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($artist->ThumbURL);
					$lfmr->setName($artist->ArtistName);
					$lfmr->setArtistId($resultObj->id);
					foreach ($albums as $album){
						if(strpos(strtolower($album->AlbumTitle),strtolower($q["album"])) !== false){
							$lfmr->addAlbum($album->AlbumTitle, $album->Status, $album->ThumbURL, $album->AlbumID);
						}
					}
				}
				else{
					$added = false;
					$cmd = "artist.getinfo&mbid=".$resultObj->id;
					$curl = curl_init();
					$getArtistInfo = "http://ws.audioscrobbler.com/2.0/?method=$cmd&api_key=$lastfmapikey&format=json";
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $getArtistInfo,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$artistinfo = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$artist = json_decode($artistinfo)->artist;
					$cmd = "artist.gettopalbums&mbid=".$resultObj->id;
					$curl = curl_init();
					$getArtistInfo = "http://ws.audioscrobbler.com/2.0/?method=$cmd&api_key=$lastfmapikey&format=json";
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $getArtistInfo,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$artistinfo = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$albums = json_decode($artistinfo)->topalbums->album;
					$lfmr = new MBRESULT($artist->name, $added);
					$lfmr->setUrl("http://musicbrainz.org/artist/".$resultObj->id);
					$lfmr->setScore($resultObj->score);
					$lfmr->setArtistImg($artist->image[3]->{'#text'});
					$lfmr->setName($artist->name);
					$lfmr->setArtistId($resultObj->id);
					foreach ($albums as $album){
						if($album->mbid != "" && strpos(strtolower($album->name),strtolower($q["album"])) !== false){						
							$lfmr->addAlbum($album->name, "Skipped", $album->image[3]->{'#text'}, $album->mbid);
						}
					}
				}
				if(count($lfmr->getAlbums()) >0){
			 		array_push($lastfmRes, $lfmr);
				}
			}
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
	$(function() {
		$( "input[type=button],input[type=submit], a.button, button" )
		  .button();
		$("button").click(function( event ) {
			event.preventDefault();
		});
	 });
	function doSubmit(){
		if($("input[name=q]").val() != "" || $("input[name=artist]").val() != "" || $("input[name=album]").val() != ""){
			$("form#srch").submit();
		}
		else{
			$("#info").html("Please enter a value in one of the fields");
		}
	}
	function ajaxSendEmail(art, alb){
		//define your send email function here
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"artist":art,"album":alb, "method":"email"},
			type:"POST",
			complete: function(jqXHR, status){
				$("#info").html(status);
				$("#info").show();
				$( "#info" ).dialog();
			}
		});
	}
	function ajaxAddAlbum(alb, albname, artnm, artid){
		//define your send email function here
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"albumid":alb, "method":"hp"},
			type:"POST",
			complete: function(jqXHR, status){
				if(status != "success"){
					$("#info").html(status);
					$("#info").show();
					$( "#info" ).dialog();
					return;
				}
				$.ajax({
					url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
					data:{"artistid":artid, "method":"hp"},
					type:"POST",
					complete: function(jqXHR, status){
						$("#info").html(status);
						$("#info").show();
						$( "#info" ).dialog();
					}
				});
			}
		});
		<?php if($email["enabled"]){ ?>
			ajaxSendEmail(artnm, albname);
		<?php } ?>
	}
	function ajaxAddArtist(art, artnm){
		//define your send email function here
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:{"artistid":art, "method":"hp"},
			type:"POST",
			complete: function(jqXHR, status){
				$("#info").html(status);
			}
		});
		<?php if($email["enabled"]){ ?>
			ajaxSendEmail(artnm, "general");
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
        <div style="clear:both"></div>
        <hr />
        <div>
            <form id="srch" enctype="application/x-www-form-urlencoded" method="post">
            <label>Find an Artist?
            <input type="text" name="artist" /></label>
            and/or
            <label>Find an Album?
            <input type="text" name="album" /></label>
            <br/>
            </form>
            <button onClick="doSubmit();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>
            <br />
            
            <br />
            <div id="results">
            <?php
                if($query === true && count($lastfmRes) >0){
					$i =0;
					foreach($lastfmRes as $album){
						echo "<div class=\"result\">";
							echo "<h3>" ."<a target=\"new\" href=\"" . $album->getUrl() . "\" >". $album->getName()." - All Albums</a>". "</h3>";
							echo '<div style="clear:both"></div>';
							echo '<div class="resultImg"><img src="' . $album->getArtistImg() . '" />';
							if($album->isAdded()){
								echo '<input type="button" value="Added" disabled />';
							}
							elseif($hp["enabled"]){
								echo '<input type="button" onClick="ajaxAddArtist(\''. $album->getArtistId().'\',\''. $album->getName() .'\')" value="Add" />';
							}
							elseif($email["enabled"]){
								echo '<input type="button" onClick="ajaxSendEmail(\''. $album->getName().'\',\''. "general" .'\')" value="Add" />';
							}
							echo "<strong>".$album->getScore()."</strong></div>";
							echo "<div class=\"resultData\">";
								$albums = $album->getAlbums();
								$k=0;								
								foreach ($albums as $alb){
									echo "<div>";									
									if($album->getAvailable($k) != "Skipped"){										
										echo '<div class="addBtn"><input type="button" value="Added" disabled />'.'</div>';
									}
									elseif($hp["enabled"]){
										echo '<div class="addBtn"><input type="button" onClick="ajaxAddAlbum(\''.$album->getAlbumsId($k).'\',\''. $alb .'\',\''. $album->getName() .'\',\''. $album->getArtistId() .'\')" value="Add" />'.'</div>';
									}
									elseif($email["enabled"]){
										echo '<div class="addBtn"><input type="button" onClick="ajaxSendEmail(\''. $album->getName().'\',\''. $alb .'\')" value="Add" />'.'</div>';
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
                elseif($query === false){
                    echo "<h3>Enter values above and click search</h3>";
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
		$notify=true;
	}
	if($config === false){
		LOG::error(__FILE__." Line[".__LINE__."]"."config.php missing");
		echo "<h3>Improper installation. Missing config.php</h3>";
		$notify=true;
	}
	elseif($indexers === false){
		LOG::warn(__FILE__." Line[".__LINE__."]"."couldn't find any indexers");
		echo "Please configure at least one index site<a class=\"button\" href=\"". $root.CONFIG::$MGMT."\" >Manage</a>";
		$notify=true;
	}
	elseif($indexersprop === false){
		LOG::warn(__FILE__." Line[".__LINE__."]"."couldn't find any proper indexers");
		echo "index site db curropted please repair<a class=\"button\" href=\"". $root.CONFIG::$MGMT."\" >Manage</a>";
		$notify=true;
	}
	
	if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php }
	//unset($_SESSION['response']);
	exit;
	?>
</div>
</body>
</html>