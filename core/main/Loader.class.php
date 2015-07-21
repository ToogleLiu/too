<?php

class Loader
{
	private static $loaded = array();

	private static $valid = array('library','view','model','helper','router','config','hook','cache','db');

	public static function load($class)
	{
		if (!in_array($class, self::$valid)) {
			throw new Exception("Not a valid class. class:[".$class."]");
		}

		if (empty(self::$loaded[$class])) {
			self::$loaded[$class] = new $class();
		}

		return self::$loaded[$class];
	}
}