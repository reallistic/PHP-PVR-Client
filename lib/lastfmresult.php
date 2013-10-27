<?php
class LASTFMRESULT{
	private $link;
	private $artist;
	private $albums;
	private $available;
	private $albumArts;
	private $artistImg;
	private $name;
	
	function __construct($a){
		$this->albums = array();
		$this->available = array();
		$this->albumArts = array();
		$this->artist = $a;
	}
	function setArtistImg($im){
		$this->artistImg = $im;
	}
	function getArtistImg(){
		return $this->artistImg;
	}
	function addAlbum($a, $av, $art){
		array_push($this->albums, $a);
		array_push($this->available, $av);
		array_push($this->albumArts, $art);
	}
	function setAvailable($i, $b){
		$this->available[$i] = $b;
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
	function getName(){
		return $this->name;
	}
	function setName($n){
		$this->name = $n;
	}
	function getAlbumAsQuery($i){
		return array(
			'artist' => $this->artist,
			'album' => $this->albums[$i]
		);
	}
}
/*$album->getArtist() . "</h3>";
						echo "<a href=\"" . $album->getUrl() . "\" >LastFm - " .$album->getName()."</a><br>";			
						//echo '<img src="' . $artist->getImage(4) . '">';
						echo "<ul>";
						foreach ($album->getAlbums() as $alb){
							*/
?>

