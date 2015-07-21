<?php

class Router
{
	const DEFAULT_CONTROLLER 	= 'index';		//默认controller
	const DEFAULT_ACTION 		= 'index';		//默认action

	private $controller;
	private $action;
	private $params;

	public function __construct()
	{

		if (strpos($_SERVER['REQUEST_URI'], '.php') !== FALSE) {
			header('Location:/');
			exit();
		}

		if (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) {
			list($route_str) = explode('?', $_SERVER['REQUEST_URI']);
			$route_arr = explode('/', $route_str);
		} else {
			$route_arr = explode('/', $_SERVER['REQUEST_URI']);
		}

		$route_arr = Utils::cleanArray($route_arr);

		if (!empty($route_arr)) {
			$this->controller = $route_arr[0];
			$this->action = $route_arr[1];
		} else {
			$this->controller = self::DEFAULT_CONTROLLER;
			$this->action = self::DEFAULT_ACTION;
		}

		$this->params = array_merge($_GET, $_POST);
	}

	public function getController()
	{
		if ($this->controller == null) {
			throw new Exception('Not found the controller: [' . var_export($this->controller, 1) . ']');
		}
		return $this->controller;
	}

	public function getAction()
	{
		if (empty($this->action)) {
			$this->action = 'main';
		}
		return $this->action;
	}

	public function getParams()
	{
		return $this->params;
	}
}