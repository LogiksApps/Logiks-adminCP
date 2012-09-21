<?php
if (!defined('ROOT')) exit('No direct script access allowed');
_js(array("dialogs"));
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
#logdata {
	width:90%;
	height:70%;
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
			<input id=date2 type=text class=datepicker autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td width=300px style=''><b>Limit Changelog To Folder</b></td>
		<td>
			<select id=rootfolder>
				<option value='/'>root (/)</option>
				<?php
					$arr=scandir(ROOT.APPS_FOLDER);
					foreach($arr as $a) {
						if($a=="." || $a=="..") continue;
						$p=APPS_FOLDER.$a;
						echo "<option value='$p'>$a</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr><td colspan=10><hr/></td></tr>
	<tr><td colspan=10 align=center>
		<button id=viewbtn>View</button>
	</td></tr>
</table>
<div id=logdata class='ui-widget-content ui-corner-all'>
</div>
<script language=javascript>
$(function() {
	$("button").button();
	$(".datepicker").datepicker({
			dateFormat:"d/m/yy",
		});
	$("#viewbtn").click(function() {
			if($("#date1").val().length<=0) {
				$("#logdata").html("<div align=center>Please Select Date Limits</div>");
				return;
			}
			$("#logdata").html("<div class=ajaxloading>Loading Log ...</div>");
			lnk="services/?scmd=changelog&mode=viewlog&date1="+$("#date1").val()+"&date2="+$("#date2").val()+"&root="+$("#rootfolder").val();
			$("#logdata").load(lnk,function(responseText, textStatus, XMLHttpRequest) {
					$("#logdata .viewlink").click(function() {
							lgksOverlayURL($(this).attr("href"),"View File :: " + $(this).text());
							return false;
						});
				});
		});
});
</script>
