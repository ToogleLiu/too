<?php

class Initializer
{
	public static function initialize()
	{
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/main/cache');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/helpers');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/libraries');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/phplib');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/app/common');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/app/controllers');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/app/models');
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/app/views');

		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/phplib/smarty');
	
		$include_path  = PATH_SEPARATOR . HOST_DIR . '/core/phplib';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/common';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/controllers';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/models';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/views';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/core/phplib/smarty';

		ini_set('include_path', ini_get('include_path') . $include_path);
	}
}