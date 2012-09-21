<?php
if (!defined('ROOT')) exit('No direct script access allowed');
if (!defined('APPROOT')) exit('No direct script access allowed');

session_check();

if(defined("APPS_CSS_TYPE")) $css->TypeOfDispatch(APPS_CSS_TYPE);
if(defined("APPS_JS_TYPE")) $js->TypeOfDispatch(APPS_JS_TYPE);

if(getConfig("MOBILITY_PAGE_THEME")==null) LoadConfigFile(ROOT."config/mobility.cfg");

$theme_page=getConfig("MOBILITY_PAGE_THEME");
$theme_header=getConfig("MOBILITY_HEADER_THEME");
$theme_footer=getConfig("MOBILITY_HEADER_THEME");
$theme_button=getConfig("MOBILITY_BUTTON_THEME");
$device=getUserDeviceType();

loadModule("mobility");

if(!isset($_REQUEST["page"])) {
	$_REQUEST["page"]="mhome";
}

_js(array("mobile.jquery-1.4.4.min","mobile.jquery.mobile-1.0a2.min","ajax","dialogs"));
_css(array("jquery.mobile-1.0a2"));//,"mobile","handheld"

$page=$_REQUEST["page"];
$f=APPROOT.APPS_PAGES_FOLDER."$page.php";
if(file_exists($f)) {
	printUserPageStyle();
	echo "</head>\n<body style='width:100%;height:100%;padding:0px;margin:0px;' ".getBodyContext().">\n";
	include $f;
	echo "</body>";
} else {
	dispErrMessage("<i>$page</i> Page Not Found.","Page Not Found",404,'media/images/notfound/file.png');
}
?>
<script language=javascript>
$(document).bind("mobileinit", function() {
    // Make your jQuery Mobile framework configuration changes here!
    $.mobile.allowCrossDomainPages = true;
});
</script>
