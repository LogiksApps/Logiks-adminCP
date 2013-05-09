<?php
if (!defined('ROOT')) exit('No direct script access allowed');

loadModule("page");
$params=array();
$params["toolbar"]=array(
		array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load TrashBox","onclick"=>"reloadList()"),
		array("title"=>"Clear","icon"=>"clearicon","tips"=>"Clear Selected Trash/Cache/Logs","onclick"=>"clearTrash()"),
		array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()"),
	);
$params["contentarea"]="printContent";

printPageContent("apppage",$params);
?>
<style>
.datatable td {
	border-right:0px;
}
</style>
<script language=javascript>
cmdLnk=getServiceCMD("trashbox");
$(function() {
	$("#checkAll").change(function() {
			b=this.checked;
			$("#datatable input[name=selectTrash]").each(function() {
					this.checked=b;
				});
		});
	reloadList();
});
function reloadList() {
	$("#datatable tbody").html("<tr><td colspan=10><div class=ajaxloading6></div></td></tr>");
	lnk=cmdLnk+"&action=list";
	$("#datatable tbody").load(lnk,function() {
			$("#datatable input[type=checkbox]").change(function() {
					if($(this).is(":checked")) {
						$(this).parents("tr").addClass("active");
					} else {
						$(this).parents("tr").removeClass("active");
					}
				});
		});
}
function clearTrash() {
	lnk=cmdLnk+"&action=clear&boxes=";
	q="";
	$("#datatable input[type=checkbox]:checked").each(function(){
			q+=$(this).attr("rel")+",";
		});
	if(q.trim().length>0) {
		$("#datatable tbody").html("<tr><td colspan=10><div class=ajaxloading6></div></td></tr>");
		processAJAXQuery(lnk+q,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				reloadList();
			});
	}
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Help !",null,true,"700","300");
}
</script>
<?php function printContent() { ?>
	<table id=datatable class='datatable' width=99% cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:2px solid #aaa;'>
		<thead>
			<tr align=center class='ui-widget-header'>
				<th width=50px><input type=checkbox id=checkAll /></th>
				<th>Box Name</th>
				<th width=150px>Size</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<?php }?>
