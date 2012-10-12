<?php
if (!defined('ROOT')) exit('No direct script access allowed');

loadModule("page");

$params=array();

$params["toolbar"]="printBar";
$params["contentarea"]="printContent";

$webPath=getWebPath(__FILE__);

file_put_contents(ROOT.BACKUP_FOLDER.".htaccess","deny from all\n");
chmod(ROOT.BACKUP_FOLDER.".htaccess",0777);

printPageContent("apppage",$params);
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<script language=javascript>
cmdLnk="services/?scmd=sitebackup&site=<?=SITENAME?>";
$(function() {
	$("select#siteselector").load("services/?scmd=qtools&site=<?=SITENAME?>&action=sitelist&format=select");
	reloadList();
});
function reloadList() {
	$("#loadingmsg").show();
	$("#datatable tbody").html("<tr><td colspan=10><div class=ajaxloading6></div></td></tr>");
	lnk=cmdLnk+"&action=list";
	$("#datatable tbody").load(lnk,function() {
			$("#datatable .backup input[type=checkbox]").click(function() {
					//alert($(this).attr("rel"));
				});
			$("#datatable .backup .buttondiv").click(function() {
					site=$(this).parents("tr.backup").attr("site");
					ref=$(this).attr("rel");
					if($(this).hasClass("downloadicon")) {
						q="services/?scmd=sitebackup&action=download&ref="+ref;
						window.open(q);
					} else if($(this).hasClass("restoreicon")) {
						$("#loadingmsg").show();
						q="services/?scmd=sitebackup&action=restore&ref="+ref+"&forsite="+site;
						processAJAXQuery(q,function(txt) {
								if(txt.length>0) lgksAlert(txt);
								$("#loadingmsg").hide();
							});
					} else  if($(this).hasClass("deleteicon")) {
						q="services/?scmd=sitebackup&action=delete&ref="+ref;
						$("#loadingmsg").show();
						processAJAXQuery(q,function(txt) {
								if(txt.length>0) lgksAlert(txt);
								reloadList();
							});
					}
				});
			$("#loadingmsg").hide();
		});
	//$("#siteselector").load("services/?scmd=qtools&action=applist");
}
function createBackup(site) {
	if(site.length>0) {
		q=cmdLnk+"&action=backup&forsite="+site;
		$("#loadingmsg").show();
		processAJAXQuery(q,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				reloadList();
			});
	}
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Help !",null,true,"700","300");
}
</script>
<?php function printBar() { ?>
<button onclick="reloadList()" title="ReLoad Backup Table" ><div class="reloadicon">Reload</div></button>
|
<select id=siteselector class='ui-state-active ui-corner-all' style='width:200px;height:28px;'></select>
<button onclick="createBackup($('select#siteselector').val());" title="Create Backup" ><div class="openicon">Backup</div></button>
<button onclick="showHelp();" title="Help Contents" ><div class="helpicon">Help</div></button>
<!--
<button onclick="clearBackup($('#siteselector').val());" title="Clear Backup Cache" ><div class="clearicon">Clear</div></button>-->
<?php } ?>
<?php function printContent() { ?>
<table id=datatable width=99% cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:1px solid #aaa;'>
		<thead>
			<tr align=center class='ui-widget-header'>
				<th width=40px>*</th>
				<th>Backup Date</th>
				<th width=100px>Backup Time</th>
				<th width=150px>Backup Size</th>
				<th width=75px>Download</th>
				<th width=75px>Restore</th>
				<th width=75px>Delete</th>
				<th width=100px>Site</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<?php } ?>
