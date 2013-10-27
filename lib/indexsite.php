<?php
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
class INDEXSITE{
	private $name;
	private $apikey;
	private $category;
	private $url;
	public static $default_cat = "3010";
	private $enabled;
	private $id;
	public static $dbfile = "indexsites.db";
	
	public function __construct($n,$a,$u, $i, $e){
		$this->category = array();
		array_push($this->category, INDEXSITE::$default_cat);
		if(substr($u,strlen($u)-1) != "/"){
			$u=$u."/";
		}
		$this->name = $n;
		$this->apikey = $a;
		$this->url = $u;
		$this->enabled = $e;
		$this->id = $i;
	}
	public static function withID($id){
		global $sroot, $fnm;
		
		$response = array();
		if(is_file($sroot.CONFIG::$DBS.INDEXSITE::$dbfile)){
			$inxs = file_get_contents($sroot.CONFIG::$DBS.INDEXSITE::$dbfile);
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				array_push($response, "indexsite db was curropt");
			}
			else{
				for( $i=0; $i<count($inxs); $i++){
					$indexsite = $inxs[$i];
					if($indexsite->getId() == $id){
						return $indexsite;
					}
					else{
						array_push($response, "Found non- matching indexsite:");
						array_push($response, "-id ".$id." ".$indexsite->getId());
						array_push($response, "-name ".$indexsite->getName());
						array_push($response, "-url ".$indexsite->getUrl());
						array_push($response, "-apikey ".$indexsite->getApiKey());
					}
				}
			}
		}
		else{
			array_push($response, "indexsite db not found");
		}
		$response = implode(",",$response);
		LOG::info(__FILE__." Line[".__LINE__."]".$response);
		return $response;
		
	}
	public function addCat($c){
		if(in_array($c, $this->category, true) === false){
			array_push($c);
		}
	}
	public function setCat($c){
		$this->category = explode(",",$c);
	}
	public function removeCat($c){
		if(in_array($c, $this->category, true) === false){
			return false;
		}
		else{
			array_splice($this->category,array_search($c,$this->category,true),1);
		}
	}
	
	public function makeSearch($q){
		global $fnm;
		
		$cats = implode(',', $this->category);
		if(is_array($q)){
			$alb="";
			$art="";
			if($q['album'] != ""){
				$alb = "&album=".$q['album'];
			}
			if($q['artist'] != ""){
				$art = "&artist=".$q['artist'];
			}
			
			$response = $this->url."api?apikey=".$this->apikey."&cat=".$cats."&extended=1"."&t=music".$art.$alb;
		}
		else{
			$response = $this->url."api?apikey=".$this->apikey."&cat=".$cats."&extended=1"."&t=search&q=".$q;
		}
		LOG::info(__FILE__." Line[".__LINE__."]".$response);
		return $response;
	}
	
	public function saveSite(){
		global $sroot, $fnm;
		
		if(is_file($sroot.CONFIG::$DBS.INDEXSITE::$dbfile)){
			$inxs = file_get_contents($sroot.CONFIG::$DBS.INDEXSITE::$dbfile);
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				$inxs=array();
			}
		}
		else{
			$inxs = array();
		}
		array_push($inxs, $this);
		$fp = fopen($sroot.CONFIG::$DBS.INDEXSITE::$dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($inxs));
			flock($fp, LOCK_UN);
			LOG::info(__FILE__." Line[".__LINE__."]"." indexsite ".$this->name." saved");
			return array(true, "indexsite ".$this->name." saved");
		}
		else {
			LOG::error(__FILE__." Line[".__LINE__."]"." file cannot be locked");
			return array(false, "file cannot be locked");
		}
		
		fclose($fp);
	}
	
	public function delSite(){
		global $sroot, $fnm;
		
		$response = array();
		if(is_file($sroot.CONFIG::$DBS.INDEXSITE::$dbfile)){
			$inxs = file_get_contents($sroot.CONFIG::$DBS.INDEXSITE::$dbfile);
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				$inxs=array();
			}
			$savedsites=array();
			
			for( $i=0; $i<count($inxs); $i++){
				$indexsite = $inxs[$i];
				if(!$this->isEqual($indexsite)){
					array_push($savedsites,$indexsite);
				}
				elseif($this->isEqual($indexsite)){
					//found site, skip it
					array_push($response, "Found matching indexsite:");
					array_push($response, "-id ".$this->id." ".$indexsite->getId());
					array_push($response, "-name ".$this->name." ".$indexsite->getName());
					array_push($response, "-url ".$this->url." ".$indexsite->getUrl());
					array_push($response, "-apikey ".$this->apikey." ".$indexsite->getApiKey());
					array_push($response, "-cat ".$this->getCat()." ".$indexsite->getCat());
					array_push($response, "Found indexsite skipping");
				}
				elseif(!$indexsite instanceof INDEXSITE){
					//improperly formatted skip it
					array_push($response, "Found improper indexsite skipping");
				}
				else{
					array_push($response, "error found object of type ". gettype($indexsite));
				}
			}
			
			if(count($inxs)>0 && count($savedsites) >0  && count($inxs) != count($savedsites)) {
				$fp = fopen($sroot.CONFIG::$DBS.INDEXSITE::$dbfile, 'w+');
				if(flock($fp, LOCK_EX)){
					fwrite($fp, serialize($savedsites));
					flock($fp, LOCK_UN);
					array_push($response, "saved ". count($savedsites) . " site(s)");
				}
				else{
					array_push($response, "failed saving ". count($savedsites) . " site(s). file cannot be locked");
				}
				fclose($fp);
			}
			elseif(count($inxs)==0 || count($savedsites) == 0 ) {
				array_push($response, "either the index site db was curropt or no sites were saved");
				unlink($sroot.CONFIG::$DBS.INDEXSITE::$dbfile);
			}
			elseif(count($inxs) == count($savedsites)){
				array_push($response, "no changes neccassary");
			}
			else{
				array_push($response, "error ". count($inxs) . " sites in db ".count($savedsites) . " sites found");
			}
		}
		else{
			array_push($response, "indexsite db not found");
		}
		
		$response = implode(",",$response);
		LOG::info(__FILE__." Line[".__LINE__."]".$response);
		return $response;
	}
	
	public function isEqual($obj){
		if($obj instanceof INDEXSITE){
			return ($this->apikey === $obj->getApiKey() && $this->id === $obj->getId() && $this->name === $obj->getName() && $this->url === $obj->getUrl() && $this->getCat() == $obj->getCat());
		}
		
		return false;
	}
	
	public function sameClass($obj){
		return $obj instanceof $this;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getApiKey(){
		return $this->apikey;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getCat(){
		return implode(",",$this->category);
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function isEnabled(){
		return $this->enabled;
	}
}
?>