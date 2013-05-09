<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
user_admin_check(true);

if(!isset($_REQUEST["mode"])) {$_REQUEST["mode"]="php";}

if(isset($_REQUEST["code"])) {
	$queryCode="<span style='color:red;width:100px'>QUERY </span>";
	$serverCode="<span style='color:blue;width:100px'>RESPONSE </span>";
	$showQuery="true";
	$encoded=false;
	$cacheClear=true;
	$CACHE_DIR="";
	
	if(isset($_REQUEST["encoded"])) $encoded=$_POST["encoded"];
	if(isset($_REQUEST["cacheClear"])) $cacheClear=$_REQUEST["cacheClear"]; else $_REQUEST["cacheClear"]=$cacheClear;
	if(isset($_REQUEST["showQuery"])) $showQuery=$_REQUEST["showQuery"];	
	$CACHE_DIR=getCacheDir();
	if(!file_exists($CACHE_DIR)) {
		echo "CACHE_DIR Could Not Be Created.";
		exit();
	}
	
	createHookups();
	$filename=md5((rand() * time()) . session_id()) . ".php";		
	$code="";
	if($encoded) {
		$code=base64_decode($_REQUEST['code']);
	} else {
		$code=$_REQUEST['code'];
	}
	$code=stripslashes($code);
	
	if($_REQUEST["mode"]=="php") {
		$s ="<?php \n";
		$s.=$code;
		$s.="\n?>";
		$f1=$CACHE_DIR . $filename;
		file_put_contents($f1,$s);
		$_SESSION['curFile']=$f1;
		
		if(strlen($code)>65) {
			$code=substr($code,0,65)." ...";
		}
		
		ob_start();
		if($showQuery=='true') {
			echo $queryCode . $code . "<br/>";
		}
		echo $serverCode;
		if(file_exists($f1)) {
			include $f1;
		} else {
			echo "Failed To Transact Cache. No Results";
		}
		ob_flush();
		ob_clean();
	} elseif($_REQUEST["mode"]=="bash") {
		ob_start();
		if($showQuery=='true') {
			echo "$ " . $code . "<br/>";
		}
		//echo $serverCode."<br/>";
		
		$command=$code;
		$_SESSION['output'] = '';
		
		$io = array();
		$p = proc_open($command,
					   array(1 => array('pipe', 'w'),
							 2 => array('pipe', 'w')),
					   $io);

		/* Read output sent to stdout. */
		while (!feof($io[1])) {
			$line=fgets($io[1]);
			if (function_exists('mb_convert_encoding')) {
				/* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
				$line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
			}
			$_SESSION['output'] .= htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
		}
		/* Read output sent to stderr. */
		while (!feof($io[2])) {
			$line=fgets($io[2]);
			if (function_exists('mb_convert_encoding')) {
				/* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
				$line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
			}
			$_SESSION['output'] .= htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
		}
		
		fclose($io[1]);
		fclose($io[2]);
		proc_close($p);
		echo $_SESSION['output'];
		
		ob_flush();
		ob_clean();
	} else {
		echo "<span style='color:red'>ShellMode Not Supported</span>";
	}	
} elseif(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="clear-trash") {
		$CACHE_DIR=getCacheDir();
		$fs=scandir($CACHE_DIR);
		unset($fs[0]);
		unset($fs[1]);
		foreach($fs as $a=>$b) {
			unlink($CACHE_DIR."/$b");			
		}
		$fs=scandir($CACHE_DIR);
		$fs=sizeOf($fs)-2;
		if($fs>0) {
			echo "Error Clearing Trash. It Still Contains $fs Files. <br/>Try Later.";
		} else {
			echo "Trash Cleaned.";
		}
	} elseif($_REQUEST["action"]=="view-trash") {
		$CACHE_DIR=getCacheDir();
		$fs=scandir($CACHE_DIR);
		$fs=scandir($CACHE_DIR);
		$fs=sizeOf($fs)-2;
		echo "Trash Contains $fs Files. <br/>Try Clearing Trash For Maintaining Efficiency.";
	} elseif($_REQUEST["action"]=="helpme") {
		loadModuleLib("konsole","help");
	} 
}
else {
	echo "Command Code Not Found";
}
function createHookups() {
	//set error handler
	set_error_handler("konsoleErrorHandler");
	set_exception_handler("konsoleExceptionHandler");
	register_shutdown_function("konsoleShutdownHandler");
	
	ini_set("display_errors","stdout");
	ini_set("error_reporting",E_ALL);
	ini_set("display_startup_errors",true);
	//ini_set("report_memleaks",true);
	//ini_set("log_errors",true);
	//ini_set("track_errors",true);
}
function konsoleErrorHandler($errno, $errMsg,$file, $line, $errText) {
	//echo "<div style='display:none'>";
	$trackBackFile="In File $file On Line $line";	
	
	echo "<b style='color:red'>Error:</b> [$errno] $errMsg";
	
	$btcid="BTC".md5(rand() * time());
	echo "<div id=$btcid class=trackBackFile title='Error Tracer'><pre>";
	var_dump(debug_backtrace());
	echo "</pre></div> <button class='errBtn' title='Code Tracer' onclick='showErrorPopup(\"#$btcid\")'>? Trace</button>";	
	
	/*$tcbid="TCB".md5(rand() * time());
	echo "<div id=$tcbid class=trackBackFile title='Error File'>$trackBackFile</div> 
			<button class='errBtn' title='Error File' onclick='showErrorPopup(\"#$tcbid\");'>+ File</button>";*/	
}
function konsoleExceptionHandler($exp) {	
	echo "Uncaught Exception: ". $exp->getMessage(), "\n";
}
function konsoleShutdownHandler() {
	if($_REQUEST["mode"]=="php") {
		$kdbid="KODE".md5(rand() * time());
		echo "<div id=$kdbid class=trackBackFile title='Source Code'>";
		highlight_file($_SESSION['curFile']);
		echo "</div><button class='errBtn' title='Error File' onclick='showErrorPopup(\"#$kdbid\");'>Source</button>";
		
		$btcid="LOG".md5(rand() * time());
		echo "<div id=$btcid class=trackBackFile title='Error Tracer'><pre>";
		var_dump(error_get_last());
		echo "</pre></div> <button class='errBtn' title='Code Tracer' onclick='showErrorPopup(\"#$btcid\")'>Log</button>";
	}
	
	if($_REQUEST["cacheClear"]==1 || $_REQUEST["cacheClear"]=="true") {
		if(isset($_SESSION['curFile']) && file_exists($_SESSION['curFile'])) {
			unlink($_SESSION['curFile']);
			unset($_SESSION['curFile']);			
		}
	}
}
function getCacheDir() {
	$CACHE_DIR="";
	if(defined("APPS_CACHE_FOLDER")) {
		$CACHE_DIR=APPROOT.APPS_CACHE_FOLDER."konsole/";
	} else {
		$CACHE_DIR=ROOT.CACHE_FOLDER."konsole/";
	}
	if(!file_exists($CACHE_DIR)) {
		mkdir($CACHE_DIR,0777,true);
		chmod($CACHE_DIR,0777);
	}
	return $CACHE_DIR;
}
?>
