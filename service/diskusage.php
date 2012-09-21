<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

if(isset($_REQUEST["action"])) {
	$user=$_SESSION['SESS_USER_ID'];
	$path=ROOT;
	if(isset($_REQUEST["dir"])) {
		$path.=$_REQUEST["dir"];
	}
	$path=str_replace("//","/",$path);
	$path=str_replace("//","/",$path);
	
	if($_REQUEST["action"]=="filetree") {
		$files = scandir($path);
		unset($files[0]);
		unset($files[1]);
		natcasesort($files);
		
		if(count($files) > 0 ) {
			echo "<ul class=\"jqueryFileTree\" style=\"display:none;\">";
			foreach($files as $file ) {				
				if(is_dir($path . $file) ) {
					$writable=is_writable($path . $file)?"":"readonly";
					echo "<li class=\"directory collapsed $writable\"><a href=\"#\" rel=\"" . htmlentities($_REQUEST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
				}
			}			
			echo "</ul>";
		}
		
		/*if($dirBase=="/") {
			echo "<ul class=\"jqueryFileTree\" style=\"display:none;\">";
			echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"/public/\">Public</a></li>";
			echo "</ul>";
		}*/
	} elseif($_REQUEST["action"]=="diskusetbl") {
		$files=scandir($path);
		$filetable="";
		unset($files[0]);unset($files[1]);
		foreach($files as $f) {
			$name=$f;
			$size=0;
			$sizes="";
			$type=filetype($path.$f);
			$fp=str_replace(ROOT,"/",$path.$f);
			
			if(is_file($path."/".$f)) $size=filesize($path."/".$f);			
			else {
				$size=getDirSize($path."/".$f);
				$fp.="/";
			}
			if(strlen($size)<=0) $size=0;
			else {
				$sizes=round(($size/1024),2);
			}
			$filetable.="<tr><td align=left>$name</td><td align=center>$size</td><td align=center>$sizes</td><td align=left>$fp</td><td align=left>$type</td></tr>";
		}
		echo $filetable;
	}
}
function getDirSize($dir) {
	$size=0;
	$fs=scandir($dir);
	unset($fs[0]);unset($fs[1]);
	foreach($fs as $f) {
		if(is_dir($dir."/".$f)) {
			$size+=getDirSize($dir."/".$f);
		} else {
			$size+=filesize($dir."/".$f);
		}
	}
	return $size;
}
?>
