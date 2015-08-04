<?php
/**
* 初始化，设置文件包含路径
*/
class Initializer
{
	public static function initialize()
	{
		// set_include_path(get_include_path() . PATH_SEPARATOR . HOST_DIR . '/core/phplib/smarty');
		
		$include_path  = PATH_SEPARATOR . HOST_DIR . '/core/config';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/core/phplib';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/common';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/controllers';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/models';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/app/views';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/core/phplib/smarty';
		$include_path .= PATH_SEPARATOR . HOST_DIR . '/core/phplib/log';

		ini_set('include_path', ini_get('include_path') . $include_path);
	}
}