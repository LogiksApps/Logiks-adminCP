New Site's Alias (SiteCode) (Unique) *<br/>
<input name=sitecode type=text class='required' />
New Site's Name *<br/>
<input name=sitename type=text class='required' />
Select A AppImage
<select name=sitetemplate style='width:94%;height:27px;margin-top:4px;margin-left:10px;'>
		<!--<option value=''>Cancel (Don't Create)</option>
		<option value='market'>Install From AppMarket</option>
		<optgroup label='Template Based'></optgroup>-->
		<?php
			$f=APPROOT.MISC_FOLDER."apptemplates/";
			if(file_exists($f) && is_dir($f)) {
				$f=scandir($f);
				unset($f[0]);unset($f[1]);
				foreach($f as $a) {
					$ext=substr($a,strlen($a)-3);
					if(strtolower($ext)=="zip") {
						$t=str_replace("_"," ",$a);
						$t=str_replace(".zip","",$t);
						$t=str_replace(".tar","",$t);
						$t=str_replace(".gz","",$t);
						$t=ucwords($t);
						echo "<option value='$a'>$t</option>";
					}
				}
			}
		?>
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
