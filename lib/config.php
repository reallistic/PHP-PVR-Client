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
	
	public function getSab(){
		return $this->sab;
	}
	public function getEmail(){
		return $this->email;
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