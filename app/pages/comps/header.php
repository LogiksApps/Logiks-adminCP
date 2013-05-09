<?php
if (!defined('ROOT')) exit('No direct script access allowed');

include APPROOT."config/tools.php";

loadModule("accessibility");
?>
<div class='right'>
	<a class='button clr_darkblue' href='<?=SiteLocation?>api/logout.php' style='float:right;margin-left:10px;padding-top:5px;padding-right:10px;' >
		<img src='<?=loadMedia("icons/lock.png")?>' width=22px height=22px style='float:left;margin-top:-4px;' />Logout
	</a>
	<a class='button clr_darkmaroon' onclick="showSettingsEditor()" style='float:right;margin-left:10px;padding-top:5px;padding-right:10px;'>
		<img src='<?=loadMedia("icons/config.png")?>' width=22px height=22px style='float:left;margin-top:-5px;' />Configs
	</a>
	<a class='button' onclick="showProfileEditor()" style='width:25px;height:25px;border:0px;display:inline;'>
		<img src='<?=loadMedia("icons/huser.png")?>' width=25px height=22px style="margin-top:-5px;" />
	</a>
	<a onclick="showProfileEditor()" style='cursor:pointer;'>
	<?php

		if(isset($_SESSION['SESS_USER_NAME'])) echo "Welcome, <b>" . $_SESSION['SESS_USER_NAME']."</b>";
	?>
	</a>

	<?php
		loadAccessibilityButtons('#sidebar a');
	?>
</div>
<div class='left'>
	<img src='<?=loadMedia(LOGO)?>' />
</div>
<div class='center'>
	<?php
		foreach($_launchBar as $a=>$b) {
			if($b[1]!="#") {
				$title=$b[0];
				if(isset($b[1])) {
					if(strpos($b[1],"://")>0) {
						$href=$b[1];
					} else {
						$href=_site($b[1]);
					}
				} else $href="#";
				if(isset($b[2])) $target=$b[2]; else $target="";
				if(isset($b[3])) $click=$b[3]; else $click="";
				$s="<a class='button' title='$title' href='$href' target='$target' onclick=\"$click\">";

				$s.="<img src='".loadMedia("icons/launchbar/$a.png")."' width=32px height=32px /></a>";
				echo $s;
			}
		}
	?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a style='float:right;margin-top:-40px;margin-right:20px;background:transparent;border:0px;' class='button' title='Quickbar Editor' onclick="osxPopupDiv('#toolbareditmenu',null,800);"><img src='<?=loadMedia("icons/launchbar/gear.png")?>' width=32px height=32px /></a>
	<div id=toolbareditmenu  style='display:none;font-size:14px;' title='Edit QuickBar'>
		<table style='width:100%;' border=0>
			<?php
				$arr=scandir(ROOT.APPS_FOLDER);
				$sites="";
				foreach($arr as $a) {
					if($a=="." || $a=="..") continue;
					$sites.="<option value='$a'>$a</option>";
				}
				foreach($_launchBar as $a=>$b) {
					echo "<tr>";
					echo "<td width=140px>".$b[0]."</td>";
					echo "<td><input name='$a' type=text style='border:1px solid #aaa;width:100%;' value='".$b[1]."' /></td>";
					echo "<td width=200px><select style='border:1px solid #aaa;width:100%;' onchange='updateField(this)'>$sites</select></td>";
					echo "</tr>";
				}
			?>
			<tr><td colspan=10><hr/></td></tr>
			<tr><td colspan=10 align=center>
				<button onclick='saveQuickBar()'>Save</button>
			</td></tr>
		</table>
	</div>
</div>

<style>
.ui-dialog-titlebar, .ui-dialog-header {font-size:15px;}
.ui-dialog .ui-button-text {font-size:10px;}
.osx.ui-dialog {padding:0px;}
.osx .ui-dialog-titlebar.ui-corner-all { border-radius:0px;	 }
</style>
<script language=javascript>
$(function() {
	$("button").button();
});
function updateField(e) {
	$(e).parents("tr").find("input").val($(e).val());
}
function saveQuickBar() {
	lnk=getServiceCMD("qtools")+"&mode=savequickbar";
	s="";
	$("#toolbareditmenu input").each(function() {
			if($(this).val().length==0) $(this).val("#");
			s+=$(this).attr("name")+"="+$(this).val()+"&";
		});
	processAJAXPostQuery(lnk,s,function(txt) {
			window.location.reload();
		});
}
/*
$('#searchtxt').focus(function() {
	if($("#searchtxt").val().length<=0) {
		$('#searchlbl').css("opacity","0.25");
	} else {
		$('#searchlbl').css("opacity","0.1");
	}
});
$('#searchtxt').blur(function() {
	if($("#searchtxt").val().length<=0) {
		$('#searchlbl').css("opacity","0.5");
	} else {
		$('#searchlbl').css("opacity","0.1");
	}
});
$("#searchtxt").keypress(function (event) {
	if($("#searchtxt").val().length>0) {
		$('#searchlbl').css("opacity","0.1");
	}
	if(event.which==13) {
		var s=$("#searchtxt").val();
		$("#searchtxt").val("");
		alert("Searching :: " + s);
	}
});*/
</script>
