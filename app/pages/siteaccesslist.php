<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$sid=md5("salist_".rand(1000,999999)._timestamp());

$_SESSION[$sid]=array();

$_SESSION[$sid]["table"]=_dbtable("access",true);
$_SESSION[$sid]["cols"]="id,master,sites,blocked,doc,doe";
$_SESSION[$sid]["where"]="";

_js(array("jquery.multiselect"));
_css(array("jquery.multiselect"));

loadModule("page");
$params=array();
$params["toolbar"]=array(
		array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Reload Accesspoints","onclick"=>"loadAccessPointList()"),
		array("title"=>"Create","icon"=>"addicon","tips"=>"New AccessPoint","onclick"=>"addMap()"),
		array("title"=>"Edit","icon"=>"editicon","tips"=>"Edit AccessPoint","onclick"=>"editMap()"),
		array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete AccessPoint","onclick"=>"deleteMap()"),
	);
$params["contentarea"]="printContent";

printPageContent("apppage",$params);
?>
<?php function printContent() { ?>
<style>
td.serial_col {
	text-align:center;
	font-weight:bold;
}
#createMode table,#createMode table td {
	border:0px;
}
table#accesstable td {
	border-right:0px;
}
#accesstable td {
	padding-left:2px;
}
input[type=text] {
	border:1px solid #777;
	width:100%;
}
select {
	width:300px;
	height:22px;
}
#createMode table,#createMode table td {
	border:0px;
}
</style>
<div class='ui-widget-content ui-corner-all' style='width:80%;padding:4px;font-size:14px;color:maroon;text-align:justify;margin:auto;margin-top:5px;'>
		Access Points defines the sites (multiple), which a user is allowed to access/switch after login. 
		If multiple sites are selected, he will be allowed to access all of them with out relogin, simply 
		by switching.<br/>
		For Login, every user must be assigned atleast one Access Point which should have atleast one AppSite accessible.
</div>
<div id=createMode class="dialog ui-widget-content ui-corner-all" style="width:400px;height:170px;float:right;margin:10px;padding:5px;display:none;" title='Create Access Point'>
	<table width=100% cellpadding=3 cellspacing=0 border=0 class='nostyle input'>
		<input id=cm_id type=hidden value=0 />
		<input id=edit_mode type=hidden value='new' />
		<input id=blocked name=blocked type=hidden value='false' />
		<tr>
			<td width=100px>Access Point Name</td><td><input id=cm_accesspoint type=text /> </td>
		</tr>
		<tr>
			<td width=100px>AppSite</td>
			<td>
				<select id=applist size=2 multiple=true>
					<option value='*'>Each And Every Site</option>
					<?php
						$p=ROOT.APPS_FOLDER;
						$f=scandir($p);
						unset($f[0]);unset($f[1]);
						foreach($f as $a) {
							if(file_exists($p.$a."/apps.cfg")) {
								echo "<option value='$a'>$a</option>";
							}
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=10>&nbsp;</td>
		</tr>
		<tr>
			<td colspan=10><hr/></td>
		</tr>
		<tr>
			<td colspan=10 align=center>
				<button onclick="$('#createMode').hide();">Close</button>
				<button onclick="saveForm()">Save</button>
			</td>
		</tr>
	</table>
</div>
<table id=accesstable class=datatable width=670px cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:2px solid #aaa;width:670px;'>
	<thead>
		<tr align=center class='ui-widget-header'>
			<th width=40px>--</th>
			<th width=55px align=center>ID</th>
			<th>Access Point</th>
			<th>AppSite</th>
			<th width=75px align=center>Blocked</th>
			<th width=100px align=center>Created</th>
			<th width=100px align=center>Last Edit</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div style='display:none'>
<div id=createMode class="ui-widget-content noshow" title="Access Point">
	<table width=100% cellpadding=0 cellspacing=0 border=0>
		
	</table>
</div>
</div>
<?php } ?>

<script language=javascript>
lnk="services/?scmd=datagrid&site=<?=SITENAME?>&action=load&datatype=html&sqlsrc=session&sqlid=<?=$sid?>";
$(function() {
	$("button").button();
	$("#applist").multiselect();
	
	$("#accesstable tbody").delegate("tr","click",function() {
			r=$(this).find("input[name=rowselector][type=radio]");
			if(r.length>0) {
				r=r.get(0);
				r.checked=true;
				selectRow(r);
			}
		});
	$("#accesstable tbody").delegate("input[name=rowselector][type=radio]","click",function() {
			selectRow(this);
		});
	$("#accesstable tbody").delegate("input[name=blockAccesse][type=checkbox]","click",function() {
			id=$(this).parents("tr").find("td[col=id]").text();
			v="false";
			if($(this).is(":checked")) v="true";
			s="services/?scmd=formaction&action=updatebyid";
			q="&submit_table=<?=_dbtable("access",true)?>&update_id="+id+"&blocked="+v;
			processAJAXPostQuery(s,q,function(txt) {
						if(txt.length>0) {
							lgksAlert(txt);
							loadAccessPointList();
						}
					});
		});	
	loadAccessPointList();
})
function loadAccessPointList() {
	$("#accesstable tbody").html("<tr><td colspan=10 class=ajaxloading><br/><br/>Loading ...</td></tr>");
	$("#accesstable tbody").load(lnk,function() {
			$("#accesstable tbody tr").each(function() {
					$(this).find("td[col=blocked]").attr("align","center");
					if($(this).find("td[col=blocked]").text().trim()=="true") {
						$(this).find("td[col=blocked]").html("<input name=blockAccesse type=checkbox checked />");
					} else {
						$(this).find("td[col=blocked]").html("<input name=blockAccesse type=checkbox />");
					}					
					$(this).prepend("<td align=center><input name=rowselector type=radio /></td>");
				});
		});
}
function addMap() {
	$("#createMode input#blocked").val("false");
	$("#createMode input#cm_id").val("0");
	$("#createMode input#cm_accesspoint").val("");
	$("#createMode #applist").val("");
	$("#createMode #applist").multiselect("refresh");
	$("#createMode input#edit_mode").val("new");
	$("#createMode").show();
}
function editMap() {
	if($("#accesstable tr.active").length<=0) {
		lgksAlert("Select One Access Point To Edit.");
		return;
	}
	$("#accesstable tr.active").each(function() {
			$("#createMode input#cm_id").val($(this).find("td[col=id]").text());
			$("#createMode input#cm_accesspoint").val($(this).find("td[col=master]").text());
			$("#createMode #blocked").val($(this).find("td[col=blocked]").text().trim());
			
			$("#createMode #applist").val($(this).find("td[col=sites]").text().split(","));
			$("#createMode #applist").multiselect("refresh");
			$("#createMode input#edit_mode").val("edit");
			$("#createMode").show();
		});
}
function deleteMap() {
	lgksConfirm("Do you want to delete the selected Access Points ?","Delete Access Points !",function() {
			$("#accesstable tr.active td.serial_col").each(function() {
				s="services/?scmd=formaction&action=delete";
				q="&submit_table=<?=_dbtable("access",true)?>&delete_id="+$(this).text();
				processAJAXPostQuery(s,q,function(txt) {
							loadAccessPointList();
							if(txt.length>0) lgksAlert(txt);
						});
			});
		});	
}
function saveForm() {
	if($("#createMode input#edit_mode").val()=="new") {
		createAccessMap();
		$("#createMode").hide();
	} else if($("#createMode input#edit_mode").val()=="edit") {
		saveAccessMap();
		$("#createMode").hide();
	} else {
		lgksAlert("Something Went Wrong. Try Again.");
	}
}
function createAccessMap() {
	id=$("#createMode input#cm_id").val();
	accesspoint=$("#createMode input#cm_accesspoint").val();
	apps=$("#createMode #applist").val();
	blocked=$("#createMode #blocked").val();
	
	if(accesspoint.length>0) {
		s="services/?scmd=formaction&action=submit&frmMode=insert";
		q="&id="+id+"&master="+accesspoint+"&sites="+apps+"&blocked="+blocked+"&submit_table=<?=_dbtable("access",true)?>";
		
		processAJAXPostQuery(s,q,function(txt) {
				loadAccessPointList();
			});
		$(this).dialog('close');
	} else {
		lgksAlert("AccessPoint Name Is Must.");
	}
}
function saveAccessMap() {
	id=$("#createMode input#cm_id").val();
	accesspoint=$("#createMode input#cm_accesspoint").val();
	apps=$("#createMode #applist").val();
	blocked=$("#createMode #blocked").val();
	
	if(accesspoint.length>0 && apps.length>0) {
		s="services/?scmd=formaction&action=submit&frmMode=update";
		q="&id="+id+"&master="+accesspoint+"&sites="+apps+"&blocked="+blocked+"&submit_wherecol=id&submit_table=<?=_dbtable("access",true)?>";
		processAJAXPostQuery(s,q,function(txt) {
				loadAccessPointList();
				if(txt.length>0) lgksAlert(txt);
			});
	} else {
		lgksAlert("AccessPoint Name And Selected Apps Are Must.");
	}
}
function selectRow(e) {
	$(e).parents("tbody").find("tr.active").removeClass("active");
	if($(e).is(':checked')) {
		$(e).parents("tr").addClass("active");
	}
}
</script>
