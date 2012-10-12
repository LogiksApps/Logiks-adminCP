<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

$sFrmt="<div class='file'><b style='color:green;margin-right:4px;'>%d</b><a class=viewlink href='services/?scmd=changelog&mode=viewfile&file=%s' target=_blank>%s</a><a class=downloadlink href='services/?scmd=changelog&mode=downloadfile&file=%s'><i style='color:blue;margin-left:10px;'>Download</i></a><div style='float:right'>%s</div></div>";//
$date1=date("Y-m-d G:m:s");
$date2=date("Y-m-d G:m:s");
$bdir=ROOT;

$logFile=ROOT.TMP_FOLDER."changelog/".date("Y-m-d G:m").".log";
$excludeDir=array("tmp","log","cache",".git");
$excludeFile=array();
$writeLogToFile=false;

if(!file_exists(dirname($file))) {
	mkdir(dirname($file),0777,true);
	chmod(dirname($file),0777);
}

if(isset($_REQUEST["mode"])) {
	if($_REQUEST["mode"]=="viewlog") {
		if(isset($_REQUEST["date1"]) && strlen($_REQUEST["date1"])>0) {
			$dt=DateTime::createFromFormat("d/m/Y",$_REQUEST["date1"]);
			$date1=$dt->format("Y-m-d 00:00:00");
		}
		if(isset($_REQUEST["date2"]) && strlen($_REQUEST["date2"])>0) {
			$dt=DateTime::createFromFormat("d/m/Y",$_REQUEST["date2"]);
			$date2=$dt->format("Y-m-d 00:00:00");
		} else {
			$date2 = strtotime(date("Y-m-d") . " +1 day");
			$date2 = date('Y-m-d 00:00:00', $date2);
		}
		if(isset($_REQUEST["root"]) && strlen($_REQUEST["root"])>0) {
			$bdir=ROOT.$_REQUEST["root"]."/";
		}
		$bdir=str_replace("//","/",$bdir);
		$bdir=str_replace("//","/",$bdir);
		
		//setcookie("Changelog.date1", $_REQUEST["date1"], time()+3600);
		//setcookie("Changelog.date2", $_REQUEST["date2"], time()+3600);
		
		$cl=new changelog($bdir,$sFrmt,$writeLogToFile,$logFile,$excludeDir,$excludeFile);
		$cl->dumpChangelog($bdir, strtotime($date1),strtotime($date2));
	} elseif($_REQUEST["mode"]=="viewfile") {
		$f=$_SERVER["DOCUMENT_ROOT"].base64_decode($_REQUEST["file"]);
		if(file_exists($f)) {
			ob_start();
			echo "<style>html,body {padding:0px;margin:0px;overflow:hidden;}</style>";
			echo "<textarea style='width:99%;height:97%;resize:none;border:0px;padding:5px;' readonly>";
			echo file_get_contents($f);
			echo "</textarea>";
			ob_flush();
			ob_clean();
		} else {
			echo "<h3>File Not Found</h3>";
		}
	} elseif($_REQUEST["mode"]=="downloadfile") {
		$f=$_SERVER["DOCUMENT_ROOT"].base64_decode($_REQUEST["file"]);
		$filename=basename($f);
		$mime="application/txt";
		if(file_exists($f)) {
			ob_start();
			header("Content-type: $mime");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			echo file_get_contents($f);			
			ob_flush();
			ob_clean();
		} else {
			echo "<h3>File Not Found</h3>";
		}
	}
} else {
	echo "<h3>Changelog Mode Not Found</h3>";
}
exit();

class changelog {
	public $cnt=1;
	public $root;
	public $writeLogToFile;
	public $sfrmt;
	private $logFileHandle;
	private $excludeDir;
	private $excludeFile;
	
	public function __construct($bdir,$frmt,$writeLog,$logFile,$excludeDir,$excludeFile) {
		$this->root=$bdir;
		$this->writeLogToFile=$writeLog;
		$this->sfrmt=$frmt;
		$this->excludeDir=$excludeDir;
		$this->excludeFile=$excludeFile;
		$this->logFileHandle=$logFile;		
		if($writeLog && is_writable($this->logFileHandle)) {
			$this->logFileHandle=fopen($file,"w");
		} else {
			$this->logFileHandle=null;
		}
	}
	function __destruct() {
		if($this->logFileHandle!=null) fclose($this->logFileHandle);
	}
	public function dumpChangelog($dir,$frmDate,$toDate) {
		if(is_dir($dir)) {
			$fs=scandir($dir);
			foreach($fs as $a) {
				if($a=="." || $a=="..") continue;
				if(in_array($a,$this->excludeDir)) continue;
				$p=$dir."/".$a;
				$p=str_replace("//","/",$p);
				$this->dumpChangelog($p,$frmDate,$toDate);
			}
		} else {
			if(in_array(basename($dir),$this->excludeFile)) return;
			$dt=$this->getChange($dir,$frmDate,$toDate);
			if(strlen($dt)>0) {
				$path=str_replace($_SERVER["DOCUMENT_ROOT"],"",$dir);
				$path1=str_replace($this->root,"",$dir);
				printf($this->sfrmt,$this->cnt,base64_encode($path),$path1,base64_encode($path),$dt);
				$this->cnt++;
				if($this->writeLogToFile) {
					$str =$dir.'=>'.$dt."\n";
					$this->writeLog($str);
				}
			}
		}
	}
	private function getChange($f,$frmDate,$toDate) {
		$modified = filemtime($f);
		$modified_str=date("d/m/Y h:i:s", $modified);
		if($frmDate < $modified && $modified<=$toDate)  {
			return $modified_str;
		} else {
			return "";
		}
	}
	private function writeLog($str) {
		if($this->logFileHandle!=null) 
			fwrite($this->logFileHandle,$str);
	}	
}
?>
