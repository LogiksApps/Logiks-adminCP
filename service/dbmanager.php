<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
isAdminSite();

loadHelpers("specialcfgfiles");

if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="sitelist") {
		$fs=scandir(ROOT.APPS_FOLDER);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $a=>$b) {
			unset($fs[$a]);
			if(is_file(ROOT.APPS_FOLDER.$b."/config/db.cfg")) $fs[$b]=$b;
		}
		$fs["Core Site"]="*";
		$fs=array_reverse($fs);
		printFormattedArray($fs);
	} elseif($_REQUEST["action"]=="dbmstable") {
		$fs=scandir(ROOT.APPS_FOLDER);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $a=>$b) {
			unset($fs[$a]);
			if(is_file(ROOT.APPS_FOLDER.$b."/config/db.cfg")) $fs[$b]=$b;
		}
		
		$s="";
		foreach($fs as $a) {
			$f=ROOT.APPS_FOLDER.$a."/config/db.cfg";
			$fArr=SpecialCfgFiles::LoadCfgFile($f);
			$fArr=$fArr["DBCONFIG"];
			$ss="<tr rel='$a'>";
			$ss.="<td align=center><input name='rowselect' class='dbselect' type=radio rel='$a' /></td>";
			$ss.="<td align=left>$a</td>";
			$ss.="<td align=left style='text-transform:capitalize'>{$fArr['DB_DRIVER']}</td>";
			$ss.="<td align=left>{$fArr['DB_HOST']}</td>";
			$ss.="<td align=left>{$fArr['DB_DATABASE']}</td>";
			$ss.="<td align=left>{$fArr['DB_USER']}</td>";
			if(isset($fArr["DB_READ_ONLY"])) {
				if($fArr["DB_READ_ONLY"]=="true") {
					$ss.="<td align=center><input class='readonly' type=checkbox rel='$a' checked /></td>";
				} else {
					$ss.="<td align=center><input class='readonly' type=checkbox rel='$a' /></td>";
				}
			} else {
				$ss.="<td align=center><b>NA</b></td>";
			}
			
			$ss.="<td align=right>";
			$ss.="<div class='tablelist popupicon' title='Table List' style='float:right;width:5px;'></div>";
			$ss.="<div class='schema codeicon' title='Download Schema' style='float:right;width:5px;'></div>";
			$ss.="</td>";
			$ss.="</tr>";
			$s.=$ss;
		}
		echo $s;
	} elseif($_REQUEST["action"]=="readonly") {
		$f=ROOT.APPS_FOLDER.$_REQUEST['s']."/config/db.cfg";
		if(file_exists($f)) {
			if(!is_writable($f)) {
				exit("Error Writing DB Configuration For '$s'");
			}
			$vN=$_REQUEST['v'];
			$vO=($vN=="true")?"false":"true";
			$data=file_get_contents($f);
			$data=str_replace("DB_READ_ONLY=$vO","DB_READ_ONLY=$vN",$data);
			file_put_contents($f,$data);
		} else {
			echo "Error Finding DB Configuration For '$s'";
		}
	} elseif($_REQUEST["action"]=="getinfo") {
		$con=getDBConnectionForSite($_REQUEST['s']);
		if($con!=null) {
			$tblList=$con->getTableList();
			
			$s="<div style='width:200px;height:300px;overflow:auto;'>";
			$s.="<table>";				 
			foreach($tblList as $a) {
				$s.="<tr><td>$a</td></tr>";
			}
			$s.="</table>";
			$s.="</div>";
			echo $s;
		} else {
			echo "Error Finding DB Configuration For '$s'";
		}
	} elseif($_REQUEST["action"]=="getschema") {
		$con=getDBConnectionForSite($_REQUEST['s']);
		if($con!=null) {
			$tblList=$con->getTableList();
			$s="";
			foreach($tblList as $a) {
				$r=$con->executeQuery('SHOW CREATE TABLE '.$a);
				if($r) {
					$da=_dbData($r);
					$con->freeResult($r);
					$s.="{$da[0]['Create Table']};\n\n";
				}
			}
			echo "<pre>$s</pre>";
		} else {
			echo "Error Finding DB Configuration For '$s'";
		}
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("dbmanager","help");
	} 
}
exit();
function getDBConnectionForSite($site) {
	$f=ROOT.APPS_FOLDER.$site."/config/db.cfg";
	if(file_exists($f)) {
		$fArr=SpecialCfgFiles::LoadCfgFile($f);
		$fArr=$fArr["DBCONFIG"];
		$con=new Database($fArr["DB_DRIVER"]);
		$con->connect($fArr["DB_USER"],$fArr["DB_PASSWORD"],$fArr["DB_HOST"],$fArr["DB_DATABASE"]);
		return $con;
	}
	return null;
}
?>
