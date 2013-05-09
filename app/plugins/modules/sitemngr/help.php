<style>
td.okicon,td.notokicon {
	background-position:center center;
}
</style>
<div class='helpInfo'>
<b>Site Manager</b>, helps you to manage your MultiSite Enviroment. Here you can manage/clone/export and even Delete
your sites.
<ul style='list-style:square;'>
	<li><b>Create</b> Create new appSites using this. Please give new name, siteCode(alias) and db configurations
		to create and connect new appSite. Select an AppImage that can be used as the base of the new site.
	</li>
	<li><b>Clone</b> Clone appSite using this. Please give new name, siteCode(alias) and db configurations to clone
		and connect new appSite that is a clone of selected appSite.
	</li>
	<li><b>Export</b> Export appSite using this. You can export an appSite to download or create a base appImage for 
		other sites using this option.
	</li>
	<li><b>AppImages</b> Manage all existing appImages using this window. The system uses these stored appImages which 
		you downloaded from market and uploaded here or created using <b>Export</b> functions during <b>Create</b>.
	</li>
	<li><b>Flush</b> Flush/Clean to reset the all the caches of the selected appSite (including Security caches, Menus,
		Forms etc.)
	</li>
	<li><b>Delete</b> Delete the selected appSite. this is an irrversible process, so please use only if neccessary else 
		just block the site.
	</li>
</ul>
<table cellpadding=2 cellspacing=0 border=0 width=100%>
	<thead align=center>
		<tr>
			<th>Clone Type</th><th colspan=2>File System</th><th colspan=3>Database</th>
		</tr>
		<tr class='border'>
			<th>&nbsp;</th><th>System</th><th>User</th><th>Schema</th><th>System</th><th>User</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Structure Copy/Image</th><td class='okicon'></td><td class='notokicon'></td><td class='okicon'></td><td class='notokicon'></td><td class='notokicon'></td>
		</tr>
		<tr>
			<th>BareBones Copy/Image</th><td class='okicon'></td><td class='notokicon'></td><td class='okicon'></td><td class='okicon'></td><td class='notokicon'></td>
		</tr>
		<tr>
			<th>Exact Copy/Mirror Image</th><td class='okicon'></td><td class='okicon'></td><td class='okicon'></td><td class='okicon'></td><td class='okicon'></td>
		</tr>
	</tbody>
</table>
</div>
