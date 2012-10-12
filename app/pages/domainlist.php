<?php
if (!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);

loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Site List","onclick"=>"loadDomainList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Site","onclick"=>"addMap()");
$btns[sizeOf($btns)]=array("title"=>"Edit","icon"=>"editicon","tips"=>"Edit Site Properties","onclick"=>"editMap()");
$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Site","onclick"=>"deleteMap()");
$btns[sizeOf($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
	$sid=md5("dmlist_".rand(1000,999999)._timestamp());

	$_SESSION[$sid]=array();

	$_SESSION[$sid]["table"]=_dbtable("aliaspath",true);
	$_SESSION[$sid]["cols"]="id,host,alias,appsite,active,doc";
	$_SESSION[$sid]["where"]="";
?>
<style>
input {
	border:1px solid #777;
	width:100%;
}
select {
	width:100%;
	height:22px;
}
td.serial_col {
	text-align:center;
	font-weight:bold;
}
#createMode table,#createMode table td {
	border:0px;
}
table#domaintable td {
	border-right:0px;
}
#createMode table,#createMode table td {
	border:0px;
}
</style>
<div id=createMode class="dialog ui-widget-content ui-corner-all " style="width:400px;height:200px;float:right;margin:10px;padding:5px;display:none;" title="Domain Map">
	<table width=100% cellpadding=3 cellspacing=0 border=0 class='nostyle input'>
		<input id=cm_id type=hidden value=0 />
		<input id=edit_mode type=hidden value='new' />
		<tr>
			<td width=100px>Website Host</td><td><input id=cm_host type=text /> </td>
		</tr>
		<tr>
			<td width=100px>Alias</td><td><input id=cm_alias type=text /> </td>
		</tr>
		<tr>
			<td width=100px>AppSite</td>
			<td>
				<select id=applist style='width:95%;'>
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
<table id=domaintable class='datatable' cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:1px solid #aaa;width:600px;'>
	<thead>
		<tr class='ui-widget-header'>
			<th width=40px>--</th>
			<th width=55px align=center>ID</th>
			<th width=155px>Host</th>
			<th width=155px>Alias</th>
			<th width=155px>AppSite</th>
			<th width=75px align=center>Active</th>
			<th width=100px align=center>Created</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div style='display:none'>
	<div id='helpInfo' class='helpInfo' title='Help !' style='width:100%;text-align:justify;font-size:15px;font-family:verdana;'>
		<b>DomainMap</b>, helps you manage the static routing of requests from a single domain to a single appSite.
		An appSite is the single application that has been installed/developed/deployed on this <b>Logiks3</b> installation.
		<b>DomainMap</b> helps you to lock a particular domain (eg <?=$_SERVER['HTTP_HOST']?>) on to a particular appSite. 
		You can create new Domain Locked Maps, delete or change the apps locked on by the domains, or even switch between
		active or non-active modes for each Map.
	</div>
</div>
<script language=javascript>
lnk="services/?scmd=datagrid&site=<?=SITENAME?>&action=load&datatype=html&sqlsrc=session&sqlid=<?=$sid?>";
$(function() {
	$("#domaintable tbody").delegate("input.activecheckbox","click",function() {
			a="";
			if($(this).is(":checked")) a="true"; else a="false";
			id=$(this).attr("rel");
			l="services/?scmd=formaction&action=submit&frmMode=update";
			q="submit_table=<?=_dbtable("aliaspath",true)?>&submit_wherecol=id";
			q+="&id="+id+"&active="+a;
			processAJAXPostQuery(l,q,function(txt) {
					if(txt.length>0) {
						loadDomainList();
						lgksAlert(txt);
					}
				});
		});
	$("#domaintable tbody").delegate("input[name=rowselector][type=radio]","click",function() {
			selectRow(this);
		});
	$("#domaintable tbody").delegate("tr","click",function() {
			r=$(this).find("input[name=rowselector][type=radio]");
			if(r.length>0) {
				r=r.get(0);
				r.checked=true;
				selectRow(r);
			}
		});
	loadDomainList();
})
function loadDomainList() {
	$("#domaintable tbody").html("<tr><td colspan=10 class=ajaxloading></td></tr>");
	$("#domaintable tbody").load(lnk,function() {
			$("#domaintable tbody tr").each(function() {
					if($(this).children().length>1) {
						$(this).prepend("<td align=center><input name=rowselector type=radio /></td>");
						
						z=$(this).find("td[col=active]");
						id=$(this).find("td[col=id]").text();
						z.attr("rel",z.text());
						if(z.text()=="true") {
							z.html("<input class='activecheckbox' type=checkbox checked rel='"+id+"' />");
						} else {
							z.html("<input class='activecheckbox' type=checkbox rel='"+id+"' />");
						}
					}
				});
		});
}
function addMap() {
	$("#createMode input#cm_host").val("");
	$("#createMode input#cm_alias").val("");
	$("#createMode input#cm_id").val("0");
	$("#createMode input#edit_mode").val("new");
	$("#createMode").show();
}
function editMap() {
	$("#domaintable tr.active").each(function() {
			$("#createMode input#cm_host").val($(this).find("td[col=host]").text());
			$("#createMode input#cm_alias").val($(this).find("td[col=alias]").text());
			$("#createMode input#cm_id").val($(this).find("td[col=id]").text());
			$("#createMode select").val($(this).find("td[col=appsite]").text());
			$("#createMode input#edit_mode").val("edit");
			$("#createMode").show();
		});
}
function deleteMap() {
	$("#domaintable tr.active").each(function() {
			host=$(this).find("td[col=host]").text();
			app=$(this).find("td[col=appsite]").text();
			id=$(this).find("td.serial_col").text();
			
			lgksConfirm("Are You Sure About Deleting Domain Mapping For <br/><br/><div align=center><b>"+host+"=>"+app+"</b></div>","Delete Domain Map !",function() {
					s="services/?scmd=formaction&action=delete";
					q="&submit_table=<?=_dbtable("aliaspath",true)?>&delete_id="+id;
					processAJAXPostQuery(s,q,function(txt) {
							if(txt.length>0) {
								lgksAlert(txt);
							}
							loadDomainList();
						});
				});
		});
}
function saveForm() {
	if($("#createMode input#edit_mode").val()=="new") {
		createDomainMap();
		$("#createMode").hide();
	} else if($("#createMode input#edit_mode").val()=="edit") {
		saveDomainMap();
		$("#createMode").hide();
	} else {
		lgksAlert("Something Went Wrong. Try Again.");
	}
}
function createDomainMap() {
	id=$("#createMode input#cm_id").val();
	host=$("#createMode input#cm_host").val();
	alias=$("#createMode input#cm_alias").val();
	app=$("#createMode #applist").val();
	
	if(host.length>0 && alias.length>0) {
		s="services/?scmd=formaction&action=submit&frmMode=insert";
		q="&id="+id+"&host="+host+"&alias="+alias+"&appsite="+app+"&active=true&submit_table=<?=_dbtable("aliaspath",true)?>";
		processAJAXPostQuery(s,q,function(txt) {
				loadDomainList();
				if(txt.length>0) lgksAlert(txt);
			});
	}
}
function saveDomainMap() {
	id=$("#createMode input#cm_id").val();
	host=$("#createMode input#cm_host").val();
	alias=$("#createMode input#cm_alias").val();
	app=$("#createMode #applist").val();
	
	if(host.length>0 && alias.length>0) {
		s="services/?scmd=formaction&action=submit&frmMode=update";
		q="&id="+id+"&host="+host+"&alias="+alias+"&appsite="+app+"&active=true&submit_wherecol=id&submit_table=<?=_dbtable("aliaspath",true)?>";
		processAJAXPostQuery(s,q,function(txt) {
				loadDomainList();
				if(txt.length>0) lgksAlert(txt);
			});
	}
}
function selectRow(e) {
	$(e).parents("tbody").find("tr.active").removeClass("active");
	if($(e).is(':checked')) {
		$(e).parents("tr").addClass("active");
	}
}
function showHelp() {
	jqPopupDiv("#helpInfo",null,true,"700","250");
}
</script>
<?php } ?>
