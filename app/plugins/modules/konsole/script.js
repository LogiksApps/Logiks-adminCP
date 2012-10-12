welcome=">>Welcome To Konsole";
diffBar="<br/><br/><hr style='margin:0px;padding:0px;'/>";
oldCmds=[];
old1=[];
old2=[];
lastCMD="";
defCode="//Type Your Code Here";
stdCmds=["cls","edit"];
$(function() {
	w=getWindowSize();
	$("#workspace").css("height",(w.h-$("#toolbar").height()-5)+"px");
	$("#workspace").css("width",(w.w-0)+"px");
	
	$("#results").css("height",($("#workspace").height()-$("#cmdline").height())+"px");
	
	$("button").button();	
	$(".tabs").tabs();
	
	$("#cmdprompt").keydown(function(event,ui) {
			if(event.keyCode==13) {
				runCmd($("#cmdprompt").val());
				$("#cmdprompt").val('');
			}
		});
	
	loadAC("#workspace input.autocomplete");
	
	clearKonsole();
	//editor.setValue(defCode);
});
function loadAC(ids,src1) {
	$(ids).each(function() {
			var minL=1;
			if($(this).attr("minlength")!=null) minL=parseInt($(this).attr("minlength"));
			if(src1!=null) $(this).attr("src",src1);
			
			if($(this).attr("src")!=null) {
				var href=$(this).attr("src");
				$(this).autocomplete({
						minLength: minL,
						source:href,
					});
			}
		});
}
function runCmd(code) {
	if(code.length>0) {
		if($.inArray(code,stdCmds)>=0) {
			if(typeof(code)=='function') code();
			else window[code]();			
		} else {
			s=cmdLnk+"&mode="+$("#mode").val();
			s1="&cacheClear=true&code="+code;
			
			lastCMD=code;
			old1=oldCmds;
			oldCmds.push(lastCMD);
			old2=[];
			old2.push(lastCMD);
			
			processAJAXPostQuery(s,s1,function(txt) {					
					var e=document.getElementById('results');
					$(e).append("<br/>" + txt + diffBar);
					e.scrollTop=e.scrollHeight;
				});
		}
	}
}
function runCode(code) {
	if(code.length>0) {
		s=cmdLnk+"&mode="+$("#mode").val();
		s1="&cacheClear=false&code="+code;
		
		lastCMD=code;
		old1=oldCmds;
		oldCmds.push(lastCMD);
		old2=[];
		old2.push(lastCMD);
		
		processAJAXPostQuery(s,s1,function(txt) {
				var e=document.getElementById('results');
				$(e).append("<br/>" + txt + diffBar);
				e.scrollTop=e.scrollHeight;
			});
	}
}
function changeShellMode(btn) {
	lgksPopup("#changeMode",
			{},
			{width:300,height:100,show:"blind",hide:"blind",closeOnEscape:true,
				close: function(event, ui) {
					v=$("#changeMode #mode").val();
					$(btn).find("span").html(v.toUpperCase());					
					if(v=="php") loadAC("#workspace input.autocomplete","services/?scmd=lookups&src=cmds-php");
					else if(v=="bash") loadAC("#workspace input.autocomplete","services/?scmd=lookups&src=cmds-bash");
				}
			}
		);
}
function lastCmd() {
	s=old1.pop();
	if(s.length>0) {
		old2.push(s);
		$("#cmdprompt").val(s);		
	}
}
function lastButOneCmd() {	
	console.log(old2);
	s=old2.pop();
	if(s.length>0) {
		old1.push(s);
		$("#cmdprompt").val(s);
	}
}
function dumpTrash() {
	s=cmdLnk+"&action=view-trash";
	processAJAXQuery(s,function(txt) {
			lgksPopup(txt,
					{
						"Clear":function() {
							s1=cmdLnk+"&action=clear-trash";
							processAJAXQuery(s1);
							$(this).dialog( "close" );
						},
						"Close":function() {
							$(this).dialog( "close" );
						},
					},
					{width:400,height:200,show:"blind",hide:"blind",closeOnEscape:true,resizable:false},
					"data");
		});
}
function clearKonsole() {
	$("#results").html(welcome+diffBar);
}
function showErrorPopup(id) {
	jqPopupDiv(id,null, true, $(document).width()-50,$(document).height()-50,"blind");
}
function showEditor() {
	//jqPopupDiv("#editorspace",null, true, );
	lgksPopup("#editorspace",
		{
			"Run":function() {
				code=editor.getValue();
				editor.setValue(defCode);
				runCode(code);
				$(this).dialog( "close" );
			},
			"Cancel":function() {
				$(this).dialog( "close" );
			},
		},
		{width:$(document).width()-50,height:$(document).height()-50,show:"blind",hide:"blind",closeOnEscape:true}
		);
}
//Std Functions
function cls() {
	$("#cmdprompt").val("");
	$("#results").html(">>");
}
function edit() {
	$("#cmdprompt").val("");
	showEditor();
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Help !",null,true,"700","300");
}
