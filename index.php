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
				'artist'=> CONFIG::escape_query($_REQUEST['artist'])
		);
		LOG::info(__FILE__." Line[".__LINE__."]"."searching for artist/album ".$q["artist"]);
	}
	elseif(isset($_REQUEST['q']) && $_REQUEST['q'] != ""){
		$query = true;
		$q = CONFIG::escape_query($_REQUEST['q']);
		LOG::info(__FILE__." Line[".__LINE__."]"."general search for ".$q);
	}
	
	if($query === true){					 
		// search for an artist
		$lastfmRes = array();
		if($q["artist"] != "" ){
			$apikey="b0f93f5384aa9fe79f9297f6767555c7";
			$cmd = "findArtist&name=".$q["artist"];
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => "http://localhost:8181/api?apikey=$apikey&cmd=$cmd",
				CURLOPT_USERAGENT => 'Codular Sample cURL Request'
			));
			// Send the request & save response to $resp
			$results = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
			echo "<pre />";
			$results = json_decode($results);			
			print_r($results);
			exit;
			while ($artist = $results->current()) {
				
				$lfmr = new LASTFMRESULT($artist->getName());
				$lfmr->setUrl($artist->getUrl());
				$lfmr->setArtistImg($artist->getImage(4));
				$albums = Artist::getTopAlbums($artist->getName());
				$lfmr->setName($artist->getName());
				/*echo "<div>";
				echo "<h3>" . $artist->getName() . "</h3>";
				echo "<a href=\"" . $artist->getUrl() . "\" >LastFm - " .$artist->getName()."</a><br>";
				//echo '<img src="' . $artist->getImage(4) . '">';				
				echo "<ul>";*/
				foreach ($albums as $album){
					$lfmr->addAlbum($album->getName(), false, $album->getImage(4));
					//echo "<li>".$album->getName()."</li>";
				}
				/*echo "</ul>";
				echo "</div>";*/
				if(count($albums) >0){
			 		array_push($lastfmRes, $lfmr);
				}
				$artist = $results->next();
			}	
		}
		else if($q["album"] != "" && $q["artist"] == ""){
			$results = Album::search($q["album"], 5);
			while ($album = $results->current()) {
				$lfmr = new LASTFMRESULT($album->getArtist());
				$lfmr->addAlbum($album->getName(), false, $album->getImage(4));
				$artist = Artist::getInfo($album->getArtist());
				$lfmr->setArtistImg($artist->getImage(4));
				$lfmr->setUrl($album->getUrl());
				$lfmr->setName($album->getName());
				array_push($lastfmRes, $lfmr);
				
				
				/*echo "<div>";
				echo "<h3>" . $album->getArtist() . "</h3>";
				echo "<a href=\"" . $album->getUrl() . "\" >LastFm - " .$album->getName()."</a><br>";			
				//echo '<img src="' . $artist->getImage(4) . '">';
				echo "<ul>";
				echo "<li>".$album->getName()."</li>";
				echo "</ul>";
				echo "</div>";*/
			 	
				$album = $results->next();
			}
		}
		else{
			$album = Album::getInfo($q["artist"],$q["album"]);
			$lfmr = new LASTFMRESULT($album->getArtist());
			$lfmr->addAlbum($album->getName(), false, $album->getImage(4));
			$lfmr->setUrl($album->getUrl());
			$lfmr->setName($album->getName());
			$artist = Artist::getInfo($album->getArtist());
			$lfmr->setArtistImg($artist->getImage(4));
			array_push($lastfmRes, $lfmr);
		}
		$results = array();
		if(false & $indexers === true && $indexersprop === true){			
			$filter=array();
			$curls=array();
			for( $i=0; $i<count($indexsites); $i++){
				$indexsite = $indexsites[$i];
				if(!$indexsite->isEnabled()){
					continue;
				}
				array_push($curls, $indexsite->makeSearch($q));
				
				$ch = curl_init($indexsite->makeSearch($q));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$resp = curl_exec($ch);
				curl_close($ch);
				
				$xml = simplexml_load_string($resp);
				if(count($xml->channel->item) !== 0){
				   foreach ($xml->channel->item as $item):
						if(!in_array(strtolower(CONFIG::escape_query($item->title)), $filter)){
							$sr = new SEARCHRESULT();
							$sr->setLink($item->link);
							$sr->setTitle($item->title);
							$attr = $item->xpath('newznab:attr[@name="grabs"]');
							$sr->setGrabs((string)$attr[0]['value']);						
							array_push($results, $sr);
							array_push($filter, strtolower(CONFIG::escape_query($item->title)));
						}
				   endforeach;
				   
				   LOG::info(__FILE__." Line[".__LINE__."]"."found ".count($xml->channel->item)." results with site ".$indexsite->getName());
				}
				
				
			}
			if (count($results) >0){
				$response = true; //response recieved
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
	function ajaxSend(id){
		$.ajax({
			url:"<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>",
			data:$("form#"+id).serialize(),
			type:"POST",
			complete: function(jqXHR, status){
				$("#info").html(status);
				$("#info").show();
				$( "#info" ).dialog();
			}
		});
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
                if($response === true){
                    /*echo implode("<br>",$results);*/
					$i=0;
                   foreach ($results as $item): ?>
                        <form id="result<?php echo $i; ?>" enctype="application/x-www-form-urlencoded" method="post" >
                        	<input type="hidden" name="name" value="<?php echo $item->getTitle(); ?>" />
                            <input type="hidden" name="link" value="<?php echo $item->getLink(); ?>" /> 
                            <input type="hidden" name="method" value="sabnzbd" /> 
			    			<a href="<?php echo $item->getLink(); ?>" ><h3><?php echo $item->getTitle(); ?></h3></a> <strong>Grabs: <?php echo $item->getGrabs();?></strong>
                			<input type="button" onClick="ajaxSend('result<?php echo $i; ?>')" value="Send" />
                            <br />
                        </form>
                        <?php
						$i++;
                   endforeach;
                }
				elseif($query === true && count($lastfmRes) >0){
					$i =0;
					foreach($lastfmRes as $album){
						echo "<div class=\"result\">";
							echo "<h3>" ."<a target=\"new\" href=\"" . $album->getUrl() . "/+Albums\" >". $album->getArtist()." - All Albums</a>". "</h3>";
							echo '<div style="clear:both"></div>';
							echo '<div class="resultImg"><img src="' . $album->getArtistImg() . '"></div>';
							echo "<div class=\"resultData\">";
								$albums = $album->getAlbums();
								$k=0;
								
								foreach ($albums as $alb){
									echo "<div>";
									echo '<div class="addBtn"><input type="button" onClick="ajaxSendEmail(\''. $album->getArtist().'\',\''. $alb .'\')" value="Send" /></div>';
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