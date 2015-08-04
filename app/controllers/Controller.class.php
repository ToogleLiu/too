<?php
/**
* 所有controller的父类
*/
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

	/**
	* 获取POST, GET参数 eg: $this->get('id') == $_GET['id']
	*/
	protected function get($field)
	{
		if (isset(self::$params[$field])) {
			return self::$params[$field];
		}
		return NULL;
	}
}