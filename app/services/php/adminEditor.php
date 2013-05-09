<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);
if($_SESSION['SESS_PRIVILEGE_ID']!=1) {
	exit("User Privilege Level Not Authenticated For Use Of This Service");
}
if(isset($_REQUEST["action"])) {
	$baseDir=ROOT;
	
	if($_REQUEST["action"]=="fetch" && isset($_REQUEST["file"])) {
		$file=$baseDir.$_REQUEST["file"];
		if(strlen($file)>0 && file_exists($file) && is_file($file) && is_readable($file)) {
			$data="";
			$data=file_get_contents($file);
			echo $data;
		} else {
			echo "FileNotFound :: Requested Source File Not Found";
		}
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST["file"]) && isset($_POST['data'])) {
		$file=$baseDir.$_REQUEST["file"];
		if(strlen($file)>0 && file_exists($file) && is_file($file)) {
			if(is_writable($file)) {
				$data=$_POST['data'];
				$data=cleanCode($data);
				$a=file_put_contents($file,$data);
				if($a==strlen($data)) {
					echo "Successfully Saved";
				} else {
					echo "Source Page Data Could Not Be Saved. Try Again.";
				}
			} else {
				echo "Source File Is ReadOnly.";
			}
		} else {
			echo "FileNotFound :: Requested Source File Not Found";
		}
		exit();
	}
}
?>
