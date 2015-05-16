<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("page");

$btns=array();
$btns[count($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Site List","onclick"=>"loaddomainList()");
$btns[count($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Site","onclick"=>"createMap()");
$btns[count($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
?>
<style>
#domaintable {
	margin:5px;border:1px solid #aaa;
	width:600px !important;
	float: left;
}
#createMode {
	width: 400px;
	height: 130px;
	float: right;
	margin: 10px;
	padding: 15px;
	padding-top: 20px;

	display:none;
}
</style>
<div id="createMode" class="dialog ui-widget-content ui-corner-all" title="Domain Map">
	<table width=100% cellpadding=3 cellspacing=0 border=0 class='nostyle input noborder'>
		<input id="cm_id" type=hidden value=0 />
		<input id="edit_mode" type=hidden value='new' />
		<tr>
			<td width=100px>Website Host *</td><td><input id="cm_host" type=text /> </td>
		</tr>
		<tr>
			<td width=100px>AppSite *</td>
			<td>
				<select id="applist">
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
<table id="domaintable" class='datatable' cellpadding=0 cellspacing=0 border=1>
	<thead>
		<tr class='ui-widget-header'>
			<th width=155px>Host</th>
			<th width=155px>AppSite</th>
			<th width=75px align=center>Active</th>
			<th width=100px align=center>Created</th>
			<th width=100px align=center>Edited</th>
			<th width=150px></th>
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
$(function() {
	$("#domaintable tbody").delegate(".btnicon","click",function() {
			tr=$(this).parents("tr");
			if($(this).hasClass("editicon")) {
				editMap(tr);
			} else if($(this).hasClass("deleteicon")) {
				deleteMap(tr);
			}
		});
	$("#domaintable tbody").delegate("input[type=checkbox]","click",function() {
			if($(this).hasClass("active")) {
				a=this.checked;
				h=$(this).parents("tr").attr("rel");
				l=getServiceCMD("domainList")+"&action=setactive";
				q="h="+h+"&s="+a;
				$("#loadingmsg").show();
				processAJAXPostQuery(l,q,function(txt) {
						$("#loadingmsg").hide();
						if(txt.length>0) {
							lgksAlert(txt);
							loaddomainList();
						}
					});
			}
		});
	loaddomainList();
});
function createMap() {
	$("#createMode input#cm_host").val("");
	$("#createMode input#edit_mode").val("new");
	$("#createMode").show();
}
function editMap(tr) {
	$("#createMode input#cm_host").val(tr.find("td[col=host]").text());
	$("#createMode select").val(tr.find("td[col=appsite]").text());
	$("#createMode input#edit_mode").val("edit");
	$("#createMode").show();
}
function deleteMap(tr) {
	h=tr.find("td[col=host]").text();
	a=tr.find("td[col=appsite]").text();
	lgksConfirm("Are You Sure About Deleting Domain Mapping For <br/><br/><div align=center><b>"+h+"=>"+a+"</b></div><br/>You can deactivate it.","Delete Domain Map !",function() {
					l=getServiceCMD("domainList")+"&action=delete";
					q="h="+h;
					$("#loadingmsg").show();
					processAJAXPostQuery(l,q,function(txt) {
							$("#loadingmsg").hide();
							if(txt.length>0) {
								lgksAlert(txt);
							}
							loaddomainList();
						});
				});
}
function saveForm() {
	h=$("#createMode #cm_host").val();
	a=$("#createMode #applist").val();
	m=$("#createMode #edit_mode").val();
	if(m=="new" && $("#domaintable tr[rel='"+h+"']").length>0) {
		lgksAlert("Website Host Is Already Mapped, Please Edit the existing map or give new domain.");
		$("#createMode #cm_host").val("");
		return;
	}
	if(h.length<=0) {
		lgksAlert("Website Host Is Missing. Its a mandatory field.");
		return;
	}
	if(h.indexOf("http://")==0 || h.indexOf("https://")==0) {
		lgksAlert("<b>http://</b> is not allowed in Hostnames");
		return;
	}
	l=getServiceCMD("domainList")+"&action=savemap";
	q="h="+h+"&a="+a;
	$("#loadingmsg").show();
	processAJAXPostQuery(l,q,function(txt) {
			$("#loadingmsg").hide();
			loaddomainList();
			$("#createMode input#cm_host").val("");
			$("#createMode input#edit_mode").val("new");
			if(txt.length>0) {
				lgksAlert(txt);
			}
			if(m=="edit") $("#createMode").hide();
		});
}
function loaddomainList() {
	$("#domaintable tbody").html("<tr><td colspan=10 class=ajaxloading></td></tr>");
	lnk=getServiceCMD("domainList")+"&action=domainmaptable";
	$("#loadingmsg").show();
	$("#domaintable tbody").load(lnk,function(txt) {
				$("#loadingmsg").hide();
		});
}
function showHelp() {
	jqPopupDiv("#helpInfo",null,true,"700","250");
}
</script>
<?php } ?>
