<?php
/**
* 加载router类
*/
class Loader
{
	private static $loaded = array();

	private static $valid = array('router');

	public static function load($class)
	{
		if (!in_array($class, self::$valid)) {
			Log::fatal('Not a valid class. class:[%s]', var_export($class, 1));
			trigger_error('Not a valid class. class:[' . var_export($class, 1) . ']', E_USER_ERROR);
		}

		if (empty(self::$loaded[$class])) {
			self::$loaded[$class] = new $class();
		}

		return self::$loaded[$class];
	}
}