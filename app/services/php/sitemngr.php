<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(!is_dir(ROOT.CACHE_FOLDER."sitemanager/")) {
	if(mkdir(ROOT.CACHE_FOLDER."sitemanager/",0777,true)) {
		chmod(ROOT.CACHE_FOLDER."sitemanager/",0777);
	}
}
if(isset($_REQUEST["action"])) {
	loadHelpers("specialcfgfiles");
	
	if($_REQUEST["action"]=="sitelist") {
		$txt="";
		$fp=ROOT.APPS_FOLDER;
		$f=scandir($fp);
		unset($f[0]);unset($f[1]);
		$cnt=1;
		foreach($f as $a) {
			$appCfg=getAppInfo($fp.$a);
			if($appCfg!=null) {
				$txt.="<tr>";
				$txt.="<td align=center><b><input class='selectrow' type=checkbox rel='{$a}' title='{$appCfg["title"]}' /></b></td>";
				$txt.="<td align=center><b>$cnt</b></td>";
				$txt.="<td class='title'>".$appCfg["title"]."</td>";
				$txt.="<td class='applink'>$a</td>";
				$txt.="<td>".$appCfg["company"]."</td>";
				$txt.="<td>".$appCfg["apptype"]."</td>";
				if($appCfg['mobile']=="true")
					$txt.="<td rel='{$appCfg['mobile']}' align=center class='okicon' style='background-position:center center;'></td>";
				else
					$txt.="<td rel='{$appCfg['mobile']}' align=center class='notokicon' style='background-position:center center;'></td>";
				if($appCfg['tablet']=="true")
					$txt.="<td rel='{$appCfg['tablet']}' align=center class='okicon' style='background-position:center center;'></td>";
				else
					$txt.="<td rel='{$appCfg['tablet']}' align=center class='notokicon' style='background-position:center center;'></td>";
				
				$txt.="<td class='db' title='{$appCfg["database"]}'>".substr($appCfg["database"],0,15)."</td>";
				$txt.="<td class='dbms' rel='{$appCfg['dbtype']}'>".strtoupper($appCfg["dbtype"])."</td>";
				$txt.="<td align=center class='devmode publish'>".$appCfg["publish"]."</td>";
				if($appCfg["access"]=="NA")
					$txt.="<td>".ucwords($appCfg["access"])."</td>";
				else
					$txt.="<td class='access'>".ucwords($appCfg["access"])."</td>";
				$txt.="<td align=center>".$appCfg["created"]."</td>";
				
				$txt.="<td align=center>";
				$txt.="<div name='viewsite' class='openicon minibtn' rel='$a' title='View Selected Site'></div>";
				if(strtoupper($appCfg["dbtype"])=="NA")
					$txt.="<div class='blankicon minibtn' rel='$a' title='No Database'></div>";
				else
					$txt.="<div name='editdbms' class='dbicon minibtn' rel='$a' title='Edit Site Database'></div>";
				$txt.="<div name='editsite' class='editicon minibtn' rel='$a' title='Edit Site Properties'></div>";
				$txt.="</td>";
				
				$txt.="</tr>";
				$cnt++;
			}	
		}
		echo $txt;
	} elseif($_REQUEST["action"]=="devmode") {
		$app=$_REQUEST["app"];
		$b=$_REQUEST["current"];
		$c=$_REQUEST["mode"];
		
		if($c=="*") $c="";
		if($b=="*") $b="";
		
		if($app=="admincp") {
			echo "The Root Administrative Site (admincp) Cannot be blocked.";
		} else {
			$fp=ROOT.APPS_FOLDER.$app;
			$data=file_get_contents($fp."/apps.cfg");
			$data=str_replace("PUBLISH_MODE=$b","PUBLISH_MODE=$c",$data);
			if(is_writable($fp."/apps.cfg")) {
				file_put_contents($fp."/apps.cfg",$data);
			} else {
				echo "The Config File Is Readonly";
			}
		}
	} elseif($_REQUEST["action"]=="accessmode") {
		$app=$_REQUEST["app"];
		$b=$_REQUEST["current"];
		$c=$_REQUEST["mode"];
		
		if($app=="admincp") {
			echo "The Root Administrative Site (admincp) Cannot be blocked.";
		} else {
			$fp=ROOT.APPS_FOLDER.$app;
			$data=file_get_contents($fp."/apps.cfg");
			$data=str_replace("ACCESS=$b","ACCESS=$c",$data);
			if(is_writable($fp."/apps.cfg")) {
				file_put_contents($fp."/apps.cfg",$data);
			} else {
				echo "The Config File Is Readonly";
			}
		}
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST["apps"])) {
		$apps=explode(",",$_REQUEST["apps"]);
		$n=count($apps)-1;
		foreach($apps as $a) {
			if(strlen($a)<=0) continue;
			if($a=="admincp") {
				echo "The Root Administrative Site (admincp) Cannot be deleted.<br/>";
			} else {
				$f1=ROOT.APPS_FOLDER.$a;
				$f2=ROOT.SERVICES_FOLDER."php/".$a."/";
				$x=deleteFolder($f1);
				if(!$x) {
					echo "Error In Deleting <b>$a</b> File System.<br/>";
				}
				$x=deleteFolder($f2);
				if(!$x) {
					echo "Error In Deleting <b>$a</b> Service System.<br/>";
				}
			}
		}
		//echo "Deleting $n AppSites Complete";
	} elseif($_REQUEST["action"]=="createapp") {
		$app=$_POST["sitecode"];
		$tmpl=$_POST["sitetemplate"];
		$dbHost=$_POST["sitedbhost"];
		
		$tmpl=APPROOT.MISC_FOLDER."apptemplates/$tmpl";
		
		$db=null;
		if(strlen($dbHost)>0) {
			$db=new Database($_POST["sitedbdriver"]);
			$db->connect($_POST["sitedbuser"],$_POST["sitedbpwd"],$_POST["sitedbhost"],$_POST["sitedbname"]);
		}
		$a=installTemplate($app,$tmpl,$db);
		if(!$a) {
			exit("<br/><br/>Error Occured While Installing Application<b>'$app'</b> From Template <b>'".getTemplateTitle($tmpl)."'</b>");
		}
		$a=configureAppCfg($app,$_POST);
		if(!$a) {
			exit("<br/><br/>Error Occured While Configuring Application <b>'$app'</b>.");
		}
		echo "Successfully installed <b>'$app'</b> from template <b>'".getTemplateTitle($tmpl)."'</b>";
	} elseif($_REQUEST["action"]=="cloneapp") {
		$baseapp=$_POST["baseapp"];
		$toapp=$_POST["sitecode"];
		$type=$_POST["clonetype"];
		$dbHost=$_POST["sitedbhost"];
		
		if($baseapp=="admincp" || $baseapp=="cms") {
			exit("The Root Administrative Site (admincp,cms) Cannot be cloned.");
		}
		
		if(cloneApp($baseapp,$toapp,$type))
			echo "Successfully cloned <b>'$baseapp'</b> to create <b>'$toapp'</b> using '".ucwords($type)."' Cloning.";
		else
			echo "Failed To Clone <b>'$baseapp'</b> to create <b>'$toapp'</b> using '".ucwords($type)."' Cloning.";
	} elseif($_REQUEST["action"]=="exportapp") {
		$siteCode=$_POST["sitecode"];
		$siteName=$_POST["sitename"];
		$type=$_POST["exporttype"];
		$saveas=$_POST["saveas"];
		
		if(!defined("ADMIN_APPSITES")) {
			$f=ROOT.CFG_FOLDER."lists/adminsites.lst";
			$f=file_get_contents($f);
			$f=explode("\n",$f);
			if(strlen($f[count($f)-1])==0) unset($f[count($f)-1]);
			define("ADMIN_APPSITES",implode(",",$f));
		}
		$acp=explode(",",ADMIN_APPSITES);
		if(in_array($siteCode, $acp)) {
			exit("The Root Administrative Site Cannot be Exported.");
		}
		$f=exportApp($siteCode,$type);
		if(file_exists($f)) {
			if($saveas=="download") {
				$f=SiteLocation.substr($f,strlen(ROOT));
				echo "url:$f";
			} elseif($saveas=="template") {
				$dir=APPROOT.MISC_FOLDER."apptemplates/";
				copy($f,"{$dir}/".basename($f));
			}
		} else {
			echo "Error While Exporting $app";
		}
	} elseif($_REQUEST["action"]=="flushpermissions" && isset($_REQUEST["app"])) {
		$app=$_REQUEST["app"];
		$f=ROOT.CACHE_PERMISSIONS_FOLDER."{$app}/";
		if(is_dir($f)) {
			$fs=scandir($f);
			foreach($fs as $a) {
				if($a=="." || $a=="..") continue;
				unlink("{$f}{$a}");
			}
			echo "Permissions For {$app} Reset Complete.";
		}
	} elseif($_REQUEST["action"]=="listappimages") {
		$dir=APPROOT.MISC_FOLDER."apptemplates/";
		$dirs=scandir($dir);
		unset($dirs[0]);unset($dirs[1]);
		foreach($dirs as $a) {
			$ext=substr($a,strlen($a)-3);
			if(strtolower($ext)=="zip") {
				$t=getTemplateTitle($a);
				$l=filesize($dir.$a);
				$d=date("d/m/Y",filectime($dir.$a));
				$ss="<tr rel='$a'><td align=center><input name=a type=radio /></td><td>$t</td><td align=center>$l bytes</td><td align=center>$d</td>";
				$ss.="<td align=right>";
				$ss.="<div name='delete' class='deleteicon minibtn' rel='$a' title='$t' style='background-position:center center;'></div>";
				$ss.="<div name='info' class='infoicon minibtn' rel='$a' title='$t' style='background-position:center center;'></div>";
				$ss.="</td></tr>";
				echo $ss;
			}
		}
	} elseif($_REQUEST["action"]=="uploadappimage") {
		$fname=$_FILES['appimage']['name'];
		$appFile=APPROOT.MISC_FOLDER."apptemplates/$fname";
		$appText=substr($appFile,0,strlen($appFile)-3)."ini";
		
		if(!move_uploaded_file($_FILES['appimage']['tmp_name'], $appFile)) {
			echo "Error While Uploading The AppImage file, please try again!";
			exit();
		}
		$s="";
		$s.="AppName=".getTemplateTitle($fname)."\n";
		$s.="Version=1.0\n";
		$s.="BuildID=\n";
		$s.="DeveloperID=\n";
		$s.="Author={$_SESSION['SESS_USER_NAME']} [{$_SESSION['SESS_USER_ID']}]\n";
		$s.="Author Mail={$_SESSION['SESS_USER_EMAIL']}\n";
		$s.="Created=".date("Y-m-d H:i:s")."\n";
		$s.="Remarks=\n";
		file_put_contents($appText,$s);
		exit("<script>parent.reloadAppImagesList();</script>");
	} elseif($_REQUEST["action"]=="deleteappimage") {
		$app=$_REQUEST["appimage"];
		$appFile=APPROOT.MISC_FOLDER."apptemplates/$app";
		$appText=substr($appFile,0,strlen($appFile)-3)."ini";
		
		if(file_exists($appFile)) unlink($appFile);
		if(file_exists($appText)) unlink($appText);
	} elseif($_REQUEST["action"]=="infoappimage") {
		$app=$_REQUEST["appimage"];
		$appFile=APPROOT.MISC_FOLDER."apptemplates/$app";
		$appText=substr($appFile,0,strlen($appFile)-3)."ini";
		if(file_exists($appText)) {
			$s=file_get_contents($appText);
			$s="<pre>{$s}</pre>";
			echo $s;
		} else {
			echo "<h3>No Extra Information Found About AppImage.</h3>";
		}
	} elseif($_REQUEST["action"]=="dlgs" && isset($_REQUEST["dlg"])) {
		$dlg=$_REQUEST["dlg"];
		loadModuleLib("sitemngr","dlgs/$dlg");
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("sitemngr","help");
	} 
}
exit();

function getTemplateTitle($a) {
	if(file_exists($a)) $a=basename($a);
	$t=str_replace("_"," ",$a);
	$t=str_replace(".zip","",$t);
	$t=str_replace(".tar","",$t);
	$t=str_replace(".gz","",$t);
	$t=ucwords($t);
	return $t;
}
function deleteFolder($dirname) {
   if(is_dir($dirname)) $dir_handle = opendir($dirname);
   else {
	   if(file_exists($dirname)) return true;
	   else return true;
   }
   if(!$dir_handle) return false;
	while($file = readdir($dir_handle)) {
		if ($file != "." && $file != "..") {
			if (!is_dir($dirname."/".$file))
				unlink($dirname."/".$file);
			else
				deleteFolder($dirname.'/'.$file);     
		}
	}
   closedir($dir_handle);
   rmdir($dirname);
   return true;
}
function copyFolder($source, $target) {
	if (!is_dir($source)) {//it is a file, do a normal copy
		copy($source, $target);
		chmod($target,0777);
		return;
	}
	//it is a folder, copy its files & sub-folders
	@mkdir($target,0777,true);
	chmod($target,0777);
	$d = dir($source);
	$navFolders = array('.', '..','tmp');
	while (false !== ($fileEntry=$d->read() )) {//copy one by one
		//skip if it is navigation folder . or ..
		if (in_array($fileEntry, $navFolders) ) {
			continue;
		}
		//do copy
		$s = "$source/$fileEntry";
		$t = "$target/$fileEntry";
		copyFolder($s, $t);
	}
	$d->close();
}
function getCfgArr($f) {
	$arr=array();
	$f=file_get_contents($f);
	$f=explode("\n",$f);
	foreach($f as $a) {
		if(strpos($a,"=")>1 && strpos("!!".$a,"#")!=2) {
			$a=explode("=",$a);
			$arr[$a[0]]=$a[1];
		}
	}
	return $arr;
}
function getAppInfo($appPath) {
	$cfg=$appPath."/apps.cfg";	
	$dbcfg=$appPath."/config/db.cfg";
	
	if(!file_exists($cfg)) return null;
	
	$cfgArr=getCfgArr($cfg);
	
	$arr=array();
		
	$d1=filectime($appPath);
	$arr["created"]=date("d/m/Y h:i A", $d1);
		
	$arr["apptype"]=$cfgArr["APPS_TYPE"];
	
	if(isset($cfgArr["APPS_COMPANY"]))
		$arr["company"]=$cfgArr["APPS_COMPANY"];
	else
		$arr["company"]="";
	$arr["title"]=$cfgArr["APPS_NAME"]." :: v".$cfgArr["APPS_VERS"];
	
	$arr["access"]=$cfgArr["ACCESS"];
	if(strlen($arr["access"])==0) $arr["access"]="NA";
	
	$arr["mobile"]="false";
	$arr["tablet"]="false";
	
	if(isset($cfgArr["MOBILE_PAGE"]) && strlen($cfgArr["MOBILE_PAGE"])>0) {
		$arr["mobile"]="true";
	}
	if(isset($cfgArr["TABLET_PAGE"]) && strlen($cfgArr["TABLET_PAGE"])>0) {
		$arr["tablet"]="true";
	}
	if(file_exists($dbcfg)) {
		$arr["db"]="true";
		
		$dbCfgArr=getCfgArr($dbcfg);
		$arr["database"]=$dbCfgArr["DB_HOST"]."/".$dbCfgArr["DB_DATABASE"];
		$arr["dbtype"]=$dbCfgArr["DB_DRIVER"];
		$arr["dbname"]=$dbCfgArr["DB_DATABASE"];
		$arr["dbuser"]=$dbCfgArr["DB_USER"];
		$arr["dbpass"]=$dbCfgArr["DB_PASSWORD"];
	} else {
		$arr["db"]="false";
		$arr["database"]="NA";
		$arr["dbtype"]="NA";
	}
	
	if(isset($cfgArr["PUBLISH_MODE"]))
		$arr["publish"]=$cfgArr["PUBLISH_MODE"];
	else
		$arr["publish"]="publish";
	
	return $arr;
}
function getSystemTables($app) {
	$sysTables=array("do_forms","do_links","do_lists","do_reports","do_views","do_search","do_views","do_contents");
	
	$sysTablesFile=ROOT.APPS_FOLDER.$app."config/systables.lst";
	if(file_exists($sysTablesFile)) {
		$sysTables=SpecialCfgFiles::LoadListFile($sysTablesFile);
	}
	
	return $sysTables;
}

//Installs App Templates
function exportApp($app,$type) {
	$sysTables=getSystemTables($app);
	
	$f1=ROOT.APPS_FOLDER.$app."/";
	$f2=ROOT.SERVICES_FOLDER."php/".$app."/";
	$f3=ROOT.CACHE_FOLDER."sitemanager/".$app."/";
	
	$fZip=ROOT.CACHE_FOLDER."sitemanager/$app.zip";	
	$dbF=ROOT.APPS_FOLDER.$app."/config/db.cfg";
	
	$dbCon=null;
	
	if(file_exists($fZip)) unlink($fZip);
	
	if(file_exists($dbF)) {
		$dbF=SpecialCfgFiles::LoadCfgFile($dbF);
		$db1=$dbF["DBCONFIG"];
		$dbCon=new Database($db1["DB_DRIVER"]);
		$dbCon->connect($db1["DB_USER"],$db1["DB_PASSWORD"],$db1["DB_HOST"],$db1["DB_DATABASE"]);
	}
	
	if(file_exists($f3)) deleteFolder($f3);
	
	$a=mkdir($f3,0777,true);
	if(!$a) {
		echo "Error Creating Cache Folder For App Installation";
		return false;
	}
	chmod($f3,0777);
		
	copyFolder($f1, $f3."app/");
	copyFolder($f2, $f3."services/");
	$a=createAppDBCache($dbCon, $f3, $sysTables);
	if(!$a) {
		echo "Error Creating SQL Cache";
		return false;
	}
	if($type=="exact") {		
	} elseif($type=="structure") {
		unlink($f3."sql/data.sql");
	} elseif($type=="barebone") {
		unlink($f3."sql/sysdata.sql");
		unlink($f3."sql/data.sql");
	} 
	loadHelpers("zipper");
	$a=zipFolder($f3,$fZip);
	deleteFolder($f3);
	return $fZip;
}
function installTemplate($app,$tmpl,$db) {
	$f1=ROOT.APPS_FOLDER.$app."/";
	$f2=ROOT.SERVICES_FOLDER."php/".$app."/";
	$f3=ROOT.CACHE_FOLDER."sitemanager/".$app."/";
	
	/*if(file_exists($f1) || file_exists($f2)) {
		echo "Application/Service With This Name/Category Already Exists.";
		return false;
	}*/
	
	if(file_exists($f1)) deleteFolder($f1);
	if(file_exists($f2)) deleteFolder($f2);
	if(file_exists($f3)) deleteFolder($f3);
	
	$a=mkdir($f1,0777,true);
	if(!$a) {
		echo "Error Creating App Folder For App Installation";
		return false;
	}
	$a=mkdir($f2,0777,true);
	if(!$a) {
		echo "Error Creating Services Folder For App Installation";
		return false;
	}
	$a=mkdir($f3,0777,true);
	if(!$a) {
		echo "Error Creating Cache Folder For App Installation";
		return false;
	}
	chmod($f1,0777);
	chmod($f2,0777);
	chmod($f3,0777);
	loadHelpers("zipper");
	
	unzipFile($tmpl, $f3);
	
	if(file_exists($f3."app/")) {
		copyFolder($f3."app/",$f1);
	}
	if(file_exists($f3."services/")) {
		copyFolder($f3."services/",$f2);
	}
	
	if($db!=null && $db->isOpen()) {
		$a=restoreAppDBCache($db, $f3, "exact");
		if(!$a) {
			echo "Error Installing SQL Files For App Installation";
			return false;
		}
	}
	deleteFolder($f3);
	return true;
}
function cloneApp($baseapp,$toapp,$type) {
	$sysTables=getSystemTables($baseapp);
	
	$cacheDir=ROOT.CACHE_FOLDER."sitemanager/".$toapp."/";
	if(file_exists($cacheDir)) deleteFolder($cacheDir);
	mkdir($cacheDir,0777,true);
	chmod($cacheDir,0777);
			
	$appFrm1=ROOT.APPS_FOLDER.$baseapp."/";
	$appFrm2=ROOT.SERVICES_FOLDER."php/".$baseapp."/";		
	$appTo1=ROOT.APPS_FOLDER.$toapp."/";
	$appTo2=ROOT.SERVICES_FOLDER."php/".$toapp."/";
	
	/*if(file_exists($appTo1) || file_exists($appTo2)) {
		echo "Application/Service With This Name/Category Already Exists.";
		return false;
	}*/
	
	if(file_exists($appTo1)) deleteFolder($appTo1);
	if(file_exists($appTo2)) deleteFolder($appTo2);
	
	$a=mkdir($appTo1,0777,true);
	if(!$a) {
		echo "Error Creating App Folder For App Installation";
		return false;
	}
	$a=mkdir($appTo2,0777,true);
	if(!$a) {
		echo "Error Creating Services Folder For App Installation";
		return false;
	}
	chmod($appTo1,0777);
	chmod($appTo2,0777);
	
	if(is_dir($appFrm2)) copyFolder($appFrm2, $appTo2);
	if(is_dir($appFrm1)) {
		$dirs=scandir($appFrm1);
		unset($dirs[0]);unset($dirs[1]);
		
		if($type=="exact") {
			foreach($dirs as $d) {
				$df=$appFrm1."$d";
				if($d!="tmp") {
					copyFolder($df, $appTo1."$d");
				}
			}
			mkdir($appTo1."tmp/",0777,true);
			chmod($appTo1."tmp/",0777);
		} elseif($type=="structure" || $type=="barebone") {
			foreach($dirs as $d) {
				$df=$appFrm1."$d";
				if($d!="tmp" && $d!="userdata") {
					copyFolder($df, $appTo1."$d");
				}
			}
			mkdir($appTo1."tmp/",0777,true);
			chmod($appTo1."tmp/",0777);
			
			mkdir($appTo1."userdata/",0777,true);
			chmod($appTo1."userdata/",0777);
			if(is_dir($appFrm1."userdata/")) {
				$udirs=scandir($appFrm1."userdata/");
				unset($udirs[0]);unset($udirs[1]);
				foreach($udirs as $d) {
					$df=$appFrm1."userdata/$d";
					if(is_file($df)) copy($df,$appTo1."userdata/$d");
					else {
						mkdir($appTo1."userdata/$d",0777,true);
						chmod($appTo1."userdata/$d",0777);
					}
				}
			}
		} else {
			exit("Clone Type Not Supported");
		}
	}
	
	$a=configureAppCfg($toapp,$_POST);
	if(!$a) {
		exit("<br/><br/>Error Occured While Configuring Application <b>'$toapp'</b>.");
	}
	
	//Clone DB
	$db1=$appFrm1."config/db.cfg";
	$db2=$appTo1."config/db.cfg";
	$dbCon1=null;$dbCon2=null;
	
	if(file_exists($db1)) {
		$db1=SpecialCfgFiles::LoadCfgFile($db1);
		$db1=$db1["DBCONFIG"];
	}
	if(file_exists($db2)) {
		$db2=SpecialCfgFiles::LoadCfgFile($db2);
		$db2=$db2["DBCONFIG"];
	}
	$dbCon1=new Database($db1["DB_DRIVER"]);
	$dbCon1->connect($db1["DB_USER"],$db1["DB_PASSWORD"],$db1["DB_HOST"],$db1["DB_DATABASE"]);
	$dbCon2=new Database($db2["DB_DRIVER"]);
	$dbCon2->connect($db2["DB_USER"],$db2["DB_PASSWORD"],$db2["DB_HOST"],$db2["DB_DATABASE"]);
	
	$a=createAppDBCache($dbCon1, $cacheDir, $sysTables);
	$a=restoreAppDBCache($dbCon2, $cacheDir, $type);
	$a=configureAppDb($dbCon2, $toapp);
	if(!$a) {
		exit("<br/><br/>Error Occured While Configuring Application Database For <b>'$toapp'</b>.");
	}
	
	deleteFolder($cacheDir);
	return $a;
}
function configureAppCfg($app,$data) {
	$appDir=ROOT.APPS_FOLDER.$app."/";
	$f1=$appDir."apps.cfg";
	$f2=$appDir."config/db.cfg";
	
	if(!is_dir($appDir."config/")) {
		$a=mkdir($appDir."config/",0777,true);
		if($a) {
			chmod($appDir."config/",0777);
		} else {
			echo "Error Configuring App Installation";
			return false;
		}
	}
	
	//Configure apps.cfg file
	if(file_exists($f1)) {
		$cfgArr=SpecialCfgFiles::LoadCfgFile($f1);
		$cfgArr['DEFINE']['APPS_NAME']=$data["sitename"];
		SpecialCfgFiles::SaveCfgFile($f1,$cfgArr);
	}
	
	//Configure db.cfg file
	if(strlen($data["sitedbhost"])>0 && strlen($data["sitedbname"])>0) {
		if(file_exists($f2)) {
			$cfgArr=SpecialCfgFiles::LoadCfgFile($f2);
			$cfgArr['DBCONFIG']['DB_DRIVER']=$data["sitedbdriver"];
			$cfgArr['DBCONFIG']['DB_HOST']=$data["sitedbhost"];
			$cfgArr['DBCONFIG']['DB_DATABASE']=$data["sitedbname"];
			$cfgArr['DBCONFIG']['DB_USER']=$data["sitedbuser"];
			$cfgArr['DBCONFIG']['DB_PASSWORD']=$data["sitedbpwd"];
			SpecialCfgFiles::SaveCfgFile($f2,$cfgArr);
		} else {
			$cfgArr['DBCONFIG']['DB_DRIVER']=$data["sitedbdriver"];
			$cfgArr['DBCONFIG']['DB_HOST']=$data["sitedbhost"];
			$cfgArr['DBCONFIG']['DB_DATABASE']=$data["sitedbname"];
			$cfgArr['DBCONFIG']['DB_USER']=$data["sitedbuser"];
			$cfgArr['DBCONFIG']['DB_PASSWORD']=$data["sitedbpwd"];
			
			$cfgArr['DBCONFIG']['DB_APPS']="do";
			$cfgArr['DBCONFIG']['DB_READ_ONLY']="false";
			$cfgArr['DBCONFIG']['BLOCK_STATEMENTS']="";
			
			SpecialCfgFiles::SaveCfgFile($f2,$cfgArr);
			
		}
	}
	return true;
}
function configureAppDb($dbCon, $app) {
	if($dbCon!=null && $dbCon->isOpen()) {
		$dataArr=array();
		$dataArr["doc"]=date("Y-m-d");
		$dataArr["doe"]=date("Y-m-d");
		$dataArr["toc"]=date("H:i:s");
		$dataArr["toe"]=date("H:i:s");
		$dataArr["tsoc"]=date("Y-m-d H:i:s");
		$dataArr["tsoe"]=date("Y-m-d H:i:s");
		$dataArr["last_modified"]=date("Y-m-d H:i:s");
		
		$tables=$dbCon->getTableList();
		
		foreach($tables as $a) {
			$cols=$dbCon->getColumnList($a);
			$colNames=array_keys($cols);
			$sql=array();
			foreach($dataArr as $m=>$n) {
				if(in_array($m,$colNames)) {
					array_push($sql,"$m='$n'");
				}
			}
			$sql=implode(", ",$sql);
			
			if(strlen($sql)>0) {
				$sql1="UPDATE $a SET $sql";
				$dbCon->executeCommandQuery($sql1);
				if(in_array("site",$colNames)) {
					$sql2="UPDATE $a SET site='$app' where site!='*'";
					$dbCon->executeCommandQuery($sql2);
				}
				/*if(in_array("privilege",$colNames)) {
					$sql2="UPDATE $a SET privilege='*' where site!='*'";
					$dbCon->executeCommandQuery($sql2);
				}*/
			}
		}
		return true;
	}
	return false;
}

function createAppDBCache($dbCon1, $cacheDir, $sysTables=array()) {
	if($dbCon1!=null && $dbCon1->isOpen()) {
		$sqlCacheDir=$cacheDir."sql/";
		mkdir($sqlCacheDir,0777,true);
		chmod($sqlCacheDir,0777);
		
		$tables=$dbCon1->getTableList();
		
		$sql=$dbCon1->getSchema();
		file_put_contents("{$sqlCacheDir}schema.sql",$sql);
		
		$sql="";
		foreach($tables as $a) {
			if(in_array($a,$sysTables)) {
				$sd=$dbCon1->getTableInserts($a);
				$sql.="{$sd}\n\n";
			}
		}
		file_put_contents("{$sqlCacheDir}sysdata.sql",$sql);
		
		$sql="";
		foreach($tables as $a) {
			if(!in_array($a,$sysTables)) {
				$sd=$dbCon1->getTableInserts($a);
				$sql.="{$sd}\n\n";
			}
		}
		file_put_contents("{$sqlCacheDir}data.sql",$sql);
		return true;
	}
	return false;
}
function restoreAppDBCache($dbCon, $cacheDir, $restoreType) {
	if($dbCon!=null && $dbCon->isOpen()) {
		$sqlCacheDir=$cacheDir."sql/";
		if(!is_dir($sqlCacheDir)) return true;
		
		$restoreFiles=array(
				"exact"=>array("install.sql","schema.sql","sysdata.sql","data.sql","demo.sql"),
				"structure"=>array("install.sql","schema.sql","sysdata.sql","demo.sql"),
				"barebone"=>array("install.sql","schema.sql"),
			);
		if(!array_key_exists($restoreType,$restoreFiles)) return false;
		foreach($restoreFiles[$restoreType] as $f) {
			if(file_exists("{$sqlCacheDir}$f")) {
				$fileContent = file("{$sqlCacheDir}$f");
				$query = "";
				foreach($fileContent as $sql_line) {
					if(strlen(trim($sql_line))>0 && strpos($sql_line, "--") === false) {
						$query.= $sql_line;
						if(substr(rtrim($query), strlen(rtrim($query))-1)==';') {
							$rs=$dbCon->executeCommandQuery($query);
							$query="";
						}
					}
				}
			}
		}
		return true;
	}
	return false;
}
?>
