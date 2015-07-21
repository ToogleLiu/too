<?php

class Controller extends Base
{
	protected static $params = array();

	public function __construct($params, $controller)
	{
		self::$params = $params;
		parent::__construct($controller);
	}

	public function __call($method, $argv)
	{
		throw new Exception('Not found the method: ['.var_export($method, 1).']');
	}

	protected function get($field)
	{
		if (isset(self::$params[$field])) {
			return self::$params[$field];
		}
		return NULL;
	}
}