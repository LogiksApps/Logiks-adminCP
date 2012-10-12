<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	
	if($_REQUEST["action"]=="ctrllist") {
		$sql="select * from "._dbTable('admin_links',true)." where (link is not null AND link<>'' AND link<>'#') AND site='cms' and menuid='cms_menu' order by id";
		$result=_dbQuery($sql,true);
		if($result) {
			$data=_dbData($result);
			_dbFree($result);
			$menuControls=array();
			foreach($data as $a=>$b) {
				$menuControls[$b['id']]=$b;
			}
			$data=array();
			foreach($menuControls as $a=>$b) {
				$mgroup="";
				
				if(isset($menuControls[$b['menugroup']]) && isset($menuControls[$b['menugroup']]['title'])) {
					$mgroup=$menuControls[$b['menugroup']]['title'];
				}
				
				$s="<tr rel='{$a}'>";
				
				//$s.="<td class='iconpath' val='{$b['iconpath']}'>".loadMedia($b['iconpath'])."</td>";
				
				//$s.="<td class='menugroup' val='{$b['menugroup']}'>{$mgroup}</td>";
				$s.="<td class='title'>{$b['title']}</td>";
				$s.="<td class='category'>{$b['category']}</td>";
				//$s.="<td class='link'>{$b['link']}</td>";
				
				$s.="<td class='minibutton privilege usericon' val='{$b['privilege']}'></td>";
				
				if($b['blocked']=="false")
					$s.="<td class='blocked' val='{$b['blocked']}'><input type=checkbox name=blocked checked /></td>";
				else
					$s.="<td class='blocked' val='{$b['blocked']}'><input type=checkbox name=blocked /></td>";
					
				/*if($b['onmenu']=="true")
					$s.="<td class='onmenu' val='{$b['onmenu']}'><input type=checkbox name=onmenu checked /></td>";
				else
					$s.="<td class='onmenu' val='{$b['onmenu']}'><input type=checkbox name=onmenu /></td>";
				*/
				$s.="</tr>";
				
				echo $s;
			}
		} else {
			echo "<tr><td colspan=20 class=''>No Controls Found</td></tr>";
		}
	} 
	elseif($_REQUEST["action"]=="toggle" && isset($_REQUEST['type'])) {
		$sql="";
		if($_REQUEST['type']=="blocked") {
			$sql="UPDATE "._dbTable('admin_links',true)." SET blocked='{$_REQUEST['v']}' WHERE menuid='cms_menu' and id={$_REQUEST['rel']}";
		} elseif($_REQUEST['type']=="onmenu") {
			$sql="UPDATE "._dbTable('admin_links',true)." SET onmenu='{$_REQUEST['v']}' WHERE menuid='cms_menu' and id={$_REQUEST['rel']}";
		} else {
			exit("<h3>Wrong Action</h3>");
		}
		//echo $sql;
		if(strlen($sql)>0) {
			_dbQuery($sql,true);
			if(_db(true)->affected_rows()<=0) {
				echo "Failed To Update Control.Try Again!";
			}
		} else {
			exit("<h3>Wrong Action</h3>");
		}
	}
	elseif($_REQUEST["action"]=="privilege" && isset($_REQUEST['rel'])) {
		$sql="UPDATE "._dbTable('admin_links',true)." SET privilege='{$_REQUEST['v']}' WHERE menuid='cms_menu' and id={$_REQUEST['rel']}";
		_dbQuery($sql,true);
		if(_db(true)->affected_rows()<=0) {
			echo "Failed To Update Control.Try Again!";
		}
	}
	
	
	
	
	
	elseif($_REQUEST["action"]=="activitieslist") {
		
		
	}
}
?>
