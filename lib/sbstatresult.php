<?php
class SBSTATRESULT{
	private $link;
	private $img;
	private $name;
	private $tvdbid;
	private $epStatus;
	private $status;
	private $airs;
	private $epName;
	
	function __construct($i, $n, $f){
		$this->tvdbid = $i;
		$this->name = $n;
		$this->status = $f;
	}
	function setImg($im){
		$this->img = $im;
	}
	function setAirs($a){
		$this->airs = $a;
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
	function getNextEp(){
		return $this->epName;
	}
	function getEpStatus(){
		return $this->epStatus;
	}
	function setName($n){
		$this->name = $n;
	}
	function setEpName($n){
		$this->epName = $n;
	}
	function setEpStatus($a){
		$this->epStatus = $a;
	}
	function getStatus(){
		return $this->status;
	}
	function getAirs(){
		return $this->airs;
	}
}
?>

