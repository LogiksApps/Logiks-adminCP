<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="list") {
		$out=PCronQueue::get_all_tasks();
		$cnt=1;
		if(sizeof($out)>0) {
			foreach($out as $t) {
				$s="<tr rel='{$t['id']}' params={$t['script_params']} >";
				$s.="<td align=center><input name=selectedrow rel='{$t['id']}' type=radio /></td>";
				$s.="<td name=title>{$t['title']}</td>";
				$s.="<td name=schdulle rel='{$t['schdulle']}' align=center>".getPeriod($t['schdulle'])."</td>";
				
				if($t['run_only_once']=="true") 
					$s.="<td name=run_once v={$t['run_only_once']} align=center><input name=run_once_job type=checkbox checked /></td>";
				else
					$s.="<td name=run_once v={$t['run_only_once']} align=center><input name=run_once_job type=checkbox /></td>";
					
				$s.="<td name=scriptpath rel='{$t['scriptpath']}' >{$t['scriptpath']}<div class=scripteditbutton></div></td>";
				$s.="<td name=site align=left>{$t['site']}</td>";
				$s.="<td name=description>{$t['description']}</td>";
				
				if($t['method']=="POST") 
					$s.="<td name=method v={$t['method']} align=left><input name=method_job type=checkbox checked />{$t['method']}</td>";
				else $s.="<td name=method v={$t['method']} align=left><input name=method_job type=checkbox />{$t['method']}</td>";
				
				$s.="<td align=left>{$t['last_completed']}</td>";
				//{$t['blocked']}
				if($t['blocked']=="true") $s.="<td name=blocked align=center><input name=blocked_job type=checkbox checked /></td>";
				else $s.="<td name=blocked align=center><input name=blocked_job type=checkbox /></td>";
				$s.="</tr>";
				
				$cnt++;
				echo $s;
			}	
		} else {
			echo "<tr><td colspan=10><h3 align=center>No Jobs Found</h3></td></tr>";
		}
	} elseif($_REQUEST["action"]=="cmd") {
		if(!isset($_REQUEST["id"])) exit("No Job Selected");
		if(!isset($_REQUEST["cmd"])) exit("No Command Found");
		$cmd=$_REQUEST["cmd"];
		$id=$_REQUEST["id"];
		if($cmd=="block") {
			$sql="update "._dbTable("cron_jobs",true)." set blocked='true' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="unblock") {
			$sql="update "._dbTable("cron_jobs",true)." set blocked='false' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="post") {
			$sql="update "._dbTable("cron_jobs",true)." set method='POST' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="get") {
			$sql="update "._dbTable("cron_jobs",true)." set method='GET' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="run_once") {
			$sql="update "._dbTable("cron_jobs",true)." set run_only_once='true' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="run_periods") {
			$sql="update "._dbTable("cron_jobs",true)." set run_only_once='false' where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="delete") {
			$sql="delete from "._dbTable("cron_jobs",true)." where id = $id";
			_dbQuery($sql,true);
		} elseif($cmd=="run") {
			$p=new PCronQueue();
			$a=$p->runTaskById($id);
			if($a) echo "Successfully Ran Jobs :: $a";
			else echo "Error Running Jobs";
		}
	} elseif($_REQUEST["action"]=="tsk") {
		if(!isset($_REQUEST["tsk"])) exit("No Command Found");
		$tsk=$_REQUEST["tsk"];
		if($tsk=="listscripts") {
			$fs=scandir(ROOT.PCRON_FOLDER);
			unset($fs[0]);unset($fs[1]);
			foreach($fs as $a) {
				$a=str_replace(".php","",$a);
				echo "<option value='$a'>$a</option>";
			}
		} elseif($tsk=="listsites") {
			$fs=scandir(ROOT.APPS_FOLDER);
			unset($fs[0]);unset($fs[1]);
			echo "<option value='*'>All Sites</option>";
			foreach($fs as $a) {
				$a=str_replace(".php","",$a);
				echo "<option value='$a'>$a</option>";
			}
		} elseif($tsk=="uploadscript") {
			uploadFileToDir('myfile',ROOT.PCRON_FOLDER);
		} elseif($tsk=="fetchscript") {
			$f=ROOT.PCRON_FOLDER.$_REQUEST["script"].".php";
			if(file_exists($f)) echo file_get_contents($f);
		} elseif($tsk=="savescript") {
			$f=ROOT.PCRON_FOLDER.$_REQUEST["script"].".php";
			$code=$_POST["code"];
			if(is_writable($f)) file_put_contents($f,$code);
			else echo "Script File Is Readonly.";
		}
	} elseif($_REQUEST["action"]=="create") {
		$arr=array("title","description","scriptpath","schdulle","script_params","method","run_only_once","forsite");
		foreach($arr as $a=>$b) {
			unset($arr[$a]);
			$arr[$b]=$_POST[$b];
		}
		loadHelpers("dateops");
		if($arr["run_only_once"]=="true") {
			$frmt=getConfig("DATE_FORMAT");//." H:i:s";
			$frmt=str_replace("yy","Y",$frmt);
			$arr["schdulle"]=subtractDates(date($frmt),$arr["schdulle"],0);
		}
		$a=PCronQueue::createTask($arr["title"],$arr["description"],$arr["schdulle"],$arr["scriptpath"],array(),$arr["method"],$arr["run_only_once"],$arr["forsite"]);
		if(strlen($a)<=0 || $a==-1) echo "Error Creating Task/Job";
	} elseif($_REQUEST["action"]=="edit") {
		$id=$_REQUEST["id"];
		$tbl=_dbTable("cron_jobs",true);
		
		$arr=array("title","description","scriptpath","schdulle","script_params","method","run_only_once","forsite");
		foreach($arr as $a=>$b) {
			unset($arr[$a]);
			$arr[$b]=$_POST[$b];
		}
		
		$sql="UPDATE $tbl SET ";
		
		$sql.="title='{$arr['title']}'";
		$sql.=",description='{$arr['description']}'";
		$sql.=",scriptpath='{$arr['scriptpath']}'";
		$sql.=",schdulle='{$arr['schdulle']}'";
		$sql.=",script_params='{$arr['script_params']}'";
		$sql.=",method='{$arr['method']}'";
		$sql.=",run_only_once='{$arr['run_only_once']}'";
		$sql.=",site='{$arr['forsite']}'";
		
		$sql.=" WHERE ID=$id";
		
		_dbQuery($sql,true);
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("cronjobs","help");
	} 
}
exit();

function getPeriod($n) {
	return $n . " Secs";
}
function uploadFileToDir($name, $path) {
	$maxFileSize = "4000000";
	
	$fileType = $_FILES[$name]['type'];
	$fileSize = $_FILES[$name]['size'];
	$fileName = $_FILES[$name]['name'];
	$tmpName  = $_FILES[$name]['tmp_name'];
	
	$target_path= $path . strtolower($fileName);
	
	if(strpos($target_path,".php")!=strlen($target_path)-4) {
		echo "Please Upload PHP Files only";
		return;
	}
	
	if ($fileSize<$maxFileSize) {
		if (!move_uploaded_file($tmpName,$target_path)) {
			echo "Error Moving script : $fileName";
		} else {
			chmod($target_path,0777);
			echo "Uploaded Script $fileName";
		}
	} else {
		echo "Error uploading script : Please check the file size";
	}
}
?>
