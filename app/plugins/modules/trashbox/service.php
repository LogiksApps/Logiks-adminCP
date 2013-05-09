<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

loadHelpers(array("files"));
if(isset($_REQUEST["action"])) {
	$arr=array();
	$dbCon=LogDB::singleton()->getLogDBCon();
	$arr["Database System"]=array(
						"log_activity"=>getConfig("LOGDB_PREFIX")."_log_activity",
						"log_error"=>getConfig("LOGDB_PREFIX")."_log_error",
						"log_login"=>getConfig("LOGDB_PREFIX")."_log_login",
						"log_pcron"=>getConfig("LOGDB_PREFIX")."_log_pcron",
						"log_search"=>getConfig("LOGDB_PREFIX")."_log_search",
						"log_sessions"=>getConfig("LOGDB_PREFIX")."_log_sessions",
						"log_sql"=>getConfig("LOGDB_PREFIX")."_log_sql",
						"log_system"=>getConfig("LOGDB_PREFIX")."_log_system",
						"log_visitor"=>getConfig("LOGDB_PREFIX")."_log_visitor",
						"log_requests"=>getConfig("LOGDB_PREFIX")."_log_requests",
					);
	
	$arr["Cache System"]=array();
	$fs=scandir(ROOT.TMP_FOLDER);
	unset($fs[0]);unset($fs[1]);
	foreach($fs as $a) {
		if(TMP_FOLDER.$a!=CACHE_FOLDER && is_dir(ROOT.TMP_FOLDER.$a)) {
			$arr["Cache System"]["System ".$a]=ROOT.TMP_FOLDER.$a;
		}
	}
	$fs=scandir(ROOT.CACHE_FOLDER);
	unset($fs[0]);unset($fs[1]);
	foreach($fs as $a) {
		if(is_dir(ROOT.CACHE_FOLDER.$a)) {
			$arr["Cache System"]["System Cache :: ".$a]=ROOT.CACHE_FOLDER.$a;
		}
	}
	$arr["APP Cache System"]=array();
	$fs=scandir(ROOT.APPS_FOLDER);
	unset($fs[0]);unset($fs[1]);
	foreach($fs as $a) {
		if(is_dir(ROOT.APPS_FOLDER."$a/tmp/")) {
			$arr["APP Cache System"]["AppCache For ".$a]=ROOT.APPS_FOLDER."$a/tmp/";
		}
	}
	if($_REQUEST["action"]=="list") {
		foreach($arr as $a=>$b) {
			echo "<tr class='subheader'><td class='clr_darkblue' colspan=10 style='padding-left:50px;'><h3>$a</h3></td></tr>";
			foreach($b as $x=>$y) {
				$o=$x;
				$x=str_replace("_"," ",$x);
				$x=ucwords($x);
				if($a=="Database System") {
					$sql="SELECT count(*) FROM $y";
					$r=$dbCon->executeQuery($sql);
					if($r) {
						$t=$dbCon->fetchData($r);
						$y=$t["count(*)"]." Records";
					} else {
						$y="NA";
					}
				} elseif($a=="Cache System") {
					$y=getFileSizeInString(getDirSize($y));
				} elseif($a=="APP Cache System") {
					if(file_exists($y))
						$y=getFileSizeInString(getDirSize($y));
					else
						$y=getFileSizeInString(0);
				}
				echo "<tr><td align=center><input name=selectTrash rel='$a:$o' type=checkbox /></td><td>$x</td><td align=right>$y</td></tr>";
			}
		}
	} elseif($_REQUEST["action"]=="clear") {
		$boxes=$_REQUEST["boxes"];
		//$fromDate=$_REQUEST["fromDate"];
		//$tillDate=$_REQUEST["tillDate"];
		$boxes=explode(",",$boxes);
		foreach($boxes as $a=>$b) {
			$b=explode(":",$b);
			if(isset($arr[$b[0]]) && isset($arr[$b[0]][$b[1]])) {
				$a=$b[0];
				$y=$arr[$b[0]][$b[1]];
				if($a=="Database System") {
					$sql="DELETE FROM $y WHERE id>0";
					$dbCon->executeQuery($sql);					
				} elseif($a=="Cache System" || $a=="APP Cache System") {
					deleteDir($y);
					if(!file_exists($y)) {
						if(mkdir($y,0777,true)) {
							chmod($y,0777);
						}
					}
				}
			}
		}
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("trashbox","help");
	} 
}
?>
