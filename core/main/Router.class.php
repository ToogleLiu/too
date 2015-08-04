<?php
/**
* 获取controller, action, 参数
*/
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
		} else {
			$route_str = $_SERVER['REQUEST_URI'];
		}

		$route_arr = explode('/', $route_str);
		$route_arr = Utils::cleanArray($route_arr);

		//解析伪静态url，获取请求参数
		if (strpos($route_str, '.html') !== FALSE) {
			$request_arr = self::getRequestByStaticUrl($route_str);
		} elseif (count($route_arr) > 2) {
			Utils::notFound();
		}

		if (!empty($route_arr)) {
			$this->controller = $route_arr[0];
			$this->action = isset($route_arr[1]) ? $route_arr[1] : self::DEFAULT_ACTION;
		} else {
			$this->controller = self::DEFAULT_CONTROLLER;
			$this->action = self::DEFAULT_ACTION;
		}

		$this->params = array_merge($_GET, $_POST);

		//合并参数
		if (!empty($request_arr)) {
			if (!empty($this->params)) {
				foreach ($request_arr as $key => $value) {
					if (in_array($key, array_keys($this->params))) {
						// Utils::throwException('The key [%s] of request_arr [%s] have been in GET or POST array.', var_export($key, 1), var_export($request_arr, 1));
						Log::fatal('The key ['.var_export($key, 1).'] of request_arr ['.var_export($request_arr, 1).'] have been in GET or POST array.');
						trigger_error('The key [%s] of request_arr [%s] have been in GET or POST array.', var_export($key, 1), var_export($request_arr, 1));
					} else {
						$this->params[$key] = $value;
					}
				}
			} else {
				$this->params = $request_arr;
			}
		}
	}

	public function getController()
	{
		if ($this->controller == null) {
			Log::fatal('Not found the controller: [%s]', var_export($this->controller, 1));
			trigger_error('Not found the controller: [' . var_export($this->controller, 1) . ']', E_USER_ERROR);
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

	/**
	* 从伪静态URL中取得请求参数
	* @param string $url
	* @return array 参数数组
	*/
	public static function getRequestByStaticUrl($url)
	{
		$request_arr = array();
		foreach (StaticUrl::$config as $key => $value) {
			$key = str_replace('/', '\/', $key);
			$key = str_replace('.', '\.', $key);
			if (preg_match('/^'.$key.'$/i', $url, $match_arr)) {
				if (isset($match_arr[0]) && !empty($match_arr[0])) {
					if (is_array($value)) {
						foreach ($value as $kk => $vv) {
							$request_arr[$vv] = $match_arr[$kk+1];
						}
					} elseif (is_string($value) && count($match_arr) == 2) {
						$request_arr[$value] = $match_arr[1];
					}
					break;
				}
			}
		}
		if (empty($request_arr)) {
			Utils::notFound();
		}
		return $request_arr;
	}
}