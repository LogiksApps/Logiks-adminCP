<form action='<?=SiteLocation?>services/?scmd=sitemngr&action=uploadappimage' method="post" enctype="multipart/form-data" target=importtargetframe onsubmit='return checkUploadFile();'>
	<b>Upload AppImage To Install</b><br/>
	<input name=appimage type=file style='width:450px;height:24px;border:0px;' />
	<button type='submit'>Upload</button>
</form>
<iframe style='display:none' name=importtargetframe id=importtargetframe></iframe>
<hr/><br/>
<div style='width:100%;height:200px;overflow:auto;'>
	<table width=100% cellpadding=0 cellspacing=0 border=1>
		<thead class='ui-widget-header'>
			<th width=50px>--</th>
			<th>AppImage</th>
			<th>Size</th>
			<th width=75px>Created</th>
			<th width=60px>&nbsp;</th>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
