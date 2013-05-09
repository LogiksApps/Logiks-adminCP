<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$webPath=getWebPath(__FILE__);
$rootPath=getRootPath(__FILE__);

_js(array("jquery.filetree","raphael","raphael.g.raphael","raphael.g.pie"));
_css(array("jquery.filetree"));
?>
<style>
html,body {
	overflow:hidden;
}
.page {
	width:100%;
	height:100%;
}
.page #filetree {
	height:100%;
	width:15%;
	float:left;
	padding:3px;
	overflow:auto;
}
.page #usagechart {
	background:black;
	height:98%;
	width:83%;
	float:left;
	margin-top:5px;
	margin-left:9px;
	border:2px solid #aaa;
}
.page #usagechart #dataholder {
	float:right;width:500px;display:none;
}
.page #usagechart #chartholder {
	width:100%;height:100%;background:white;
}
#loadingholder {
	float:left;width:250px;
}
</style>
<div class='page ui-widget-content'>
<div id=filetree></div>
<div id=usagechart>
	<table id=dataholder align=left>
	</table>
	<div id=loadingholder class='ajaxloading'>Loading Usage Graph</div>
	<div id=chartholder>
	</div>
</div>
</div>
<script language=javascript>
$(function() {
	$("button").button();
	$(".tab").tabs();
	$("#loadingholder").hide();
	loadFileTree("#filetree");
});
function loadFileTree(treeid) {
	s=getServiceCMD("diskusage")+"&action=filetree";
	$(treeid).fileTree({root:$(treeid).attr('rel'),script:s,multiFolder:false}, function(file,type) {
		if(type=="folder") {
			createPie(file);
		}
	});
}
function createPieFromGraph(file) {
	createPie(file,function() {

		});
}
function createPie(file,func) {
	$("#loadingholder").show();
	s=getServiceCMD("diskusage")+"&action=diskusetbl&dir="+file;
	$("#usagechart #dataholder").load(s,function() {
			loadChart();
			$("#loadingholder").hide();
			if(func!=null) func();
		});
}
function showFileInfo(file) {
}
function loadChart() {
	var values = [],
		labels = [],
		hrefs  = [];
	var rad=170;
	var w=($("#usagechart").width()-rad)/2;
	var h=($("#usagechart").height())/2;

	$("#dataholder tr").each(function () {
		c1=$(this).children()[0];
		c2=$(this).children()[1];
		c3=$(this).children()[2];
		c4=$(this).children()[3];
		c5=$(this).children()[4];
		values.push(parseInt($(c2).text(), 10));
		labels.push($(c1).text()+" ("+$(c3).text()+" kb)");
		/*if(!($(c5).text()=="file")) {
			hrefs.push("javascript:createPieFromGraph('"+$(c4).text()+"');");
		} else {
			//hrefs.push("javascript:showFileInfo('"+$(c4).text()+"');");
		}*/
	});
	var r = Raphael("chartholder"),
		pie = r.piechart(w, h, rad, values, { legend: labels, legendpos: "east", href: hrefs});

	r.text(w, 70, "Disk Usage Graph").attr({ font: "20px sans-serif" });
	pie.hover(function () {
		this.sector.stop();
		this.sector.scale(1.1, 1.1, this.cx, this.cy);

		if (this.label) {
			this.label[0].stop();
			this.label[0].attr({ r: 10 });
			this.label[1].attr({ "font-weight": 800 });
		}
	}, function () {
		this.sector.animate({ transform: 's1 1 ' + this.cx + ' ' + this.cy }, 500, "bounce");

		if (this.label) {
			this.label[0].animate({ r: 5 }, 500, "bounce");
			this.label[1].attr({ "font-weight": 400 });
		}
	});
}
</script>
