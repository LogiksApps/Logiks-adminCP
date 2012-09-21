To Clone Site From *<br/>
<input name=baseapptitle type=text class='required' readonly />
<input name=baseapp type=hidden class='required' readonly />
<hr/>
New Site's Alias (SiteCode) (Unique) *<br/>
<input name=sitecode type=text class='required' />
New Site's Name *<br/>
<input name=sitename type=text class='required' />
Clone Type
<select name=clonetype style='width:94%;height:27px;margin-top:4px;margin-left:10px;'>
		<option value='structure'>Structure Copy</option>
		<option value='barebone'>BareBones Copy</option>
		<option value='exact'>Exact Copy</option>
</select>
<hr/>
DBMS Driver<br/>
<select name=sitedbdriver style='width:94%;height:27px;margin-top:4px;margin-left:10px;'>
	<option value='mysql'>MySQL Driver</option>
</select>
DB Host<br/>
<input name=sitedbhost type=text />
DB Database<br/>
<input name=sitedbname type=text />
DB User<br/>
<input name=sitedbuser type=text />
DB Password<br/>
<input name=sitedbpwd type=password />
<h5 style='margin:0px;padding:0px;'>Leaving Blank DB Fields,will copy the DB Settings Of Parent AppSite.</h5>
