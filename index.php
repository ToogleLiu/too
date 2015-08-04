<?php

define('IS_DEBUG', TRUE);	//是否打印错误信息。生产机上要设置为FALSE
// define('IS_DEBUG', FALSE);
define('APP_NAME', 'too');	//应用名称
define('HOST_DIR', dirname(__FILE__));	//根目录

require HOST_DIR . '/core/ini.php';

Initializer::initialize();

$router = Loader::load('router');

Dispatcher::dispatch($router);

?>