<?php
if (!defined('ROOT')) exit('No direct script access allowed');

loadModule("editor");
loadEditor("codemirror");//editarea,codemirror,ckeditor,nicedit,tinymce
?>
<style>
html,body {
	overflow:hidden;
}
.page {
	width:100%;
	height:100%;
	padding:0px;
	visibility:hidden;
}
.list {
	width:20%;
	height:99%;
	margin-top:1px;
	float:left;
}
.tabs {
	width:79%;
	height:99%;
	margin-top:1px;
	margin-left:3px;
	float:left;
}
.tabspace {
	overflow:auto;
}
.tabspace.ui-tabs-panel.ui-widget-content.ui-corner-bottom {
	padding:0px;
	border:0px;
}
.hooklist {
	overflow:auto;
}
.hooklist ul {	
	padding:3px;
	margin:0px;
	padding-left:10px;
	list-style-type:square;
}
.hooklist ul li {
	margin-left:15px;
	cursor:pointer;
	padding-bottom:1px;	
}
.hooklist ul>li>ul>li:hover {
	text-decoration:underline;
}
.hooklist ul ul {
	list-style:none;
	list-style-type:none;
}
.hooklist ul li h3 {
	padding:0px;
	margin:0px;
	cursor:pointer;
	color:maroon;
}
</style>
<div class='page ui-widget-content'>
	<div class='list ui-widget-header'>
		<select id='hookowner' style='width:100%;height:25px;' onchange='loadHookList();'>
			<option value='root'>Root Hooks</option>
			<?php
				$f=ROOT.APPS_FOLDER;
				$fs=scandir($f);
				unset($fs[0]);unset($fs[1]);
				foreach($fs as $a) {
					if(file_exists($f.$a."/apps.cfg")) {
						$t=ucwords($a);
						echo "<option value='$a'>$t Hooks</option>";
					}
				}				
			?>
		</select>
		<div id=hooklist class='hooklist'></div>
	</div>
	<div class='tabs'>
		<ul>
			<li><a href='#editor' onclick=''>Hook Code</a></li>
			<li><a class='editprops edit' onclick="editCode();">Edit</a></li>
			<li><a class='editprops save' onclick="saveCode();">Save</a></li>
			<li><a class='editprops delete' onclick="deleteHook();">Delete</a></li>
			<li><a class='editprops block' onclick="blockHook(this);">Block</a></li>
			<li><a class='editprops close' onclick="closeHook();">Close</a></li>
			<li><a class='hookprops' onclick="createHook();">Create</a></li>
			<li><a id=loadedFile>::</a></li>
		</ul>
		<div id=editor class='tabspace'>
			<form>
				<textarea id=hookeditor style='width:100%;height:100%;resize:none;'></textarea>
			</form>
		</div>
	</div>
</div>
<script language=javascript>
loadedCode="";
loadedHook="";
loadedPwd="";
cmdLnk="services/?scmd=hooklist&site=<?=SITENAME?>";
$(function() {
	$(".page").css("visibility","visible");
	
	$("#list").css("height",($(".page").height())+"px");
	$(".tabspace").css("height",($(".page").height()-40)+"px");
	$("#hookeditor").css("height",($(".tabspace").height()-2)+"px");
	$("#hooklist").css("height",($(".list").height()-30)+"px");
	
	$(".tabs").tabs();
	$("button").button();
	
	loadEditor("hookeditor");
	fixEditorSize("hookeditor");
	readOnly();
	
	$(".editprops").hide();
	
	loadHookList();
});
function readOnly() {
	editor.setOption("readOnly", true);
}
function loadHookList() {
	lnk=cmdLnk+"&list=ul&pwd="+$("#hookowner").val();
	$("#hooklist").html("<div class='ajaxloading3'></div>");
	$("#hooklist").load(lnk,function() {
			$(".hooklist ul li h3").each(function() {
					//$(this).parents("li").find("ul").hide();
					$(this).click(function() {
							$(this).parents("li").find("ul").slideToggle();
						});
					
				});
			$(".hooklist ul>li>ul>li").click(function() {
					loadCode($(this).attr("fl"));
				});
		});
}
function loadCode(idh, func) {
	$("*").css("cursor","wait");
	editor.setValue("");
	lnk=cmdLnk+"&fetch="+idh+"&pwd="+$("#hookowner").val();
	$.ajax({
			url:lnk,			
			success:function(data, textStatus, jqXHR) {
				loadedCode=data;
				loadedHook=idh;
				loadedPwd=$("#hookowner").val();
				editor.setValue(data);
				
				$("*").css("cursor","auto");
				$(".hooklist ul li").css("cursor","pointer");
				$(".hooklist ul li h3").css("cursor","pointer");
				$(".editprops").css("cursor","pointer");
				
				$(".editprops").show();
				
				readOnly();
				
				$("#loadedFile").html("::"+loadedHook);
				
				if(loadedHook.indexOf("~")>2) {
					$(".editprops.block").html("Unblock");
				} else {
					$(".editprops.block").html("Block");
				}
				
				if(func!=null) func();
			},
		});
}
function editCode() {
	editor.setOption("readOnly", false);
}
function saveCode() {
	if(loadedHook.length>0 && loadedCode!=editor.getValue()) {
		lnk=cmdLnk+"&save="+loadedHook+"&pwd="+loadedPwd;
		q="&code="+encodeURIComponent(editor.getValue());
		$.ajax({
				type: 'POST',
				url: lnk,
				data: q,
				success:function(data, textStatus, jqXHR) {
					if(data.length>0) lgksAlert(data);
				},			  
			});
	}	
}
function deleteHook() {
	if(loadedHook.length>0) {
		lgksConfirm("Sure About Deleting Hook ::"+loadedHook,"Delete Hook",function() {
				lnk=cmdLnk+"&delete="+loadedHook+"&pwd="+loadedPwd;
				$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							closeHook();
						},			  
					});
			});
	}
}
function blockHook(btn) {
	if(loadedHook.length>0) {
		msg="Sure About Blocking Hook ::"+loadedHook;
		title="Block Hook";
		
		if($(btn).text().toLowerCase()=="unblock") {
			msg="Sure About Unblocking Hook ::"+loadedHook;
			title="Unblock Hook";
		}
		
		lgksConfirm(msg,title,function() {
				lnk=cmdLnk+"&block="+loadedHook+"&pwd="+loadedPwd;
				$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							closeHook();
						},			  
					});
			});
	}
}
function createHook() {
	closeHook();
	msg="To create a new hook, please give a <b>state/new-name</b> for the hook.<br/>Then you can edit the newly created hook.";
	lgksPrompt(msg,"Create New Hook",null,function(txt) {
			if(txt.length>0) {
				if(txt.indexOf("/")>1) {
					txt=txt+".php";
					lnk=cmdLnk+"&create="+txt.split(" ").join("_")+"&pwd="+$("#hookowner").val();
					idh=txt;
					$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							loadCode(idh, function() { editCode() });
						},			  
					});
				} else {
					lgksAlert("New Name Must Be Like :: <b>state/new-name</b>");
				}
			}
		});
	//$(".editprops").show();
}
function closeHook() {
	loadedCode="";
	loadedHook="";
	loadedPwd=$("#hookowner").val();
	$("#loadedFile").html("::");
	editor.setValue("");
	readOnly();
	$(".editprops").hide();
}
</script>
