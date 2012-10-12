<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);
?>
<div data-role='page' data-theme='<?=$theme_page?>'>
	<div data-role='header' data-theme='<?=$theme_header?>' data-nobackbtn="false">
		<img src='media/icons/<?=$device?>.png' width=25px height=25px alt='' style='float:left;margin-right:5px;margin-top:5px;' />
		<h1>Home Page</h1>
		<a href="api/logout.php" data-role='button' data-theme='<?=$theme_button?>' data-icon="delete" rel="external" class="ui-btn-right">Logout</a>
	</div>
	<div data-role='content' data-inset="true" style='padding-top:0px;padding-bottom:0px;' align=center>
		<div  align=justify>
			<h1>Sorry, Mobile Friendly Version Is Still Under Construction Yet.</h1>
			<h4>Check Back Later</h4>
		</div>
	</div>
</div>
