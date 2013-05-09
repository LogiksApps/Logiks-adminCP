<?php
if (!defined('ROOT')) exit('No direct script access allowed');
_js(array("dialogs","jquery.ui-timepicker"));
$dtType="datetime";
?>
<style>
body {
	overflow:hidden;
}
#form {
	width:600px;
	margin:auto;
	padding:5px;
	margin-bottom:10px;
	margin-top:10px;
}
#extraTools {
	width:90%;height:23px;
	margin:auto;
}
#logdata {
	width:90%;
	height:68%;
	margin:auto;
	padding:5px;
	overflow:auto;
}
input,select {
	width:100%;
	height:20px;
	border:1px solid #aaa;
	font-weight:bold;
}
#logdata input {
	width:13px;height:13px;
	border:0px;
}
#extraTools input[type=checkbox]{
	width:13px;height:13px;
	border:0px;
}
#extraTools label {
}
select {
	height:25px;
}
button {
	width:150px;
}
a {
	text-decoration:none;
	color:maroon;
	font-size:14px;
}
.file:hover {
	background:yellow;
	cursor:pointer;
}
#downloadbtn {display:none;}
</style>
<table id=form border=0 cellpadding=0 cellspacing=0 class='ui-widget-header ui-corner-all'>
	<tr>
		<td width=300px style=''><b>View Change Log From Date</b></td>
		<td>
			<input id=date1 type=text class=datepicker autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td width=300px style=''><b>View Change Log To Date</b></td>
		<td>
			<input id=date2 type=text class=datepicker autocomplete="off" value='<?=date("d/m/Y H:i:0")?>' />
		</td>
	</tr>
	<tr>
		<td width=300px style=''><b>Limit Changelog To </b></td>
		<td>
			<select id=rootfolder>
				<optgroup label='System'>
					<option value='/'>Only Root (No Apps)</option>
					<option value='*'>Total System</option>
				</optgroup>
				<optgroup label='Apps Sites'>
					<?php
						$arr=scandir(ROOT.APPS_FOLDER);
						foreach($arr as $a) {
							if($a=="." || $a=="..") continue;
							elseif(is_file(ROOT.APPS_FOLDER.$a)) continue;
							$p=APPS_FOLDER.$a;
							echo "<option value='$p'>$a</option>";
						}
					?>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr><td colspan=10><hr/></td></tr>
	<tr><td colspan=10 align=center>
		<button id=patchesbtn title='View Patches History'>Old Patches</button>
		||
		<button id=viewbtn title='View All Changes'>List Changes</button>
		<button id=downloadbtn title='Create Patche From Selected Changes.'>Download</button>
	</td></tr>
</table>
<div id=extraTools class='ui-widget-header ui-corner-top'>
	<input type=checkbox cmd=toggleAll style='' default="true" /><label for=toggleAll>Toggle All</label>
</div>
<div id=logdata class='ui-widget-content ui-corner-all'>
</div>
<iframe name=downloadFrame id=downloadFrame style='display:none' width=0 height=0 ></iframe>
<script language=javascript>
$(function() {
	$("button").button();
	$("#logdata").delegate(".viewlink","click",function() {
			lgksOverlayURL($(this).attr("href"),"View File :: " + $(this).text());
			return false;
		});
	$("#extraTools input[type=checkbox]").click(function() {
			cmd=$(this).attr("cmd");
			ele=this;
			if(cmd=="toggleAll") {
				if($("#logdata input[type=checkbox]:checked").length<=0) {
					$("#logdata input[type=checkbox]").each(function() {this.checked=ele.checked;});
				} else {
					$("#logdata input[type=checkbox]").each(function() {this.checked=ele.checked;});
				}
			}
		});
	<?php if($dtType=="datetime") { ?>
	$(".datepicker").datetimepicker({
					timeFormat:"h:m:s",
					separator:' ',
					ampm:false,
					changeMonth:true,
					changeYear:true,
					showButtonPanel:true,
					//yearRange:yearRange,
					dateFormat:"d/m/yy",
				});
	<?php } else { ?>
	$(".datepicker").datepicker({
			dateFormat:"d/m/yy",
		});
	<?php } ?>
	$("#viewbtn").click(function() {
			if($("#date1").val().length<=0) {
				$("#logdata").html("<div align=center>Please Select Date Limits</div>");
				return;
			}
			$("#logdata").html("<div class=ajaxloading>Loading Log ...</div>");
			lnk=getServiceCMD("changelog")+"&mode=viewlog&date1="+$("#date1").val()+"&date2="+$("#date2").val()+"&root="+$("#rootfolder").val();
			$("#downloadbtn").hide();
			processAJAXQuery(lnk,function(txt) {
					$("#logdata").html(txt);
					$("#downloadbtn").show();
					$("#extraTools input[type=checkbox][default]").each(function() {
							if($(this).attr("default")=="checked") this.checked=true;
							else this.checked=false;
						});
				});
		});
	$("#downloadbtn").click(function() {
			if($("#logdata").text().length<=0) {
					$("#logdata").html("<div align=center>Please Click <u>View</u> To Dowloadable Changelog</div>");
					return;
			}
			if($("#logdata input[type=checkbox]:checked").length<=0) {
					lgksAlert("No files are selected to be downloaded...");
					return;
			}
			a=[];
			$("#logdata input[type=checkbox]:checked").each(function() {
					a.push("file[]="+$(this).attr("rel"));
				});
			lnk=getServiceCMD("changelog")+"&mode=downloadzip&"+a.join("&");

			//window.open(lnk);
			$("iframe#downloadFrame").attr("src",lnk);
		});
	$("#patchesbtn").click(function() {
			$("#downloadbtn").hide();
			$("#logdata").html("<div class=ajaxloading>Loading Log ...</div>");
			lnk=getServiceCMD("changelog")+"&mode=listpatches";
			processAJAXQuery(lnk,function(txt) {
					$("#logdata").html(txt);
				});
		});
});
</script>
