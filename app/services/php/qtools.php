<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(isset($_REQUEST["mode"])) {
	if($_REQUEST["mode"]=="savequickbar") {
		$f=APPROOT.CFG_FOLDER."tools.php";
		if(file_exists($f)) {
			include $f;
			
			foreach($_launchBar as $a=>$b) {
				if(isset($_REQUEST[$a])) {
					$_launchBar[$a]=array($b[0],$_REQUEST[$a],$b[2]);
				}
			}
			
			$txt="<?php\n";
			$txt.='$_launchBar=array('."\n";
			foreach($_launchBar as $a=>$b) {
				$s="\t\t'%s'=>array('%s','%s','_blank'),\n";
				$txt.=sprintf($s,$a,$b[0],$b[1],$b[2]);
			}
			$txt.="\t);\n";
			$txt.="?>\n";
			if(is_writable($f)) {
				file_put_contents($f,$txt);
			} else {
				echo "Error:File Is Write Protected ...";
			}
		}
	}
}
if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="applist") {
		$fs=scandir(ROOT.APPS_FOLDER);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $a=>$b) {
			unset($fs[$a]);
			if(!is_file(ROOT.APPS_FOLDER.$b)) $fs[$b]=$b;
		}
		$fs=array_reverse($fs);
		$fs["Common To All"]="**";
		$fs["All AppSites"]="*";
		$fs=array_reverse($fs);
		printFormattedArray($fs);
	} elseif($_REQUEST["action"]=="sitelist") {
		$fs=scandir(ROOT.APPS_FOLDER);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $a=>$b) {
			unset($fs[$a]);
			if(!is_file(ROOT.APPS_FOLDER.$b)) $fs[$b]=$b;
		}
		printFormattedArray($fs);
	} elseif($_REQUEST["action"]=="privilegelist") {
		$s="";
		if(isset($_REQUEST["forsite"])) $s=$_REQUEST["forsite"];
		$sql="SELECT id,name FROM "._dbtable("privileges",true)." where blocked='false'";
		if(strlen($s)>0) $sql.=" and (site='*' or site='$s')";
		else $sql.=" and site='*'";
		
		$r=_dbQuery($sql,true);
		if($r) {
			$a=_db(true)->fetchAllData($r);
			$o=array();
			foreach($a as $x=>$c) {
				$o[$c["name"]]=$c["id"];
			}
			printFormattedArray($o);
		}
	} elseif($_REQUEST["action"]=="accesslist") {
		$s="";
		if(isset($_REQUEST["forsite"])) $s=$_REQUEST["forsite"];
		$sql="SELECT id,master FROM "._dbtable("access",true)." where blocked='false' and sites='*'";
		if(strlen($s)>0) $sql.=" or sites LIKE '%$s%'";
		$r=_dbQuery($sql,true);
		if($r) {
			$a=_db(true)->fetchAllData($r);
			$o=array();
			foreach($a as $x=>$c) {
				$o[$c["master"]]=$c["id"];
			}
			printFormattedArray($o);
		}
	}
}
exit();

?>
