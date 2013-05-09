<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$webPath=getWebPath(__FILE__);
$rootPath=getRootPath(__FILE__);

_js(array("dialogs"));

loadModule("editor");
loadEditor("codemirror");

?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<link href='<?=$webPath?>errors.css' rel='stylesheet' type='text/css' media='all' />
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>

<div style='width:100%;height:100%;overflow:hidden;'>
<div id=toolbar class="toolbar ui-widget-header">
	<div class='left' style='margin-left:5px;'>
		<button style='width:75px;' onclick="changeShellMode(this)" value='php'>PHP</button>
		<button title='Clear Console.' onclick="cls()"><div class='clearicon'>Clear</div></button>
		<button title='Run Command.' onclick="runCmd($('#cmdprompt').val());$('#cmdprompt').val('');"><div class='runicon'>Run</div></button>
		<button title='Clear Console.' onclick="showEditor();"><div class='editoricon'>Editor</div></button>
		<button title='Empty Trash.' onclick="dumpTrash();"><div class='trashicon'>Trash</div></button>
		<button title='help Contents.' onclick="showHelp();"><div class='helpicon'>Help</div></button>
	</div>
	<div class='right' style='margin-right:5px;'>
		<button style='width:45px;' onclick="lastCmd()"><div class='previousicon'></div></button>
		<button style='width:45px;' onclick="lastButOneCmd()"><div class='nexticon'></div></button>
	</div>
</div>
<div id=workspace class="ui-widget-content" style='width:100%;'>
	<div id=cmdline>
		<b>>></b><input id=cmdprompt name=cmd class='commands autocomplete' type='text' src='services/?scmd=lookups&src=cmds-php' />
	</div>
	<div id=results></div>
</div>
<div id=editorspace class="ui-widget-content" title="Code Editor">
	<textarea id=code_editor style='width:100%;height:100%;'>>>Welcome To Konsole</textarea>
</div>
<div id=changeMode class="ui-widget-content noshow" title="Change Mode">
	<select id=mode style='width:94%;height:27px;margin-top:4px;margin-left:10px;font-weight:bold;'>
			<option value='php'>PHP Mode</option>
			<option value='bash'>BASH Mode</option>
	</select>
</div>
</div>
<script>
cmdLnk=getServiceCMD("konsole");
$(function() {
	loadEditor("code_editor");
});
</script>
