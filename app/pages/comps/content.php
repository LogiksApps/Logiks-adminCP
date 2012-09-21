<?php
if(!defined('ROOT')) exit('No direct script access allowed');
//$_SESSION["LGKS_EDITOR_FPATH"]=ROOT;

loadModule("page");
printPageContent("workspace");

_js(array("jquery.mailform"));
?>
<style>
#workspace.ui-corner-all {border:0px !important;}
</style>

<script language=javascript>
$(function() {
	$("body").attr("oncontextmenu","return false");
	$("body").attr("onselectstart","return false");
	$("body").attr("ondragstart","return false");
});
function initUI(ele) {
	$(ele+" button").button();
	$("select").addClass("ui-state-default ui-corner-all");
	$(ele+" .datepicker").datepicker();
	$(ele+" .progressbar").progressbar({value:37});
	$(ele+" .slider").slider();
	$(ele+" .draggable").draggable();
	$(ele+" .accordion").accordion({
			fillSpace: true
		});
}
function showProfileEditor() {
	openInNewTab('Profile', '?site=<?=SITENAME?>&page=profile');
}
function showSettingsEditor() {
	openInNewTab('Settings', '?site=<?=SITENAME?>&page=settings');
}
function openMailPad(mailto,subject,body,attach) {
	if(mailto==null) mailto="";
	if(subject==null) subject="";
	if(body==null) body="";
	
	$.mailform(mailto,subject,body);
}
</script>
