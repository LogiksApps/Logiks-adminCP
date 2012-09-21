<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

$p="";
if(isset($_REQUEST["pwd"])) {
	if(strtolower($_REQUEST["pwd"])=="root") { $p=ROOT.HOOKS_FOLDER; }
	else {
		$p=ROOT.APPS_FOLDER.$_REQUEST["pwd"]."/";
		if(file_exists($p."config/folders.cfg")) {
			$arr=parseConfigFile($p."config/folders.cfg");
			if(array_key_exists("APPS_HOOKS_FOLDER",$arr)) {
				$p=$p.$arr["APPS_HOOKS_FOLDER"]["value"];
			} else {
				$p=$p."misc/hooks/";
			}
		} else {
			$p=$p."misc/hooks/";
		}
	}
} else {
	exit("ERROR Listing HOOKS");
}


if(isset($_REQUEST["create"])) {
	$f=$p.$_REQUEST["create"];
	$d=dirname($f);
	if(!(file_exists($d) && is_dir($d))) {
		@mkdir($d,0777,true);
		@chmod($d,0777);
	}
	file_put_contents($f,"");
	@chmod($f,0777);
} elseif(isset($_REQUEST["block"])) {	
	$f=$p.$_REQUEST["block"];
	if(file_exists($f) && is_file($f)) {
		if(strpos($f,"~")>2) {
			$a=rename($f,dirname($f)."/".substr(basename($f),1));
			if(!$a) echo "Error While UnBlocking Hook.";
		} else {
			$a=rename($f,dirname($f)."/~".basename($f));
			if(!$a) echo "Error While Blocking Hook.";
		}
	}
} elseif(isset($_REQUEST["delete"])) {
	$f=$p.$_REQUEST["delete"];
	if(file_exists($f) && is_file($f)) {
		$a=unlink($f);
		if(!$a) echo "Error While Deleting Hook To File.";	
	}
} elseif(isset($_REQUEST["save"])) {
	if(!isset($_POST["code"])) exit();
	
	$f=$p.$_REQUEST["save"];
	if(file_exists($f) && is_file($f)) {
		if(is_writable($f)) {
			$data=$_POST['code'];
			$data=cleanText($data);
			$a=file_put_contents($f,$data);
			if(!$a) echo "Error While Saving Hook To File.";			
		} else {
			echo "Target Hook File Is Not Writable.";
		}
	} else {
		echo "Could Not Find Target Hook File.";
	}
	exit();
} elseif(isset($_REQUEST["fetch"])) {
	$f=$p.$_REQUEST["fetch"];
	if(file_exists($f) && is_file($f)) {
		echo file_get_contents($f);
	}
	exit();
} elseif(isset($_REQUEST["list"])) {
	if(file_exists($p) && is_dir($p)) {
		$str="";
		$fs=scandir($p);
		unset($fs[0]);unset($fs[1]);		
		if(count($fs)>0) {
			$str="<ul>";
			foreach($fs as $a) {
				$str.="<li><h3>$a</h3>";
				$fs1=scandir($p.$a);
				unset($fs1[0]);unset($fs1[1]);
				if(count($fs)>0) {
					$str.="<ul>";
					foreach($fs1 as $b) {
						$th="{$a}/{$b}";
						$str.="<li fl='$th'>$b</li>";
					}
					$str.="</ul>";
				}			
				$str.="</li>";
			}
			$str.="</ul>";
		} else {
			$str="<h3 align=center>0 States Found</h3>";
		}
		echo $str;
	} else {
		echo "<h3 align=center>Hooks Not Supported</h3>";
	}
	exit();
}
?>
