<?php
class CONFIG{
	private $sab = array(
		"server" => "0.0.0.0",
		"apikey" =>"",
		"port" => "8080",
		"category" => "music",
		"enabled" => true,
		"https" => false
	);
	private $hp = array(
		"server" => "0.0.0.0",
		"apikey" =>"",
		"port" => "8181",
		"enabled" => true,
		"https" => false
	);
	private $sb = array(
		"server" => "0.0.0.0",
		"apikey" =>"",
		"port" => "8081",
		"enabled" => true,
		"https" => false
	);
	private $cp = array(
		"server" => "0.0.0.0",
		"apikey" =>"",
		"port" => "5050",
		"enabled" => true,
		"https" => false
	);
	private $email = array(
		"enabled"=>false,
		"to" => "admin@localhost",
		"from" => "admin@localhost",
		"subject" => "new request"
	);
	
	public $info;
	public static $dbfile = "config.db";
	public static $SCRIPTS = "conf/";
	public static $CLASSES = "lib/";
	public static $STYLE = "html/style.css";
	public static $DBS = "db/";
	public static $LOGS = "log/";
	public static $REQ = "";
	public static $MGMT = "manage/";
	public static $QUEUE = "queue/";
	public static $CHSCRIPT = "changeconf.php";
	public static $NTYSCRIPT = "notify.php";
	public static $LGOUTSCRIPT = "logout.php";
	public static $LOGSTOKEEP = 5; //CHANGE ME: number of logs to keep
	public static $MAXLOGSIZE = 2097152; //CHANGE ME: max log file size (2MB)
	public static $APPNAME = "PVR {PHP}"; //CHANGE ME: Will be displayed in title and headers
	private $LASTFMAPI = "";
	
	public function __construct(){
		global $sroot;
		
		if(file_exists($sroot.CONFIG::$DBS.CONFIG::$dbfile)){
			$conf = file_get_contents($sroot.CONFIG::$DBS.CONFIG::$dbfile);
			$conf = unserialize($conf);
			if($conf instanceof CONFIG){
				$s= $conf->getSab();
				$this->sab["server"] = $s["server"];
				$this->sab["apikey"] = $s["apikey"];
				$this->sab["port"] = $s["port"];
				$this->sab["category"] = $s["category"];
				$this->sab["enabled"] = $s["enabled"];
				$this->sab["https"] = $s["https"];
				$s= $conf->getEmail();
				$this->email["enabled"] = $s["enabled"];
				$this->email["to"] = $s["to"];
				$this->email["from"] = $s["from"];
				$this->email["subject"] = $s["subject"];
				$s= $conf->getHP();
				$this->hp["server"] = $s["server"];
				$this->hp["apikey"] = $s["apikey"];
				$this->hp["port"] = $s["port"];
				$this->hp["enabled"] = $s["enabled"];
				$this->hp["https"] = $s["https"];
				
				$s= $conf->getSB();
				$this->sb["server"] = $s["server"];
				$this->sb["apikey"] = $s["apikey"];
				$this->sb["port"] = $s["port"];
				$this->sb["enabled"] = $s["enabled"];
				$this->sb["https"] = $s["https"];
				
				$s= $conf->getCP();
				$this->cp["server"] = $s["server"];
				$this->cp["apikey"] = $s["apikey"];
				$this->cp["port"] = $s["port"];
				$this->cp["enabled"] = $s["enabled"];
				$this->cp["https"] = $s["https"];
				
				$this->LASTFMAPI = $conf->getLastfmApiKey();
				$this->info = array(true, "config loaded ");
			}
			else{
				unlink($sroot.CONFIG::$DBS.CONFIG::$dbfile);
				$this->info = array(true,"initialized config with default settings1");
			}
		}
		else{
			$this->info = array(true,"initialized config with default settings0<br>");
		}
		LOG::info(__FILE__." Line[".__LINE__."]".$this->info[1]);
	}
	public function getLastfmApiKey(){
		return $this->LASTFMAPI;
	}
	public function saveLastfmApikey($a){
		global $sroot;
		$this->LASTFMAPI = $a;
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	public function saveSabConfig($s){
		global $sroot;
		
		if(substr($s["server"],strlen($s["server"])-1) == "/"){
			$s["server"]=substr($s["server"],0,strlen($s["server"])-1);
		}
		//self::$_instance = new self();
		$this->sab["server"] = $s["server"];
		$this->sab["apikey"] = $s["apikey"];
		$this->sab["port"] = $s["port"];
		$this->sab["category"] = $s["category"];
		$this->sab["enabled"] = $s["enabled"];
		$this->sab["https"] = $s["https"];
		
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	
	public function saveHPConfig($s){
		global $sroot;
		
		if(substr($s["server"],strlen($s["server"])-1) == "/"){
			$s["server"]=substr($s["server"],0,strlen($s["server"])-1);
		}
		$this->hp["server"] = $s["server"];
		$this->hp["apikey"] = $s["apikey"];
		$this->hp["port"] = $s["port"];
		$this->hp["enabled"] = $s["enabled"];
		$this->hp["https"] = $s["https"];
		
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	
	public function saveCPConfig($s){
		global $sroot;
		
		if(substr($s["server"],strlen($s["server"])-1) == "/"){
			$s["server"]=substr($s["server"],0,strlen($s["server"])-1);
		}
		$this->cp["server"] = $s["server"];
		$this->cp["apikey"] = $s["apikey"];
		$this->cp["port"] = $s["port"];
		$this->cp["enabled"] = $s["enabled"];
		$this->cp["https"] = $s["https"];
		
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	
	public function saveSBConfig($s){
		global $sroot;
		
		if(substr($s["server"],strlen($s["server"])-1) == "/"){
			$s["server"]=substr($s["server"],0,strlen($s["server"])-1);
		}
		$this->sb["server"] = $s["server"];
		$this->sb["apikey"] = $s["apikey"];
		$this->sb["port"] = $s["port"];
		$this->sb["enabled"] = $s["enabled"];
		$this->sb["https"] = $s["https"];
		
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	
	public function getHP(){
		return $this->hp;
	}
	public function getCP(){
		return $this->cp;
	}
	public function getSB(){
		return $this->sb;
	}
	public function getSab(){
		return $this->sab;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getHPHistory(){
		$hp = $this->hp;
		$cmd = "getHistory";
		if($hp["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$results = curl_exec($ch);
		curl_close($ch);
		LOG::info(__FILE__." Line[".__LINE__."]"."getting HP history - ".$url);
		return $results;
	}
	public function getSabQueue(){
		if($this->sab["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->sab["server"].":".$this->sab["port"]."/sabnzbd/api?mode=queue&limit=200&output=xml&apikey=".$this->sab["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."getting sab queue - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		return $resp;
	}
	
	public function getSabHistory(){
		if($this->sab["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->sab["server"].":".$this->sab["port"]."/sabnzbd/api?mode=history&output=xml&limit=200&apikey=".$this->sab["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."getting sab history - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		return $resp;
	}
	
	public function sendToSab($l, $n){
		if($this->sab["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->sab["server"].":".$this->sab["port"]."/sabnzbd/api?mode=addurl&name=".$l."&nzbname=".$n."&apikey=".$this->sab["apikey"]."&cat=".$this->sab["category"];
		LOG::info(__FILE__." Line[".__LINE__."]"."sending nzb to sab - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		return $resp;
	}
	
	public function sendToHP($l){
		if($this->hp["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->hp["server"].":".$this->hp["port"]."/api?cmd=addArtist&id=".$l."&apikey=".$this->hp["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."adding artist to hp - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		if($this->hp["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->hp["server"].":".$this->hp["port"]."/api?cmd=refreshArtist&id=".$l."&apikey=".$this->hp["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."refreshing artist in hp - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp .= "|".curl_exec($ch);
		curl_close($ch);
		return $resp;
	}
	public function sendToHPAlb($l){
		if($this->hp["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->hp["server"].":".$this->hp["port"]."/api?cmd=addAlbum&id=".$l."&apikey=".$this->hp["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."adding album in hp - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		if($this->hp["https"] === true){
			$url = "https://";
		}
		else{
			$url = "http://";
		}
		$url .= $this->hp["server"].":".$this->hp["port"]."/api?cmd=queueAlbum&id=".$l."&apikey=".$this->hp["apikey"];
		LOG::info(__FILE__." Line[".__LINE__."]"."queuing album in hp - ".$url);
		$this->info= array(true, $url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp .= "|".curl_exec($ch);
		curl_close($ch);
		return $resp;
	}
	
	public function saveMailConfig($s){
		global $sroot;
		$this->email["enabled"] = $s["enabled"];
		$this->email["to"] = $s["to"];
		$this->email["from"] = $s["from"];
		$this->email["subject"] = $s["subject"];
		
		$fp = fopen($sroot.CONFIG::$DBS.CONFIG::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->info = array(true, "config saved for mail");
			LOG::info(__FILE__." Line[".__LINE__."]"."config saved for mail");
		}
		else {
			$this->info = array(false, "file cannot be locked");
			LOG::error(__FILE__." Line[".__LINE__."]"."file cannot be locked");
		}
		$_SESSION['response'] = $this->info[1];
	}
	
	public function sendToMail($a, $b){
		LOG::info(__FILE__." Line[".__LINE__."]"." in send to mail");
		$msg = "Request for:" ."\n";
		$msg.= $a ."\n";
		$msg.= $b ."\n";
		$headers = 'From: '. $this->email["from"] . "\r\n";
		LOG::info(__FILE__." Line[".__LINE__."]"."sending email to - ".$this->email["to"]. " msg: ".CONFIG::escape_query($msg));
		$this->info= array(true, $url);
		$resp = mail($this->email["to"], $this->email["subject"], $msg, $headers);
		return $resp;
	}
	public function getMovieResults($q){
		$cp = $this->cp;
		$cmd = "/movie.search?q=".urlencode($q);
		if($cp["https"] === true){
			$getCPMovie = "https://";
		}
		else{
			$getCPMovie = "http://";
		}
		$getCPMovie .= $cp["server"].":".$cp["port"]."/api/".$cp["apikey"].$cmd;
		LOG::info(__FILE__." Line[".__LINE__."]"."Searching for movie in cp ".$getCPMovie);
		$ch = curl_init($getCPMovie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$results = curl_exec($ch);
		curl_close($ch);
		return json_decode($results);
	}
	public function getTvResults($q){
		$feed = $proto."://".$ip.":".$port."/api/".$api."/?cmd=".$cmd.$searchName;
		$sb = $this->sb;
		$cmd = "/?cmd=sb.searchtvdb&name=".urlencode($q);
		if($sb["https"] === true){
			$getSBShow = "https://";
		}
		else{
			$getSBShow = "http://";
		}
		$getSBShow .= $sb["server"].":".$sb["port"]."/api/".$sb["apikey"].$cmd;
		LOG::info(__FILE__." Line[".__LINE__."]"."Searching for show in sb ".$getSBShow);
		$ch = curl_init($getSBShow);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$results = curl_exec($ch);
		curl_close($ch);
		return json_decode($results);
	}
	public function getMusicResults($q){
		$hp = $this->hp;
		$lastfmapikey=$this->LASTFMAPI;
		$lastfmRes = array();
		if($q["artist"] != "" && $q["album"] == ""){
			$cmd = "findArtist&name=".urlencode($q["artist"]);
			if($hp["https"] === true){
				$getArtistUrl = "https://";
			}
			else{
				$getArtistUrl = "http://";
			}
			$getArtistUrl .= $hp["server"].":".$hp["port"]."/api?cmd=$cmd&apikey=".$hp["apikey"];
			LOG::info(__FILE__." Line[".__LINE__."]"."Searching for artist in hp ".$getArtistUrl);
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
			LOG::info(__FILE__." Line[".__LINE__."]"."Searching for album in hp ".$getArtistUrl);
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
			LOG::info(__FILE__." Line[".__LINE__."]"."Searching for artist then album in hp ".$getArtistUrl);
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
		return $lastfmRes;
	}
	
	public static function escape_query($str) {
		$str=htmlentities($str, ENT_QUOTES);
		return strtr($str, array(
			"\0" => "",
			"\"" => "&#34;",
			"\\" => "&#92;"
		));
	}
}
?>