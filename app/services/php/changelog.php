<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

$sFrmt="<div class='file'><input type=checkbox rel='%s' /><b style='color:green;margin-right:4px;'>%d</b><a class=viewlink href='".SiteLocation."services/?scmd=changelog&site=".SITENAME."&mode=viewfile&file=%s' target=_blank>%s</a><a class=downloadlink href='".SiteLocation."services/?scmd=changelog&mode=downloadfile&file=%s' target=_blank><i style='color:blue;margin-left:10px;'>Download</i></a><div style='float:right'>%s</div></div>";//
$patchFrmt="";
$date1=date("Y-m-d G:m:s");
$date2=date("Y-m-d G:m:s");
$bdir=ROOT;

$logFile=ROOT.TMP_FOLDER."changelog/".date("Y-m-d G:m").".log";
$patchFile=ROOT.TMP_FOLDER."patches/patch_".date("Y:m:d-H:i").".zip";
$excludeDir=array("tmp","userdata","log","cache",".git");
$excludeFile=array(".gitignore");
$writeLogToFile=false;

if(!file_exists(dirname($logFile))) {
	mkdir(dirname($logFile),0777,true);
	chmod(dirname($logFile),0777);
}
if(!file_exists(dirname($patchFile))) {
	mkdir(dirname($patchFile),0777,true);
	chmod(dirname($patchFile),0777);
}
if(isset($_REQUEST["root"]) && $_REQUEST["root"]=="*") {
	$_REQUEST["root"]="/";
} else array_push($excludeDir,"apps");

if(isset($_REQUEST["mode"])) {
	if($_REQUEST["mode"]=="viewlog") {
		if(isset($_REQUEST["date1"]) && strlen($_REQUEST["date1"])>0) {
			$date1=getDateFormated($_REQUEST["date1"],"d/m/Y");
		}
		if(isset($_REQUEST["date2"]) && strlen($_REQUEST["date2"])>0) {
			$date2=getDateFormated($_REQUEST["date2"],"d/m/Y");
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
			readfile($f);
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
			readfile($f);
			ob_flush();
			ob_clean();
		} else {
			echo "<h3>File Not Found</h3>";
		}
	} elseif($_REQUEST["mode"]=="downloadzip" && isset($_REQUEST["file"])) {
		$zipFile=$patchFile;
		$baseFolder=$_SERVER["DOCUMENT_ROOT"]."/".InstallFolder;

		//$zip=null;
		$zip = new ZipArchive;
		$res = $zip->open($zipFile, ZipArchive::CREATE);
		if($res !== TRUE){
			echo 'Error: Unable to create zip file';
		} else {
			foreach($_REQUEST["file"] as $f) {
				$f=$_SERVER["DOCUMENT_ROOT"].base64_decode($f);
				if(file_exists($f)) {
					$fName=str_replace("#{$baseFolder}","","#{$f}");
					$zip->addFile($f,$fName);
				}
			}
			$zip->close();

			$filename=basename($zipFile);
			$mime="application/zip";
			ob_start();
			header("Content-type: $mime");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			readfile($zipFile);
			ob_flush();
			ob_clean();
			if(isset($_REQUEST['autoclear']) && $_REQUEST['autoclear']=="true")
				unlink($zipFile);
			exit();
		}
		echo "Failed To Collect The Files";
	} elseif($_REQUEST["mode"]=="listpatches") {
		$patchDir=dirname($patchFile)."/";
		$fs=scandir($patchDir);
		$cnt=1;
		if(count($fs)>2) {
			foreach($fs as $a) {
					if($a=="." || $a=="..") continue;
					elseif(is_dir($patchDir.$a)) continue;

					$x=explode("_",$a);
					$dt=$x[1];
					$dt=substr($dt,0,strlen($dt)-4);
					$patchFrmt="<div class='file'><b style='color:green;margin-right:4px;'>%d</b>%s<a class=downloadlink href='".SiteLocation."services/?scmd=changelog&mode=downloadpatch&file=%s' target=_blank><i style='color:blue;margin-left:10px;'>Download</i></a><div style='float:right'>%s</div></div>";
					printf($patchFrmt,$cnt,$a,$a,$dt);
					$cnt++;
			}
		} else {
				echo "<h3 align=center>No Patches Created Till Now.</h3>";
		}
	} elseif($_REQUEST["mode"]=="downloadpatch" && isset($_REQUEST["file"])) {
		$patchDir=dirname($patchFile)."/";
		$zipFile=$patchDir.$_REQUEST["file"];

		if(file_exists($zipFile)) {
			$filename=basename($zipFile);
			$mime="application/zip";
			ob_start();
			header("Content-type: $mime");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			readfile($zipFile);
			ob_flush();
			ob_clean();
		} else {
			echo "<b>{$_REQUEST["file"]}</b> Was Not Found In Patch Repo ...";
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
			$this->logFileHandle=fopen($logFile,"w");
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
				elseif(in_array($a,$this->excludeDir)) continue;
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
				printf($this->sfrmt,base64_encode($path),$this->cnt,base64_encode($path),$path1,base64_encode($path),$dt);
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
		$modified_str=date("d/m/Y H:i:s", $modified);
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
function getDateFormated($d1,$frmtFrm="d/m/Y",$frmtTo="Y-m-d") {
	$d1=explode(" ",$d1);
	if(isset($d1[1])) $t1=$d1[1];
	else $t1="00:00:00";
	$d1=$d1[0];

	if(method_exists(new DateTime(),"createFromFormat")) {
		$dt=DateTime::createFromFormat($frmtFrm,$d1);
		return $dt->format($frmtTo)." $t1";
	} else {
		$dd=_date($d1,$frmtFrm,"Y/m/d");
		$dt=new DateTime($dd);
		return $dt->format($frmtTo)." $t1";
	}
}
?>
