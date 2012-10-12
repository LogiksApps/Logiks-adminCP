<?php
if (!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check(true);

loadModule("page");

$params=array();

$params["toolbar"]="printBar";
$params["contentarea"]="printContent";

$webPath=getWebPath(__FILE__);

printPageContent("apppage",$params);
?>
<?php function printBar() { ?>
<button onclick="reloadList()" title="Load DBMS List" ><div class="reloadicon">Reload</div></button>
||
<button onclick="editDatabase()" title="Edit Database" ><div class="editicon">Edit</div></button>
<button onclick="dbmsManager()" title="Open DBMS Admin" ><div class="dbicon">Admin</div></button>
||
<button onclick="showHelp()" title="Help Contents" ><div class="helpicon">Help</div></button>
<?php } ?>
<?php function printContent() { ?>
<style>
#page .toolbar>.left {
	padding-top:0px;
}
</style>
<table id=datatable class='datatable' width=99% cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:1px solid #aaa;'>
	<thead>
		<tr align=center class='ui-widget-header'>
			<th width=40px>*</th>
			<th width=100px>App</th>
			<th width=140px>DBMS</th>
			<th>Host</th>
			<th>Database</th>
			<th>User</th>
			<th width=70px>Read Only</th>
			<th width=100px>INFO</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<script language=javascript>
cmdLnk="services/?scmd=dbmanager&site=<?=SITENAME?>";
$(function() {
	$("#datatable tbody").delegate("tr","click",function() {
			$(this).find("input[name=rowselect]").get(0).checked=true;
		});
	$("#datatable tbody").delegate(".readonly","change",function() {
			a=$(this).is(":checked");
			b=$(this).attr("rel");
			url=cmdLnk+"&action=readonly&s="+b+"&v="+a;
			processAJAXQuery(url,function(data) {
					if(data.length>0) lgksAlert(data);
				});
		});
	$("#datatable tbody").delegate(".tablelist","click",function() {
			b=$(this).parents("tr").attr("rel");
			url=cmdLnk+"&action=getinfo&s="+b;
			processAJAXQuery(url,function(data) {
					lgksAlert(data,"Table List");
				});
		});
	$("#datatable tbody").delegate(".schema","click",function() {
			b=$(this).parents("tr").attr("rel");
			url=cmdLnk+"&action=getschema&s="+b;
			window.open(url);
		});
	reloadList();
});
function reloadList() {
	url=cmdLnk+"&action=dbmstable";
	$("#datatable tbody").html("<tr><td colspan=20 class='ajaxloading6'></td></tr>");
	$("#datatable tbody").load(url,function() {			
		});
}
function editDatabase() {
	a=$("#datatable tbody").find("input[name=rowselect]:checked");
	if(a.length>0) {
		app=a.parents("tr").attr("rel");
		lnk="<?=SiteLocation?>?site=<?=SITENAME?>&page=configeditor&cfg=db&forsite="+app;
		parent.openInNewTab("DB : "+app, lnk);
	}
}
function dbmsManager() {
	a=$("#datatable tbody").find("input[name=rowselect]:checked");
	if(a.length>0) {
		app=a.parents("tr").attr("rel");
		lnk="<?=SiteLocation?>?site=<?=SITENAME?>&page=dbadmin&forsite="+app;
		parent.openInNewTab("dbAdmin : "+app, lnk);
	}
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Help !",null,true,"700","300");
}
</script>
<?php } ?>
