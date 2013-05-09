<?php if($_REQUEST["page"]=="home") { ?>	
<script>
$(function() {
	s="<a class='button' onclick='openCmdPrompt()' style='width:25px;height:25px;border:0px;display:inline;'>";
	s+="<img src='<?=loadMedia("icons/cmd.png")?>' width=22px height=22px style='margin-top:5px;float:left;' />";
	s+="</a>";
	
	$("#header .right").append(s);
});
cmd_1245=1;
last_cmd_1245="";
function openCmdPrompt() {
	lgksPrompt("Please Give The Command Page ?","Command Prompt",last_cmd_1245,function(txt) {
			if(txt.length<=0) return;
			last_cmd_1245=txt;
			if(!(txt.indexOf("http")==0 || txt.indexOf("https")==0)) {
				if(txt.indexOf("=")<0) {
					txt="site=<?=SITENAME?>&page="+txt;
				} else {
					if(txt.indexOf("site")<0) {
						txt=txt+"&site=<?=SITENAME?>";
					}
				}
				if(txt.indexOf("&")==0) {
					txt=txt.substr(1);
				}
				if(txt.indexOf("?")<0) {
					txt="?"+txt;
				}
			}
			if(typeof openInNewTab=="function") {
				openInNewTab("Command "+cmd_1245,txt);
				cmd_1245++;
			} else {
				window.open(txt);
			}
		}).dialog("option",{
				"closeOnEscape":true,
				"draggable":true,
				"position":'top',
				"modal":true,
			});
}
</script>
<?php } ?>
