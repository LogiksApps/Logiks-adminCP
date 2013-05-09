<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);
if($_SESSION['SESS_PRIVILEGE_ID']!=1) {
	exit("User Privilege Level Not Authenticated For Use Of This Service");
}
loadModule("adminCodeEdit");
?>
