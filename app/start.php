<?php
if(!defined('ROOT')) exit('No direct script access allowed');
if(!defined('APPROOT')) exit('No direct script access allowed');

$a=session_check(true);

if(defined("APPS_CSS_TYPE")) $css->TypeOfDispatch(APPS_CSS_TYPE);
if(defined("APPS_JS_TYPE")) $js->TypeOfDispatch(APPS_JS_TYPE);

if(!isset($_REQUEST["page"]) || strlen($_REQUEST["page"])==0) {
	$_REQUEST["page"]=getConfig("LANDING_PAGE");
}

_js(array("jquery","jquery.ui"));
_css(array("ajax","colors","style"));//icons

$css->loadCSS("ie6","*","ie6");
$css->loadCSS("print","*","","print");
$js->display();
$css->display();
echo "\n";

$page=$_REQUEST["page"];

$arrPages=array();
array_push($arrPages,APPROOT.APPS_PAGES_FOLDER."$page");
if(ALLOW_DEFAULT_SYSTEM_PAGES=="true") {
	array_push($arrPages,ROOT.PAGES_FOLDER."$page");
}

$loaded=false;
if(isLayoutConfig($page)) {
	$loaded=true;
	echo "<style>html,body {width:100%;height:100%;padding:0px;margin:0px;}".getUserPageStyle(false)."</style>\n";
	echo "</head>\n<body style='width:100%;height:100%;padding:0px;margin:0px;' ".getBodyContext().">\n";
	generatePageLayout($page);
	echo "</body>";
} else {
	$found=false;
	foreach($arrPages as $a) {
		if(!$loaded) {
			$arr1=getSupportedPages($a);
			foreach($arr1 as $f) {
				if(file_exists($f)) {
					$loaded=true;
					echo "<style>html,body {width:100%;height:100%;padding:0px;margin:0px;}".getUserPageStyle(false)."</style>\n";
					echo "</head>\n<body style='width:100%;height:100%;padding:0px;margin:0px;' ".getBodyContext().">\n";
					include $f;	
					echo "</body>";
					break;
				}
			}
		}
	}
}
if(!$loaded) {
	dispErrMessage("<i>$page</i> Page Not Found.","Page Not Found",404,'media/images/notfound/file.png');
}
_css("print","*","","print");
_js(array("ajax","commons","dialogs"));
printSubSkin();
exit();
?>
