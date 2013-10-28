<?php
class SBRESULT{
	private $link;
	private $img;
	private $name;
	private $tvdbid;
	private $added;
	private $started;
	
	function __construct($i, $n, $f){
		$this->tvdbid = $i;
		$this->name = $n;
		$this->started = $f;
	}
	function setImg($im){
		$this->img = $im;
	}
	
	function getImg(){
		return $this->img;
	}
	function getTvdbId(){
		return $this->tvdbid;
	}
	function setUrl($u){
		$this->link = $u;
	}
	function getUrl(){
		return $this->link;
	}
	function getName(){
		return $this->name;
	}
	function getStarted(){
		return $this->started;
	}
	function setName($n){
		$this->name = $n;
	}
	function setAdded($a){
		$this->added = $a;
	}
	function isAdded(){
		return $this->added;
	}
	function getStatus(){
		if($this->added){
			return "Added";
		}
		else{
			return "Not Added";
		}
	}
}
?>

