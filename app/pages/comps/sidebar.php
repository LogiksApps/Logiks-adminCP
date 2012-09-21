<?php
$SHOW_EMPTY_HOLDERS=getSiteSettings("SHOW_EMPTY_HOLDERS","false","SIDEBAR");
?>
<div id=infodisplay>
	<div id=infodata class="framed" align=center>
		<img src='<?=loadMedia("logos/logiks.100.png")?>' width=80px height=70px/>
	</div>
</div>
<ul>
	<li><a href="#" onclick="$tabs.tabs('select',0);"><img src='<?=loadMedia("icons/sidebar/home.png")?>' />Dashboard</a></li>
	<?php
		loadModule("navigator",array("site"=>"admincp","dbtable"=>_dbtable("admin_links",true),"dbLink"=>getSysDBLink(),
			"menuid"=>getConfig("DEFAULT_NAVIGATION"),"showEmptyHolders"=>"$SHOW_EMPTY_HOLDERS"));
	?>
</ul>
<script language=javascript>
$("#sidebar li ul a").hover(function() {
		$(this).animate({
					paddingLeft:'35px',
				}, 300 );
	},function() {
		$(this).animate({
					paddingLeft:'20px',
				}, 300 );
	});
$(function() {
	loadSidebar("#sidebar");
});
</script>
