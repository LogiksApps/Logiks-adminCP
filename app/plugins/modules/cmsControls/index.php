<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js(array("jquery.multiselect"));
_css(array("jquery.multiselect"));

loadModule("page");

$layout="apppage";
$params=array("toolbar"=>null,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
?>
<style>
#cmsCtrlPage .ui-tabs-panel.ui-widget-content {
	border:0px !important;
	padding:2px;
	width:99%;height:94%;
}
#cmsCtrlPage .datatable td.blocked,
#cmsCtrlPage .datatable td.onmenu,
#cmsCtrlPage .datatable td.privilege {
	text-align:center;
	background-position:center center;
}
button.ui-multiselect {
	width:500px !important;
}
</style>
<div id=cmsCtrlPage class=tabs style='width:100%;height:100%;'>
	<ul>
		<li><a href='#ctrlList' onclick="loadControlList()">Controls</a></li>
		<li><a href='#b'>Configurations</a></li>
	</ul>
	<div id=ctrlList style='overflow-y:auto;overflow-x:hidden;'>
		<table class='datatable' width=50% cellpadding=2 cellspacing=0 border=0 style='margin-left:5px;width:60%;'>
			<thead>
				<tr class='ui-widget-header'>
					<!--<th>Group</th>-->
					<th width=25px></th>
					<th>Name</th>
					<th>Category</th>
					<!--<th>Link</th>-->
					<th width=30px>Privilege</th>
					<th width=40px>Enabled</th>
					<!--<th width=40px>Visible</th>-->
				</tr>
			</thead>
			<tbody>
			
			</tbody>
		</table>
	</div>
	<div id=b>
		<iframe src='<?=_link("configeditor")?>&forsite=cms&popup=true' frameborder=0 style='width:100%;height:100%;margin:0px;padding:0px;'>
		</iframe>
	</div>
</div>
<div style='display:none'>
	<div id=privilegeDialog title='Set Control Privilege' align=center>
		<select style='width:100%;' multiple>
			
		</select>
	</div>
</div>
<script language=javascript>
$(function() {
	$("#privilegeDialog select").multiselect({
				
			});
	$("#ctrlList .datatable tbody").delegate("input[type=checkbox]","change",function() {
			rel=$(this).parents("tr").attr("rel");
			v=!this.checked;
			nm=$(this).attr("name");
			l=getServiceCMD("cmscontrols")+"&action=toggle&type="+nm+"&rel="+rel+"&v="+v;
			processAJAXQuery(l,function(txt) {
					if(txt.length>0) {
						loadControlList();
						lgksAlert(txt);
					}
				});
		});
	$("#ctrlList .datatable tbody").delegate(".minibutton","click",function() {
			rel=$(this).parents("tr").attr("rel");
			v=$(this).attr("val");
			ele=$(this);
			if($(this).hasClass("privilege")) {
				l=getServiceCMD("qtools")+"&action=privilegelist&format=select&forsite=cms";
				$("#privilegeDialog select").html("<option value='##'>Loading ...</option>");
				$("#privilegeDialog select").load(l,function() {
						$("#privilegeDialog select").prepend("<option value='*'>All Users With CMS Login</option>");
						$("#privilegeDialog select option[value=1]").detach();
						$("#privilegeDialog select option[value=2]").detach();
						v=v.split(",");
						$.each(v,function(a,b) {
								$("#privilegeDialog select option[value='"+b+"']").attr("selected",true);
							});
						$("#privilegeDialog select").multiselect("refresh");
					});
				
				//alert(rel+v);
				jqPopupDiv("#privilegeDialog").dialog({
						resizable:false,
						width:550,
						height:200,
						buttons:{
							Select:function() {
								val=$("#privilegeDialog select").val();
								if(val.indexOf('*')==0) val="*";
								//lgksAlert(val);
								l=getServiceCMD("cmscontrols")+"&action=privilege&v="+val+"&rel="+rel;
								processAJAXQuery(l,function(txt) {
										if(txt.length>0) {
											loadControlList();
											lgksAlert(txt);
										} else {
											ele.attr('val',val);
										}
									});
								$(this).dialog("close");
							},
							Cancel:function() {
								$(this).dialog("close");
							}
						}
					});
			}
		});
	loadControlList();
});
function loadControlList() {
	l=getServiceCMD("cmsControls")+"&action=ctrllist";
	$("#ctrlList .datatable tbody").html("<tr><td colspan=20 class='ajaxloading'></td></tr>");
	$("#ctrlList .datatable tbody").load(l);
}
</script>
<?php
}
?>
