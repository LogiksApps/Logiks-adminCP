<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	$dmF=ROOT.CFG_FOLDER."security/domainmap.json";
	if(!file_exists($dmF)) {
		DomainMap::createDefaultDomainMap();
	}
	$dmArr=json_decode(file_get_contents($dmF),true);
	if($dmArr==null) {
		$dmArr=array();
	}
	if($_REQUEST["action"]=="domainmaptable") {
		foreach($dmArr as $a=>$b) {
			echo "<tr rel='$a'>";
			//echo "<td align=center><input name=rowselector type=radio /></td>";
			echo "<td align=left col=host>{$a}</td>";
			echo "<td align=left col=appsite>{$b['appsite']}</td>";
			if($b['active'])
				echo "<td align=center><input class='active' type=checkbox checked=true /></td>";
			else
				echo "<td align=center><input class='active' type=checkbox /></td>";
			echo "<td align=center>{$b['doc']}</td>";
			echo "<td align=center>{$b['doe']}</td>";
			echo "<td align=right><span class='btnicon editicon'></span><span class='btnicon deleteicon'></span></td>";
			echo "</tr>";
		}
	} elseif($_REQUEST["action"]=="setactive" && isset($_POST['h']) && isset($_POST['s'])) {
		$h=$_POST['h'];
		if(isset($dmArr[$h])) {
			$b=$dmArr[$h];
			$b['active']=($_POST['s']=="true")?true:false;
			$dmArr[$h]=$b;
			$json=json_encode($dmArr);
			file_put_contents($dmF,$json);
		} else {
			echo "Error Enabling/Disabling Domain";
		}
	} elseif($_REQUEST["action"]=="delete" && isset($_POST['h'])) {
		$h=$_POST['h'];
		if(isset($dmArr[$h])) {
			unset($dmArr[$h]);
			$json=json_encode($dmArr);
			file_put_contents($dmF,$json);
		} else {
			echo "Error Deleting Domain, Doesn't Exist";
		}
	} elseif($_REQUEST["action"]=="savemap" && isset($_POST['h']) && isset($_POST['a'])) {
		$h=$_POST['h'];
		$a=$_POST['a'];
		if(isset($dmArr[$h])) {
			$b=$dmArr[$h];
			$b['appsite']=$a;
			$b['doe']=date("Y-m-d");
			$dmArr[$h]=$b;
			$json=json_encode($dmArr);
			file_put_contents($dmF,$json);
		} else {
			$b=array("appsite"=>$a,"nodal"=>"","active"=>true,"doc"=>date("Y-m-d"),"doe"=>date("Y-m-d"));
			$dmArr[$h]=$b;
			$json=json_encode($dmArr);
			file_put_contents($dmF,$json);
		}
	}

}
?>
