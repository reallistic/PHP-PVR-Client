<?php
class MBRESULT{
	private $link;
	private $artist;
	private $albums;
	private $available;
	private $albumArts;
	private $albumIds;
	private $artistImg;
	private $name;
	private $artistId;
	private $added;
	
	function __construct($a, $e){
		$this->albums = array();
		$this->available = array();
		$this->albumArts = array();
		$this->artist = $a;
		$this->added = $e;
	}
	function setArtistImg($im){
		$this->artistImg = $im;
	}
	
	function getArtistImg(){
		return $this->artistImg;
	}
	function setArtistId($id){
		$this->artistId = $id;
	}
	function getArtistId(){
		return $this->artistId;
	}
	function addAlbum($a, $av, $art, $id){
		array_push($this->albums, $a);
		array_push($this->available, $av);
		array_push($this->albumArts, $art);
		array_push($this->albumIds, $id);
	}
	function setAvailable($i, $b){
		$this->available[$i] = $b;
	}
	function getAvailable($i){
		return $this->available[$i];
	}
	function setUrl($u){
		$this->link = $u;
	}
	
	function getArtist(){
		return $this->artist;
	}
	function getUrl(){
		return $this->link;
	}
	function getAlbums(){
		return $this->albums;
	}
	function getAlbumsArt($i){
		return $this->albumArts[$i];
	}
	function getAlbumsId($i){
		return $this->albumIds[$i];
	}
	function getName(){
		return $this->name;
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
	function getAlbumAsQuery($i){
		return array(
			'artist' => $this->artist,
			'album' => $this->albums[$i],
			'id' => $this->albumIds[$i],
			'art' => $this->albumArts[$i]
		);
	}
}
?>

