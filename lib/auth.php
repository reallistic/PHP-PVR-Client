<?php
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
class AUTH{
	//auth token to expire
	private $username;
	private $password;
	private $authtoken;
	private $level;
	private $ip;
	private $enttype = "AES-256-CBC";
	private $sh;
	private $iv;
	public $info;
	private $dbfile = "auth.db";
	
	function __construct($u, $p){
		$this->authtoken = NULL;
		$this->info = array(false,"on init");
		$this->checkAuth($u, $p);		
	}
	
	private function checkAuth($u, $p){
		global $sroot, $fnm;
		
		if(!isset($this->authtoken)){
			if(is_file($sroot.CONFIG::$DBS.$this->dbfile)){
				$at = unserialize(file_get_contents($sroot.CONFIG::$DBS.$this->dbfile));
				if($at instanceof AUTH){
					$p = md5($p);
					$this->username = $u;
					if($at->getUsername() === $u && $at->getPassword() === $p){
						$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
						$this->info = array(true, "logged in successfully");
						
					}
					else{
						$this->authtoken = NULL;
						$this->info = array(true, "bad username or password");
					}
				}
				else{
					$this->authtoken = NULL;
					$this->info = array(true, "curropt auth.db file");
				}
			}
			else{
				$this->authtoken = "confirm";
				$this->info = array(true, "confirm");
				$this->username = $u;
				$this->password = $p;
			}
			
		}
		else{
			$this->info = array(false, "authtoken not null");
		}
		LOG::info(__FILE__." Line[".__LINE__."]".$this->info[1]);
	}
	public function checkToken($c){
		return (($this->authtoken == md5($_SERVER['REMOTE_ADDR'].$this->username)) || ($this->authtoken == "confirm" && $c ===true && $this->info[1]=="confirm"));
	}
	public function confirm($p){
		global $sroot, $fnm;
		
		if($p !== $this->password){
			$this->info = array(false, "Passwords don't match");
			$response = "Passwords don't match";
		}
		elseif($this->authtoken == "confirm" && isset($this->password) && !is_file($sroot.CONFIG::$DBS.$this->dbfile)){
			$this->info = $this->createAuth();
			$response = "auth created";
		}
		else{
			$this->info = array(false, "an error occured2");
			$response = "error";
		}
		LOG::info(__FILE__." Line[".__LINE__."]".$response);
	}
	private function createAuth(){
		global $sroot;
		
		if($this->authtoken === "confirm" || $this->checkAuth($this->username, $this->password)){
			if(is_file($sroot.CONFIG::$DBS.$this->dbfile)){
				unlink($sroot.CONFIG::$DBS.$this->dbfile);
			}
			$np = md5($this->password);
			$this->password = $np;
			$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$this->username);
			$fp = fopen($sroot.CONFIG::$DBS.$this->dbfile, 'w+');
			if(flock($fp, LOCK_EX)) {
				fwrite($fp, serialize($this));
				flock($fp, LOCK_UN);
				
				return array(true, "Credentials saved ".$this->username);
			}
			else {
				return array(false, "file cannot be locked");
			}
			fclose($fp);
		}
		else {
			return array(false, "An error occurred1");
		}
	}
	
	public function changeAuth($nu, $np){
		global $sroot;
		
		if($this->checkToken()){
			if(is_file($sroot.CONFIG::$DBS.$this->dbfile)){
				unlink($sroot.CONFIG::$DBS.$this->dbfile);
			}
			$this->username = $nu;
			$np = md5($np);
			$this->password = $np;
			$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$nu);
			$fp = fopen($sroot.CONFIG::$DBS.$this->dbfile, 'w+');
			if(flock($fp, LOCK_EX)) {
				fwrite($fp, serialize($this));
				flock($fp, LOCK_UN);				
				return "Credentials saved ".$this->username;
			}
			else {
				return "file cannot be locked";
			}
			fclose($fp);
		}
		else {
			return "An error occurred1";
		}
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getPassword(){
		return $this->password;
	}
}
?>