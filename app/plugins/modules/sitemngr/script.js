$(function() {
	$("#sitetable tbody").delegate("tr","click",function() {
			$("#sitetable tbody tr.active").removeClass("active");
			$(this).addClass("active");
		});
	$("#sitetable tbody").delegate(".minibtn","click",function() {
			nm=$(this).attr("name");
			app=$(this).attr("rel");
			if(nm=="viewsite") {
				src="index.php?site="+app;
				window.open(src);
			} else if(nm=="editdbms") {
				src="index.php?&site=admincp&page=configeditor&cfg=db&forsite="+app;
				parent.openInNewTab("DB : "+app, src);
			} else if(nm=="editsite") {
				src="index.php?&site=admincp&page=configeditor&cfg=apps&forsite="+app;
				parent.openInNewTab("Edit : "+app, src);
			}
		});
	$("#sitetable").delegate("tr","dblclick",function() {
			app=$(this).find("div[name=viewsite]").attr("rel");
			title=$(this).find("td.title").html();
			lgksConfirm("Do you want to visit site <b>"+title+"</b> !","Visit Site",function() {
					src="index.php?site="+app;
					window.open(src);
				});
		});
	$("#appimageManager tbody").delegate(".minibtn","click",function() {
			app=$(this).attr("rel");
			nm=$(this).attr("name");
			t=$(this).attr("title");
			if(nm=="info") {
				lnk=cmdLnk+"&action=infoappimage&appimage="+app;
				jqPopupURL(lnk,"AppImage Info",null,true,600,300,"fade");
			} else if(nm=="delete") {
				lgksConfirm("Do you really want to delete selected AppImage <b>'"+t+"'</b>?","Delete AppImage ?",function() {
						$("#loadingmsg").show("fast");
						lnk=cmdLnk+"&action=deleteappimage&appimage="+app;
						processAJAXQuery(lnk,function(txt) {
									if(txt.length>0) lgksAlert(txt);
									reloadAppImagesList();
								});
					}).dialog("option",{show:"fade",hide:"fade"});
			}
		});
	loadSiteList();
});
function loadSiteList() {
	$("#loadingmsg").show("fast");
	$("#sitetable tbody").html("<tr><td colspan=100 class=ajaxloading>Loading Site List ...</td></tr>");
	lnk=cmdLnk+"&action=sitelist";
	$("#sitetable tbody").load(lnk, function() {
			$("#sitetable tbody td:not(.blocked)").each(function() {
					if($(this).html()=="true") {$(this).html("<span style='color:#1F3FBF;'><b>true</b></span>");}
					if($(this).html()=="false") {$(this).html("<span style='color:#BF190D;'><b>false</b></span>");}
				});
			$("#sitetable tbody td.blocked").each(function() {
					if($(this).html()=="true") {$(this).html("<span style='color:#BF190D;'><b>true</b></span>");}
					if($(this).html()=="false") {$(this).html("<span style='color:#04BF20;'><b>false</b></span>");}
				});
			$("#sitetable tbody td.access").editInPlace({
					show_buttons:false,
					default_text:"",
					field_type: "select",
					save_if_nothing_changed:false,
					select_options:"Private:private,Public:public",
					callback: function(idOfEditor, enteredText, orinalHTMLContent, settingsParams, callbacks) {
						app=$("#sitetable tbody tr.active td.applink").text();
						b=orinalHTMLContent.toLowerCase();
						s=enteredText.toLowerCase();
						$("#loadingmsg").show("fast");
						lnk=cmdLnk+"&action=accessmode&app="+app+"&current="+b+"&mode="+s;
						processAJAXQuery(lnk,function(txt) {
								if(txt.length>0) {
									lgksAlert(txt);
									loadSiteList();
								} else {
									$("#loadingmsg").hide("fast");
								}
							});
						return enteredText;
					},
				});
			$("#sitetable tbody td.devmode").editInPlace({
					show_buttons:false,
					default_text:"",
					field_type: "select",
					save_if_nothing_changed:false,
					select_options:"Published Mode:publish,Blocked Mode:blocked,Restricted/Whitelist Mode:restricted,Maintainance Mode:maintainance,Under Construction Mode:underconstruction",
					callback: function(idOfEditor, enteredText, orinalHTMLContent, settingsParams, callbacks) {
						opts={
								"Published Mode":'publish',
								"Blocked Mode":'blocked',
								"Restricted/Whitelist Mode":'restricted',
								"Maintainance Mode":'maintainance',
								"Under Construction Mode":'underconstruction',
							};
						app=$("#sitetable tbody tr.active td.applink").text();
						b=orinalHTMLContent.toLowerCase();
						s=opts[enteredText];
						$("#loadingmsg").show("fast");
						lnk=cmdLnk+"&action=devmode&app="+app+"&current="+b+"&mode="+s;
						processAJAXQuery(lnk,function(txt) {
								if(txt.length>0) {
									lgksAlert(txt);
									loadSiteList();
								} else {
									$("#loadingmsg").hide("fast");
								}
							});
						return s;
					},
				});
			$("#loadingmsg").hide("fast");
		});
}

function createAppSite() {
	$("#createMode input[type=text],input[type=password],textarea").val("");
	lgksPopup("#createMode",
			{},
			{width:350,height:470,show:"slide",hide:"slide",closeOnEscape:true,resizable:false,
				buttons:{
					Cancel:function() {
						$(this).dialog('close');
					},
					Create:function() {
						lnk=cmdLnk+"&action=createapp";
						postAppForm("#createMode",lnk);
						
						/*if(s=="market") {
								l="index.php?&site=admincp&page=installer&mode=apps";
								parent.openInNewTab("Apps Market", l);
						}*/
					}
				}
			}
		);
}
function cloneAppSite() {
	if($("#sitetable tbody tr.active td.applink").text().length>0) {
		app=$("#sitetable tbody tr.active td.applink").text();
		title=$("#sitetable tbody tr.active td.title").text();
		
		$("#cloneMode input[type=text],input[type=password],textarea").val("");
		$("#cloneMode input[name=baseapp]").val(app);
		$("#cloneMode input[name=baseapptitle]").val(title);
		lgksPopup("#cloneMode",
				{},
				{width:350,height:525,show:"slide",hide:"slide",closeOnEscape:true,resizable:false,
					buttons:{
						Cancel:function() {
							$(this).dialog('close');
						},
						Clone:function() {
							lnk=cmdLnk+"&action=cloneapp";
							postAppForm("#cloneMode",lnk);
						}
					}
				}
			);
	} else {
		lgksAlert("Please select a single site to clone.");
	}
}
function exportAppSite() {
	if($("#sitetable tbody tr.active td.applink").text().length>0) {
		app=$("#sitetable tbody tr.active td.applink").text();
		title=$("#sitetable tbody tr.active td.title").text();
		
		$("#exportMode input[type=text],input[type=password],textarea").val("");
		$("#exportMode input[name=sitecode]").val(app);
		$("#exportMode input[name=sitename]").val(title);
		lgksPopup("#exportMode",
				{},
				{width:350,height:250,show:"slide",hide:"slide",closeOnEscape:true,resizable:false,
					buttons:{
						Cancel:function() {
							$(this).dialog('close');
						},
						Export:function() {
							lnk=cmdLnk+"&action=exportapp";
							postAppForm("#exportMode",lnk);
						}
					}
				}
			);
	} else {
		lgksAlert("Please select a single site to Export.");
	}
}
function manageAppImages() {
	reloadAppImagesList();
	lgksPopup("#appimageManager",
			{},
			{width:600,height:400,show:"slide",hide:"slide",closeOnEscape:true,resizable:false,
				buttons:{
					Reload:function() {
						reloadAppImagesList();
					},
					Close:function() {
						$(this).dialog('close');
					},
				}
			}
		);
}
function checkUploadFile() {
	v=$("#appimageManager form input[name=appimage]").val();
	if(v.length<4) return false;
	if(v.substr(v.length-3)=="zip") {
		return true;
	} else {
		lgksAlert("Only Zip AppImage Files Are Acceppted.");
		$("#appimageManager").find("form").get(0).reset();
		return false;
	}
}
function reloadAppImagesList() {
	l=cmdLnk+"&action=listappimages";
	$("#appimageManager tbody").html("<tr><td class='ajaxloading6' colspan=10></td></tr>");
	$("#appimageManager tbody").load(l, function() {
		});
	$("#appimageManager").find("form").get(0).reset();
}
function collectData(id) {
	s="";
	$(id).find("input, select").each(function() {
			if($(this).attr("name")!=null && $(this).attr("name").length>0) {
				if($(this).attr("name")=="sitecode") {
					s+="&"+$(this).attr("name")+"="+$(this).val().replace(" ","");
				} else {
					s+="&"+$(this).attr("name")+"="+$(this).val();
				}
			}
		});
		
	return s;
}
function deleteAppSite() {
	n=$("#sitetable tbody input.selectrow:checked").length;
	if(n<=0) {
		return;
	}
	msg="<div style='width:400px;height:120px;overflow:auto;'>Are you sure about deleting site/s <br/><br/>";
	toDelete="";
	$("#sitetable tbody input.selectrow:checked").each(function() {
			app=$(this).attr("rel");
			name=$(this).attr("title");
			msg+="&nbsp;&nbsp;&nbsp;* "+name+" ["+app+"]<br/>";
			toDelete+=app+",";
		});
	msg+="<br/><span style='color:maroon;'>This is an irreversible process.</span></div>";
	lgksConfirm(msg,"Deleting "+n+" AppSite/s", function() {
			$("#loadingmsg").show("fast");
			lnk=cmdLnk+"&action=delete&apps="+toDelete;
			processAJAXQuery(lnk,function(txt) {
					if(txt.length>0) lgksAlert(txt);
					loadSiteList();
				});
		});
}
function flushPrivileges() {
	if($("#sitetable tbody tr.active td.applink").text().length>0) {
		app=$("#sitetable tbody tr.active td.applink").text();
		$("#loadingmsg").show("fast");
		lnk=cmdLnk+"&action=flushpermissions&app="+app;
		processAJAXQuery(lnk,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				$("#loadingmsg").hide("fast");
			});
	}
}
function showHelp() {
	jqPopupURL(cmdLnk+"&action=helpme","Help !",null,true,"700","550");
}
function postAppForm(divID,lnk) {
	nm=$(divID+" input[name=sitename]").val();
	nc=$(divID+" input[name=sitecode]").val();
	if(nm.length>0 && nc.length>0) {
		s=collectData(divID);
		if($(divID+" input[name=sitedbhost]").length>0 && $(divID+" input[name=sitedbhost]").val().length<=0) {
			lgksConfirm("No Database Mentioned. Do you want to proceed without Database?","No DB Found?",function() {
					$("#loadingmsg").show("fast");
					$("#sitetable tbody").html("<tr><td colspan=100 class=ajaxloading>Working, Please wait ...</td></tr>");
					processAJAXPostQuery(lnk,s,function(txt) {
								if(txt.length>0) {
									if(txt.indexOf("url:")===0) {
										window.open(txt.substr(4),'Download');
									} else {
										lgksAlert(txt);
									}
								}
								loadSiteList();
							});
					$(divID).dialog('close');
				});
		} else {
			$("#loadingmsg").show("fast");
			$("#sitetable tbody").html("<tr><td colspan=100 class=ajaxloading>Working, Please wait ...</td></tr>");
			processAJAXPostQuery(lnk,s,function(txt) {
						if(txt.length>0) {
							if(txt.indexOf("url:")===0) {
								window.open(txt.substr(4));
							} else {
								lgksAlert(txt);
							}
						}
						loadSiteList();
					});
			$(divID).dialog('close');
		}
	} else {
		lgksAlert("Site Name And Alias(SiteCode) are mandatory Fields.");
	}
}
