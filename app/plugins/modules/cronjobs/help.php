<style>
.helpInfo {
	width:100%;text-align:justify;font-size:15px;font-family:verdana;
}
.helpInfo .infoIcon {
	padding-left:24px;width:100%;
	background-position:left center;
}
.helpInfo table {
	border:1px dashed #aaa;
	padding:3px;
}
.helpInfo thead tr.border th {
	 border-bottom:2px solid #aaa !important;
}
.helpInfo ul ul li {
	padding-bottom:5px;
}
</style>
<div class='helpInfo forms'>

<ul>
	<li>What is Scheduller(PCron) Jobs ?</li>
	<ul><li>
		Scheduller(PCron) takes care of running periodical jobs or schedulled tasks like indexing, backups, etc...
		Logiks Scheduller uses <b>PCron Engine</b> to run its tasks which actually uses website visitor to trigger
		actions. The actual "cron job" is a time-triggered action that is usually (and most efficiently) performed 
		by your website's hosting server but we understand that not all have access to Hosting Server's Cron Controls 
		or they may be a bit complicated. So <b>PCron Engine</b> uses Site Visitor's to trigger the actions. Though 
		this may not be as accurate. For more accuracy please confgiure your Hosting Server's Cron Controls.
	</li></ul>
	<li>What is A Job ?</li>
	<ul><li>All Jobs are recurrent in nature. They are run at the given schedulle regullarly.</li></ul>
	<li>What is A Task ?</li>
	<ul><li>All Tasks are single time in nature. They are run only One-Time On the Given Schedulle</li></ul>	
	<li>How To Configure Hosting Server's Cron Controls ?</li>
	<ul><li>
		This enables you to configure <b>PCron Engine</b> as remote service to be visited by the Hosting Server's Cron. 
		The <b>PCron Engine</b> can be run using the link <br/>
		<div align=center style="padding:2px;"><b><a href='<?=SiteLocation?>/pcron.php?pcron_key=<?=getConfig("PCRON_KEY")?>' target=_blank><?=SiteLocation?>/pcron.php?pcron_key=<?=getConfig("PCRON_KEY")?></a></b></div>
	</li></ul>
</ul>
</div>
