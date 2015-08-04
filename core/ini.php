<?php

define('CONTROLLER_DIR', HOST_DIR . '/app/controllers');
define('PHPLIB_DIR', HOST_DIR . '/core/phplib');

define('PROCESS_START_TIME', microtime(TRUE) * 1000);

//smarty setting
define('TEMPLATE_PATH', HOST_DIR.'/app/views');
define('SMARTY_TEMPLATE_DIR', TEMPLATE_PATH.'/templates');
define('SMARTY_COMPILE_DIR', TEMPLATE_PATH.'/templates_c');
define('SMARTY_CONFIG_DIR', TEMPLATE_PATH.'/config');
define('SMARTY_CACHE_DIR', TEMPLATE_PATH.'/cache');
define('SMARTY_PLUGIN_DIR', TEMPLATE_PATH.'/plugins');
define('SMARTY_LEFT_DELIMITER', '{{');
define('SMARTY_RIGHT_DELIMITER', '}}');

//如果magic_quotes_gpc是开启的，就反转义字符串（去掉已经添加了的'\'），因为使用PDO操作数据库不需要转义字符串
if (get_magic_quotes_gpc()) {

	function stripslashes_deep($value)
	{
		return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/main');

function my_autoload($class){
	$className = strtolower($class);
	if (!strncmp($className, 'smarty', 6)) {
		return;
	}
	include_once $class . '.class.php';
}

spl_autoload_register('my_autoload');

// define('LOG_TYPE', 'NET_LOG');
define('LOG_TYPE', 'LOCAL_LOG');
define('LOG_LEVEL', 0x15);

$GLOBALS['LOG'] = array(
	'appname' 	=> APP_NAME,
	'type' 		=> LOG_TYPE,
	'level' 	=> LOG_LEVEL,
	'path' 		=> HOST_DIR . '/log',
	'filename' 	=> APP_NAME . '.log.' . date('YmdH'),
);

//error setting
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	ob_get_clean();
	restore_error_handler();
	header('HTTP/1.0 500 Internal Server Error');
	include SMARTY_TEMPLATE_DIR . '/error/500.html';
	exit;
}

if (defined('IS_DEBUG') && IS_DEBUG == TRUE) {
	ini_set('display_errors', 1);
} else {
	ini_set('display_errors', 0);
	set_error_handler('myErrorHandler');
}

function myExceptionHandler(Exception $ex)
{
	restore_exception_handler();
	Log::fatal($ex->getMessage());
	trigger_error($ex->getMessage());
}

set_exception_handler('myExceptionHandler');
