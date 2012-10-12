<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);

if(isset($_REQUEST["cfg"])) {
	$cfg=$_REQUEST["cfg"];
	if(isset($_REQUEST["schema"])) $schema=$_REQUEST["schema"]; else $schema=$cfg;
	loadModule("cfgeditor");
	
	$schema=explode(",",$schema);
	
	if(isset($_REQUEST["forsite"])) {
		$cfgFile=findAppCfgFile($cfg);
		if(strlen($cfgFile)>0) {
			loadCfgFile($cfgFile,$schema);
		} else {
			echo "<style>body {overflow:hidden;}</style>";
			dispErrMessage("<span style='color:green'>[$cfg]</span> Configuration May/Has Not Have Been Enabled For This Site.","Configuration Not Supported",405);
		}
	} else {
		$cfgGroups=array();
		if(file_exists(APPROOT."config/cfgGroups.php")) include APPROOT."config/cfgGroups.php";
		if(array_key_exists($cfg,$cfgGroups)) {
			if(!in_array("system",$schema)) array_push($schema,"system");
			$cfgFile=findCfgFile($cfg);
			if(strlen($cfgFile)>0) {
				loadCfgFile($cfgFile,$schema,$cfgGroups[$cfg]);
			} else {
				$cfgFile=findCfgFile("system");
				if(strlen($cfgFile)>0) {
					loadCfgFile($cfgFile,$schema,$cfgGroups[$cfg]);
				} else {
					echo "<style>body {overflow:hidden;}</style>";
					dispErrMessage("Config Request Not Found","404:Not Found",404);
				}
			}
		} else {		
			$cfgFile=findCfgFile($cfg);
			if(strlen($cfgFile)>0) {
				loadCfgFile($cfgFile,$schema,null);
			} else {
				echo "<style>body {overflow:hidden;}</style>";
				dispErrMessage("Config Request Not Found","404:Not Found",404);
			}
		}
	}
} else {
	loadModuleLib("cfgeditor","manager");
}
?>
