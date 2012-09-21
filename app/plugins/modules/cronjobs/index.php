<?php
if (!defined('ROOT')) exit('No direct script access allowed');

_css(array("colors"));

loadModule("editor");
loadEditor("codemirror");
loadModule("page");

$params=array();
$params["toolbar"]=array(
		array("title"=>"Reload","icon"=>"reloadicon","tips"=>"ReLoad Job List","onclick"=>"reloadList()"),
		array("title"=>"Job","icon"=>"addicon","tips"=>"Create RecurrentJob","onclick"=>"createJob(1)"),
		array("title"=>"Task","icon"=>"addicon","tips"=>"Create OneTime Task","onclick"=>"createJob(2)"),
		array("title"=>"Edit","icon"=>"editicon","tips"=>"Edit Task/Job","onclick"=>"editJob()"),
		array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Task/Job","onclick"=>"deleteJob()"),
		array("title"=>"Run","icon"=>"openicon","tips"=>"Run Task/Job Now","onclick"=>"runJob()"),
		array("title"=>"Help","icon"=>"helpicon","tips"=>"Help !","onclick"=>"showHelp()"),
	);
$params["contentarea"]="printContent";

$webPath=getWebPath(__FILE__);

printPageContent("apppage",$params);
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' /> 
<script language=javascript>
cmdLnk="services/?scmd=cronjobs&site=<?=SITENAME?>";
$(function() {
	$(".datefield").datepicker({
			dateFormat:'<?=getConfig("DATE_FORMAT")?>',
		});
	$(".forms .uploadbutton").addClass("ui-corner-all");
	$(".forms .uploadbutton").click(uploadScripts);
	
	$("#scriptUploader_file").change(function() {
			$("#scriptUploader_file").parents("form").submit();
		});
	$("#scriptUploader_target").load(function() {
			$("#scriptUploader_file").val("");
		});
	reloadList();
	
	loadEditor("scriptEditor_txt");
});
function reloadList() {
	$("#loadingmsg").show();
	$("#datatable tbody").html("<tr><td colspan=10><div class=ajaxloading6></div></td></tr>");
	lnk=cmdLnk+"&action=list";
	$("#datatable tbody").load(lnk,function() {
			$("#datatable tr td").click(function() {
					$(this).parents("tbody").find("input[type=radio]").removeAttr("checked","true");
					$(this).parents("tbody").find("tr.selected").removeClass("selected");
					
					$(this).parents("tr").find("input[type=radio]").attr("checked","true");
					$(this).parents("tr").addClass("selected");
				});
			$("#datatable input[type=radio]").change(function() {
					$(this).parents("tbody").find("tr.selected").removeClass("selected");
					if($(this).is(":checked")) {
						$(this).parents("tr").addClass("selected");
					} else {
						$(this).parents("tr").removeClass("selected");
					}
				});
			$("#datatable input[type=checkbox]").change(function() {
					if($(this).attr("name")=="blocked_job") {
						id=$(this).parents("tr").attr("rel");
						if($(this).is(":checked")) execCmd(id,"block");
						else execCmd(id,"unblock");
					} else if($(this).attr("name")=="method_job") {
						id=$(this).parents("tr").attr("rel");
						if($(this).is(":checked")) execCmd(id,"post");
						else execCmd(id,"get");
					} else if($(this).attr("name")=="run_once_job") {
						id=$(this).parents("tr").attr("rel");
						if($(this).is(":checked")) execCmd(id,"run_once");
						else execCmd(id,"run_periods");
					}
				});
			$("#datatable .scripteditbutton").click(function() {
					if($(this).parents("td").attr("rel").length>0) {
						f=$(this).parents("td").attr("rel");
						lnk=cmdLnk+"&action=tsk&tsk=fetchscript&script="+f;
						processAJAXQuery(lnk,function(txt) {
								editor.setValue(txt);
								lgksOverlayDiv("#scriptEditor",function(txt) {
										lnk=cmdLnk+"&action=tsk&tsk=savescript&script="+f;
										q="&code="+editor.getValue();
										processAJAXPostQuery(lnk,q,function(txt) {
												if(txt.length>0) lgksAlert(txt);
												editor.setValue("");
											});
										return true;
									});
							});
					}
				});
			$("#loadingmsg").hide();
		});
}
function createJob(i) {
	$("#createForm_"+i).find("input[type=text]").val("");
	$("select.scriptlist").load(cmdLnk+"&action=tsk&tsk=listscripts");
	$("select.sitelist").load(cmdLnk+"&action=tsk&tsk=listsites");
	
	$("#createForm_1").find("#schdulle_selector #cstm_prd").html("Custom Period");
	$("#createForm_1").find("#schdulle_selector #cstm_prd").attr("value","*");
	$("#createForm_1").find("#schdulle_selector").val("60");
	$("#createForm_1").find("input[name=schdulle]").val("60");
	
	$("#editform").hide();
	$("#createform").show();
	osxPopupDiv("#createForm_"+i);
}
function editJob() {
	if($("#datatable tr.selected").length>0) {
		id=$("#datatable tr.selected").attr("rel");
		tr=$("#datatable tr.selected");
		
		$("select.scriptlist").load(cmdLnk+"&action=tsk&tsk=listscripts", function() {
				$("#createForm_1").find("select[name=scriptpath]").val(tr.find("td[name=scriptpath]").text());
			});
		$("select.sitelist").load(cmdLnk+"&action=tsk&tsk=listsites", function() {
				$("#createForm_1").find("select[name=forsite]").val(tr.find("td[name=site]").text());
			});
		
		$("#createForm_1").find("input[name=id]").val(id);
		$("#createForm_1").find("input[name=run_only_once]").val(tr.find("td[name=run_once]").attr('v'));
		
		$("#createForm_1").find("input[name=title]").val(tr.find("td[name=title]").text());
		$("#createForm_1").find("input[name=description]").val(tr.find("td[name=description]").text());
		$("#createForm_1").find("select[name=method]").val(tr.find("td[name=method]").attr('v'));
		$("#createForm_1").find("input[name=script_params]").val($("#datatable tr.selected").attr("params"));
		
		period=tr.find("td[name=schdulle]").attr("rel");
		$("#createForm_1").find("input[name=schdulle]").val(period);
		if($("#createForm_1").find("#schdulle_selector option[value="+period+"]").length>0) {
			$("#createForm_1").find("#schdulle_selector").val(period);
			$("#createForm_1").find("#schdulle_selector #cstm_prd").html("Custom Period");
			$("#createForm_1").find("#schdulle_selector #cstm_prd").attr("value","*");
		} else {
			$("#createForm_1").find("#schdulle_selector #cstm_prd").html("Custom Period = "+period);
			$("#createForm_1").find("#schdulle_selector #cstm_prd").attr("value",period);
			$("#createForm_1").find("#schdulle_selector").val(period);
		}
		
		$("#createform").hide();
		$("#editform").show();
		
		osxPopupDiv("#createForm_1");
	}
}
function deleteJob() {
	if($("#datatable tr.selected").length>0) {
		id=$("#datatable tr.selected").attr("rel");
		execCmd(id,"delete");
	}
}
function runJob() {
	if($("#datatable tr.selected").length>0) {
		id=$("#datatable tr.selected").attr("rel");
		execCmd(id,"run");
	}
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Jobs/Tasks Help !",null,true,"700","550");
}
function execCmd(id,cmd) {
	$("#loadingmsg").show();
	lnk=cmdLnk+"&action=cmd&cmd="+cmd+"&id="+id;
	processAJAXQuery(lnk,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			reloadList();
		});
}
function updateSchdulePeriod(target,period, selector) {
	if(period=="*") {
		lgksPrompt("Please give the Period in number","Custom Period",null,function(period) {				
				if(period.length>0 && !isNaN(period)) {
					$(target).val(period);
					$(selector).find("option:selected").html("Custom Period = "+period);
				} else {
					$(selector).find("option:selected").html("Custom Period");// = Not Valid
					$(selector).find("option:selected").attr("value","*");
					$(selector).val($(target).val());
				}
			});
	} else {
		if(period.length>0 && !isNaN(period)) $(target).val(period);
	}
}
function uploadScripts() {
	$("#scriptUploader_file").val("");
	jqPopupDiv("#scriptUploader",function() {
			$("select.scriptlist").load(cmdLnk+"&action=tsk&tsk=listscripts");
		},true,300,200);
}
function saveForm(formDiv,mode) {
	if($(formDiv).find("input[name=title]").val().length<=0) {
		lgksAlert("Job Title Is Must");
		return;		
	}
	if($(formDiv).find("input[name=schdulle]").val().length<=0) {
		lgksAlert("Job Schdule Is Must");
		return;		
	}
	lnk=cmdLnk+"&action="+mode;
	//alert(lnk);
	q="";
	$("#loadingmsg").show();
	$(formDiv).find("input,select").each(function() {
			name=$(this).attr("name");
			val=$(this).val();
			if(name!=null && name.length>0) { q+="&"+name+"="+val; }
		});
	processAJAXPostQuery(lnk,q,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			formDiv.dialog('close');
			reloadList();
		});
}
</script>
<?php function printContent() { ?>
	<table id=datatable width=99% cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:2px solid #aaa;'>
		<thead>
			<tr align=center class='ui-widget-header'>
				<th width=40px>*</th>
				<th>Task</th>
				<th width=100px>Period</th>
				<th width=70px>Run-Once</th>
				<th>Script</th>
				<th width=100px>Site</th>
				<th>Description</th>
				<th width=60px>Method</th>
				<th width=150px>Last Run</th>
				<th width=50px>Blocked</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div style="display:none;">
		<div id=createForm_1 class="forms" style="width:500px;" title="Create Recurrent Job">
			<table width=99% cellpadding=0 cellspacing=0 border=0 style='margin:5px;'>
				<input name=id type=hidden value='0' />
				<input name=run_only_once type=hidden value='false' />
				<tr><th align=left width=210px>Title</th><td><input name=title type=text /></td></tr>
				<tr><th align=left>Description</th><td><input name=description type=text /></td></tr>
				<tr><th align=left>Script</th>
					<td>
						<select name=scriptpath class=scriptlist>
							<option value='*'>Loading Scripts</option>
						</select>
						<div class='uploadbutton'></div>
					</td>
				</tr>
				<tr>
					<th align=left>Schdule</th>
					<td>
						<input name=schdulle id=Schdule1 type=hidden value='60' />
						<select id=schdulle_selector onchange="updateSchdulePeriod('#Schdule1',$(this).val(),this)">
							<option value='60'>Every Minute</option>
							<option value='1800'>Every Half-An Hour</option>
							<option value='3600'>Every Hour</option>
							<option value='43200'>Every 12 Hours</option>
							<option value='86400'>Every Day (24 Hours)</option>
							<option value='604800'>Every Week</option>
							<option value='1296000'>Every 15 Days</option>
							<option value='2592000'>Every 30 Days</option>
							<option value='31536000'>Every Year</option>
							<option id=cstm_prd value='*'>Custom Period</option>
						</select>
					</td>
				</tr>
				<tr><th align=left>Parameters (comma sperated)</th><td><input name=script_params type=text /></td></tr>
				<tr><th align=left>Method</th>
					<td>
						<select name=method>
							<option value='PHP'>PHP</option>
							<option value='GET'>GET</option>
							<option value='POST'>POST</option>
						</select>
					</td>
				</tr>
				<tr><th align=left>For Site</th>
					<td>
						<select name=forsite class=sitelist>
							<option value='*'>Loading Site List</option>
						</select>
					</td>
				</tr>
				<tr><td colspan=10>&nbsp;</td></tr>
				<tr><td colspan=10><hr/></td></tr>
				<tr><td colspan=10 align=center>
					<button onclick="$(this).parents('div.forms').dialog('close');">Cancel</button>
					<button id=createform onclick="saveForm($(this).parents('div.forms'),'create')">Save</button>
					<button id=editform onclick="saveForm($(this).parents('div.forms'),'edit')">Update</button>
				</td></tr>
			</table>
		</div>
		<div id=createForm_2 class="forms" style="width:500px;" title="Create One-Time Tasks">
			<table width=99% cellpadding=0 cellspacing=0 border=0 style='margin:5px;'>
				<input name=id type=hidden value='0' />
				<input name=run_only_once type=hidden value='true' />
				<tr><th align=left width=210px>Title</th><td><input name=title type=text /></td></tr>
				<tr><th align=left>Description</th><td><input name=description type=text /></td></tr>
				<tr><th align=left>Script</th>
					<td>
						<select name=scriptpath class=scriptlist>
							<option value='*'>Loading Scripts</option>
						</select>
						<div class='uploadbutton'></div>
					</td>
				</tr>
				<tr><th align=left>Schdule</th><td><input name=schdulle type=text class="datefield" /></td></tr>
				<tr><th align=left>Parameters (comma sperated)</th><td><input name=script_params type=text /></td></tr>
				<tr><th align=left>Method</th>
					<td>
						<select name=method>
							<option value='PHP'>PHP</option>
							<option value='GET'>GET</option>
							<option value='POST'>POST</option>
						</select>
					</td>
				</tr>
				<tr><th align=left>For Site</th>
					<td>
						<select name=forsite class=sitelist>
							<option value='*'>Loading Site List</option>
						</select>
					</td>
				</tr>
				<tr><td colspan=10>&nbsp;</td></tr>
				<tr><td colspan=10><hr/></td></tr>
				<tr><td colspan=10 align=center>
					<button onclick="$(this).parents('div.forms').dialog('close');">Cancel</button>
					<button onclick="saveForm($(this).parents('div.forms'),'create')">Save</button>
				</td></tr>
			</table>
		</div>
		<div id=scriptUploader class="forms" title="Script Uploader">
			<form METHOD="POST" enctype="multipart/form-data" target='scriptUploader_target' action="services/?scmd=cronjobs&site=<?=SITENAME?>&action=tsk&tsk=uploadscript">
				<input id=scriptUploader_file name=myfile type=file />
			</form>
			<iframe id=scriptUploader_target name=scriptUploader_target style='width:100%;height:50px;border:0px;overflow:hidden;'></iframe>
		</div>
		<div id=scriptEditor class="forms" title="Script Editor" style='overflow:hidden;'>
			<textarea id=scriptEditor_txt style="width:99%;height:98%;margin:auto;resize:none;" >asdasd</textarea>
		</div>
	</div>
<?php } ?>
