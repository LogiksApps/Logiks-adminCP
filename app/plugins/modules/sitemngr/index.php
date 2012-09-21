<?php
if (!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

_js(array("jquery.editinplace"));
_css(array("jquery.editinplace"));

loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Site List","onclick"=>"loadSiteList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Site","onclick"=>"createAppSite()");
$btns[sizeOf($btns)]=array("title"=>"Clone","icon"=>"cloneicon","tips"=>"Clone Selected Site","onclick"=>"cloneAppSite()");

$btns[sizeOf($btns)]=array("title"=>"Export","icon"=>"exporticon","tips"=>"Export Selected Site And Download As AppImage.","onclick"=>"exportAppSite()");
//$btns[sizeOf($btns)]=array("title"=>"Import","icon"=>"importicon","tips"=>"Import AppImage","onclick"=>"importAppSite()");

$btns[sizeOf($btns)]=array("bar"=>"|");
$btns[sizeOf($btns)]=array("title"=>"AppImages","icon"=>"appimageicon","tips"=>"Manage AppImages","onclick"=>"manageAppImages()");

$btns[sizeOf($btns)]=array("bar"=>"|");
$btns[sizeOf($btns)]=array("title"=>"Flush","icon"=>"clearicon","tips"=>"Flush Privilege Cache","onclick"=>"flushPrivileges()");
$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Site","onclick"=>"deleteAppSite()");
$btns[sizeOf($btns)]=array("bar"=>"|");
$btns[sizeOf($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
	$webPath=getWebPath(__FILE__);
	$rootPath=getRootPath(__FILE__);
?>
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>
<link href="<?=$webPath?>style.css"  rel="stylesheet" type='text/css' media='screen'/>
<table id=sitetable class='datatable1' width=99% cellpadding=0 cellspacing=0 border=1 style='margin:auto;margin:5px;border:2px solid #aaa;'>
	<thead>
		<tr align=center class='ui-widget-header'>
			<th width=30px>--</th>
			<th width=40px>Sl.</th>
			<th>Name</th>
			<th width=110px>Alias</th>
			<th>Company</th>
			<th width=100px>Type</th>
			<th width=50px>Mobile</th>
			<th width=50px>Tablet</th>
			<th>DB</th>
			<th width=90px>DBMS</th>
			<th width=80px>MODE</th>
			<th width=70px>Access</th>
			<th width=140px>Created</th>
			<th width=100px>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div style='display:none'>
	<div id=createMode class="ui-widget-content noshow" title="New Site Configurations">
		<?php 
			include "dlgs/createapp.php"; 
		?>
	</div>
	<div id=cloneMode class="ui-widget-content noshow" title="Clone Site's Configurations">
		<?php 
			include "dlgs/cloneapp.php"; 
		?>
	</div>
	<div id=exportMode class="ui-widget-content noshow" title="Export Existing Site">
		<?php 
			include "dlgs/exportapp.php"; 
		?>
	</div>
	<div id=appimageManager class="ui-widget-content noshow" title="AppImage Manager">
		<?php 
			include "dlgs/manager.php";
		?>
	</div>
</div>
<script>
cmdLnk="services/?scmd=sitemngr&site=<?=SITENAME?>";
</script>
<?php } ?>
