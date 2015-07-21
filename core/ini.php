<?php

define('CONTROLLER_DIR', HOST_DIR . '/app/controllers');
define('PHPLIB_DIR', HOST_DIR . '/core/phplib');

//smarty setting
define('TEMPLATE_PATH', HOST_DIR.'/app/views');
define('SMARTY_TEMPLATE_DIR', TEMPLATE_PATH.'/templates');
define('SMARTY_COMPILE_DIR', TEMPLATE_PATH.'/templates_c');
define('SMARTY_CONFIG_DIR', TEMPLATE_PATH.'/config');
define('SMARTY_CACHE_DIR', TEMPLATE_PATH.'/cache');
define('SMARTY_PLUGIN_DIR', TEMPLATE_PATH.'/plugins');
define('SMARTY_LEFT_DELIMITER', '{{');
define('SMARTY_RIGHT_DELIMITER', '}}');

set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/main');

function my_autoload($class){
	$className = strtolower($class);
	if (!strncmp($className, 'smarty', 6)) {
		return;
	}
	include_once $class . '.class.php';
}

spl_autoload_register('my_autoload');


ini_set('display_errors', 1);

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);