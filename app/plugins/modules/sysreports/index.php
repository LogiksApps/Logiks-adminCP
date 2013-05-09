<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(isset($_REQUEST['report'])) {
	$p=dirname(__FILE__)."/reports/".$_REQUEST['report'].".php";
	if(file_exists($p)) {
		include $p;
	} else {
		echo "<style>body {overflow:hidden;}</style>";
		dispErrMessage("Requested Log Report Not Found.","404:Log-Report Not Found",404);
	}
} else {
	echo "<style>body {overflow:hidden;}</style>";
	dispErrMessage("Requested Log Report Not Found.","404:Log-Report Not Found",404);
}
?>
