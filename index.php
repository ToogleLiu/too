<?php

define('HOST_DIR', dirname(__FILE__));

require HOST_DIR . '/core/ini.php';

Initializer::initialize();

$router = Loader::load('router');

Dispatcher::dispatch($router);

?>