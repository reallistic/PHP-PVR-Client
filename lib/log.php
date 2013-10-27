<?php
class LOG{
	private $numlogs = 0;
	public static $LOGFILENAME = "pvr_php.log";

	public static function info($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("m/d/Y h:i:s a", time())." [INFO]: $text\r\n");
			fclose($fp);
		}
	}
	
	public static function warn($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("m/d/Y h:i:s a", time())." [WARN]: $text\r\n");
			fclose($fp);
		}
	}
	
	public static function error($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("m/d/Y h:i:s a", time())." [ERROR]: $text\r\n");
			fclose($fp);
		}
	}
	
	private static function checkFileSize($text){
		global $sroot;
		$textsize = mb_strlen($text, "UTF-8");
		if(!is_dir($sroot.CONFIG::$LOGS)){
			mkdir($sroot.CONFIG::$LOGS,0774,true);
		}
		if(file_exists($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME)){
			$size = filesize($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME);
			if($size+$textsize > CONFIG::$MAXLOGSIZE){
				if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.CONFIG::$LOGSTOKEEP)){
					unlink($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.CONFIG::$LOGSTOKEEP);
				}
				for($i =1; $i<CONFIG::$LOGSTOKEEP; $i++){
					if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i)){
						rename($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i,$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.($i+1));
					}
				}
				
			}
		}
	}
	public static function getLogs(){
		global $sroot;
		
		$logs = array();
		if(file_exists($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME)){		
			//self::info(__FILE__." Line[".__LINE__."]"." looking for file ".$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
			if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME)){
				//self::info(__FILE__." Line[".__LINE__."]"." found file ".$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
				$filecont = file_get_contents($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
				$logs = array_merge($logs,explode("\r\n",$filecont));
				$logs = array_reverse($logs);
			}	
			/*for($i =1; $i<CONFIG::$LOGSTOKEEP; $i++){
				//self::info(__FILE__." Line[".__LINE__."]"." looking for file ".$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
				if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i)){
					//self::info(__FILE__." Line[".__LINE__."]"." found file ".$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
					$filecont = file_get_contents($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i);
					$logs = array_merge($logs,explode("\r\n",$filecont));
				}
			}*/
		}
		return $logs;
	}
	private static function numLogs(){
		global $sroot;
		
		$cnt =0;
		if ($handle = opendir($sroot.CONFIG::$LOGS)) {
			while (false !== ($file = readdir($handle))) {
				if(is_file($sroot.CONFIG::$CLASSES.$file)){
					$cnt++;
				}
			}
		}
		return $cnt;
	}
}
?>