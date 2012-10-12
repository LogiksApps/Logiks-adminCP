<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(isset($_REQUEST['forsite'])) {
	$dir=ROOT.BACKUP_FOLDER.$_REQUEST['forsite']."/";
} else {
	$dir=ROOT.BACKUP_FOLDER."/";
}
$dirStamp=date("Y-m-d H:i:s");
$backup_dir=$dir.$dirStamp."/";

if(!is_dir($dir)) {
	mkdir($dir,0777,true);
	chmod($dir,0777);
}
if(!(is_dir($dir) && is_writable($dir))) {
	printErr("Backup Folder Is Not Found Or Is Not Writtable ...");
	exit();
}
$htaccessFile=ROOT.BACKUP_FOLDER."/.htaccess";
if(!file_exists($htaccessFile))
	file_put_contents($fs,"deny from all\n");


loadHelpers(array("files"));

if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="list") {
		$arr=array();
		$fs=scandir(ROOT.APPS_FOLDER);
		unset($fs[0]);//unset($fs[1]);
		$fs[1]="Core";
		natsort($fs);
		foreach($fs as $a) {
			$fa=ROOT.APPS_FOLDER."$a";
			if(is_file($fa)) continue;
			
			echo "<tr class='subheader ui-state-active'><th colspan=10>
						<div onclick=\"createBackup('$a')\" title='Create Backup Image Now' class='buttondiv openicon' style='float:right;cursor:pointer;width:22px;padding:2px;'></div>
						<h3 class='siteicon'>".ucwords($a)." </h3></th></tr>";
			$f=ROOT.BACKUP_FOLDER."$a/";
			if(file_exists($f) && is_dir($f)) {
				$lst=scandir($f);
				unset($lst[0]);unset($lst[1]);
				if(sizeof($lst)>0) {
					foreach($lst as $x) {
						$fx=ROOT.BACKUP_FOLDER."$a/$x";
						$dt1=date ("F d Y", filemtime($fx));
						$dt2=date ("H:i:s", filemtime($fx));
						$size=getFileSizeInString(fileSize($fx));
						$s="<tr class='backup' site='$a' >";
						$s.="<td align=center><input rel='$a/$x' type=checkbox /></td>";
						$s.="<td name=date>$dt1</td>";
						$s.="<td name=time align=center>$dt2</td>";
						$s.="<td name=size align=center>$size</td>";
						$s.="<td name=download align=center><div rel='$a/$x' title='Download This Image' class='buttondiv downloadicon hover'></div></td>";
						$s.="<td name=restore align=center><div rel='$a/$x' title='Restore Site Using This Image' class='buttondiv restoreicon hover'></div></td>";
						$s.="<td name=delete align=center><div rel='$a/$x' title='Delete This Image' class='buttondiv deleteicon hover'></div></td>";
						$s.="<td name=site align=right><b>$a</b></td>";
						$s.="</tr>";
						
						echo $s;
					}
					echo "<tr><td colspan=10>&nbsp;</td></tr>";
				} else {
					echo "<tr><td colspan=10 align=center><h3 style='color:maroon;'>No Backup Found Till Date</h3></td></tr>";
				}
			} else {
				mkdir($f,0777,true);
				chmod($f,0777);
				echo "<tr><td colspan=10 align=center><h3 style='color:maroon;'>No Backup Found Till Date</h3></td></tr>";
			}			
		}
	} elseif($_REQUEST["action"]=="download") {
		if(isset($_REQUEST["ref"])) {
			$file=ROOT.BACKUP_FOLDER.$_REQUEST["ref"];
			downloadFile($file);
		} else {
			printErr("FileNotFound","Download Backup Index Not Found");
		}
	} elseif($_REQUEST["action"]=="backup") {
		if(isset($_REQUEST["forsite"])) {
			createBackup($_REQUEST['forsite'],$backup_dir);
		} else {
			printErr("DataNotFound","What To Backup?");
		}
	} elseif($_REQUEST["action"]=="restore") {
		if(isset($_REQUEST["forsite"])) {
			if(isset($_REQUEST["ref"])) {
				$file=ROOT.BACKUP_FOLDER.$_REQUEST["ref"];
				restoreBackup($_REQUEST['forsite'],$file);
			} else {
				printErr("FileNotFound","Restore Index Not Found");
			}
		} else {
			printErr("DataNotFound","What To Backup?");
		}
	} elseif($_REQUEST["action"]=="delete") {
		if(isset($_REQUEST["ref"])) {
			$file=ROOT.BACKUP_FOLDER.$_REQUEST["ref"];
			unlink($file);
		} else {
			printErr("FileNotFound","Download Backup Index Not Found");
		}
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("sitebackup","help");
	} 
}
exit();
function downloadFile($f) {
	if(!file_exists($f)) {
		printErr("FileNotFound","Download Backup Index Not Found");
		return;
	}
	$file_name=basename($f);
	$file = @fopen($f,"rb");
	if ($file){
		header("Cache-Control: public");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=\"{$file_name}\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($f));
		while(!feof($file)){
			echo fread($file, 500*1024);
			flush();
			if (connection_status()!=0){
				@fclose($file);
				die();
			}
		}
		@fclose($file);
	}
}
function restoreBackup($site,$file) {
	echo "Restoration Of $site can only be done via CMS.<br/>Thank You.";
}
function createBackup($site,$backup_dir) {
	if($site=='Core') {
		$sourceTobackup=ROOT;
		$serviceTobackup=ROOT."services/php/".$site."/";
	} else {
		$sourceTobackup=ROOT.APPS_FOLDER.$site."/";
		$serviceTobackup=ROOT."services/php/".$site."/";
	}
	mkdir($backup_dir,0777,true);
	chmod($backup_dir,0777);
	if(is_dir($backup_dir) && strlen($sourceTobackup)>0) {
		if(file_exists($sourceTobackup)) Zip($sourceTobackup, $backup_dir."www.zip");
		if(file_exists($serviceTobackup)) Zip($serviceTobackup, $backup_dir."service.zip");
		if(file_exists($sourceTobackup."config/db.cfg")) backup_tables($backup_dir);
		
		finalBackup($backup_dir,ROOT.BACKUP_FOLDER.$site."/".date("Y-m-d H:i:s").".zip");
		echo "Backup For '$site' Complete On ".date("Y-m-d H:i:s");
		doDeleteDir($backup_dir);
	} else {
		echo "Backup For '$site' Failed To Create Cache Folder.";
	}
}

//-------------------------internal functions------------------------------------

function unzip($zip_file,$unzip_dir){	
	$zip = new ZipArchive;
	if ($zip->open($zip_file) === TRUE) {
		 for($i = 0; $i < $zip->numFiles; $i++) {
								 
				$zip->extractTo($unzip_dir, array($zip->getNameIndex($i)));				
				if($zip->getNameIndex($i)=='www.zip'){									
					unzip($unzip_dir.$zip->getNameIndex($i),$unzip_dir);
					unlink($unzip_dir.$zip->getNameIndex($i));
				}
				if($zip->getNameIndex($i)=='service.zip'){									
					unzip($unzip_dir.$zip->getNameIndex($i),ROOT."services/php/".$_REQUEST["forsite"]."/");
					unlink($unzip_dir.$zip->getNameIndex($i));
				}
				if($zip->getNameIndex($i)=='sql.zip'){
					unzip($unzip_dir.$zip->getNameIndex($i),$unzip_dir);
					unlink($unzip_dir.$zip->getNameIndex($i));					
					doDBRestore($unzip_dir.'db-backup.sql');
				}
				
								
			}
		$zip->close();
	} else
	   return false;
}
/*-------------------------------zipping codes----------------------------------------------------------------*/
function recurse_zip($src,&$zip,$path) {
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if(is_readable($src."/".$file)){
				if ( is_dir($src . '/' . $file) ) {
					recurse_zip($src . '/' . $file,$zip,$path);
				} else {					 
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
					//echo $src . '/' . $file."<br>";
				}
			} else{
				echo "Nonreadable :".$src."/".$file."<br>";
			}
			
		}
	}
	closedir($dir);
}
function Zip($src,$dst='') {
	if(substr($src,-1)==='/') {$src=substr($src,0,-1);}
	if(substr($dst,-1)==='/') {$dst=substr($dst,0,-1);}
	$path=strlen(dirname($src).'/');
	$filename=substr($src,strrpos($src,'/')+1).'.zip';
	$dst=empty($dst)? $filename : $dst;
	//@unlink($dst);
	$zip = new ZipArchive;
	$res = $zip->open($dst, ZipArchive::CREATE);
	if($res !== TRUE){
		echo 'Error: Unable to create zip file';
		exit;
	}
	if(is_file($src)) {		
		$zip->addFile($src,substr($src,$path));		
	}
	else {
		if(!is_dir($src)){
			 $zip->close();
			 //@unlink($dst);
			 echo 'Error: File not found';
			 exit;
		}
		recurse_zip($src,$zip,$path);
	}
	
	$zip->close();       
	return $dst;
}

function foldersize($path) {
    $total_size = 0;
    $files = scandir($path);
    foreach($files as $t) {
        if (is_dir(rtrim($path, '/') . '/' . $t)) {
            if ($t<>"." && $t<>"..") {
                $size = foldersize(rtrim($path, '/') . '/' . $t);
                $total_size += $size;
            }
        } else {
            $size = filesize(rtrim($path, '/') . '/' . $t);
            $total_size += $size;
        }   
    }
    return $total_size;
}
/*-----------------------------------------------------------------------------------------------*/
function getDBControls($site) {
	$dbFile=ROOT.APPS_FOLDER.$site."/config/db.cfg";
	if(file_exists($dbFile)) {
		LoadConfigFile($dbFile);
		$con=new Database($GLOBALS['DBCONFIG']["DB_DRIVER"]);
		$con->connect($GLOBALS['DBCONFIG']["DB_USER"],$GLOBALS['DBCONFIG']["DB_PASSWORD"],$GLOBALS['DBCONFIG']["DB_HOST"],$GLOBALS['DBCONFIG']["DB_DATABASE"]);
		return $con;
	} else {
		printErr("NotSupported","DB Configuration Missing For Site");
	}
}
/*
function doDBRestore($file){
	$link =getDBControls($_REQUEST["forsite"]); 
	$sql_file=$file;
	$file_content = file($sql_file);
	$query = "";
	foreach($file_content as $sql_line){
		if(trim($sql_line) != "" && strpos($sql_line, "--") === false){
			$query .= $sql_line;
			if (substr(rtrim($query), -1) == ';') {
				//echo $query;
				$result = $link->executeQuery($query);
				$query = "";
			}
		}
	}
	echo $sqlfile;
	unlink($sql_file);
}
* */
function backup_tables($dir,$tables = '*'){ 
	$link =getDBControls($_REQUEST["forsite"]);  
	//get all of the tables    
	if($tables == '*'){
		$tables = $link->getTableList();   
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}	
	//cycle through
	foreach($tables as $table) { 
		$result =  $link->executeQuery('SELECT * FROM '.$table);
		$num_fields = $link->columnCount($result);

		$return.= 'DROP TABLE IF EXISTS '.$table.';';    
		$r=$link->executeQuery('SHOW CREATE TABLE '.$table);
		$row2 =$link->fetchData($r,"array");    
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) {
			while($row = $link->fetchData($result,"array")) {
				$return.= 'INSERT INTO '.$table.' VALUES(';        
				for($j=0; $j<$num_fields; $j++) {                
					$row[$j] = addslashes($row[$j]);          
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	$str='db-backup.sql';
	//save file  
	$handle = fopen($dir.$str,'w+') or die("could not open");
	fwrite($handle,$return);
	fclose($handle);
	Zip($dir.$str, $dir."sql.zip");  
	unlink($dir.$str);  
}
function finalBackup($source,$dest){
	Zip($source,$dest);
}
function doDeleteDir($path){
	return is_file($path)? @unlink($path): array_map('doDeleteDir',glob($path.'/*'))==@rmdir($path);
}
?>
