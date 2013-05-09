<?php
$cfgGroups["general"]=array(
	"Site Defaults"=>array("DEFAULT_SITE","TITLE_FORMAT", "LANDING_PAGE","DEFAULT_THEME"),
	"User Interface"=>array("LOADER_LAYOUT", "LOCK_CONTEXTMENU", "LOCK_SELECTION", "LOCK_MOUSEDRAG", ),
	"Date/Time"=>array("DIRECTORY_SEPARATOR", "DATE_SEPARATOR", "TIME_SEPARATOR", "DATE_FORMAT","TIME_FORMAT","TIMESTAMP_FORMAT","DATE_YEAR_RANGE"),
	);
$cfgGroups["system"]=array(
	"Regional And Languages"=>array("DEFAULT_COUNTRY", "DEFAULT_LOCALE", "DEFAULT_TIMEZONE", "DATE_FORMAT", "TIME_FORMAT", "DATE_YEAR_RANGE"),
	"PHP Settings"=>array("error_reporting", "file_uploads", "max_file_uploads", "max_execution_time","memory_limit"),
	"Templating System"=>array("TEMPLATE_CACHE", "SMARTY_COMPILE_CHECK", "SMARTY_PHP_ALLOW", "TEMPLATE_EXPIRY", "TEMPLATE_CACHE_ON_DISPLAY",),
	);
$cfgGroups["logging"]=array(
	"Error/Exceptions/Debug"=>array("ERROR_MESSAGE_FORMAT", "ERROR_DISP_TYPE", "ERROR_TRACE", "ERROR_VIEWER", "EXCEPTION_HANDLER", "ERROR_HANDLER",),
	"Logging"=>array("LOG_FORMAT", "LOG_DATE", "LOG_TIME", "LOG_HANDLERS","LOG_VISITOR_TOTAL_INFO","LOG_VISITORS_PAGE",
		"LOG_EVENTS_VISITOR","LOG_EVENTS_ERROR","LOG_EVENTS_SYSTEM","LOG_EVENTS_ACTIVITY","LOG_EVENTS_REQUESTS","LOG_EVENTS_SEARCH",
		"LOG_EVENTS_SQL","LOG_EVENTS_SQL_SELECT","LOG_USER_AGENTS","LOG_NO_EVENTS_ON_ERROR"),
	);
$cfgGroups["development"]=array(
		"Debug/Error"=>array("MASTER_DEBUG_MODE",),
		"CSS/JS"=>array("CSS_DISP_TYPE", "JS_DISP_TYPE","CSS_CACHEBUSTER", "JS_CACHEBUSTER",),
		"Cache"=>array("FULLPAGE_CACHE_ENABLED","FULLPAGE_CACHE_PERIOD","FULLPAGE_CACHE_NOCACHE"),
		"Advanced"=>array("MAX_UPLOAD_FILE_SIZE","DATABUS_TIMEOUT","CACHE_EXPIRY","ERROR_REDIRECTION_LEVEL","GENERATED_PERMALINK_STYLE","FULL_MEDIA_PATH"),
	);
$cfgGroups["others"]=array(
	"Others"=>array("IMAGE_STORAGE_FORMAT","THUMBNAIL_SIZE"),
	);
$cfgGroupsFor="system.cfg";
$cfgGroupsDefaulters=array("mobility.cfg","login.cfg");
?>
