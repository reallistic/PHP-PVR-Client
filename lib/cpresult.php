<?php
class CPRESULT{
	private $link;
	private $img;
	private $name;
	private $imdbid;
	private $added;
	private $started;
	private $genre;
	
	function __construct($i, $n, $f){
		$this->imdbid = $i;
		$this->name = $n;
		$this->started = $f;
	}
	function setImg($im){
		$this->img = $im;
	}
	function getGenre(){
		return $this->genre;
	}
	function setGenre($g){
		$this->genre = $g;
	}
	function getImg(){
		return $this->img;
	}
	function getImdbId(){
		return $this->imdbid;
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
		return ($this->added >0);
	}
	function getStatus(){
		if($this->added === 1){
			return "Wanted";
		}
		elseif($this->added === 2){
			return "Added";
		}
		else{
			return "Not Added";
		}
	}
}
?>

