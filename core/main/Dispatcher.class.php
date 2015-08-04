<?php
/**
* 实例化controller类，执行action方法
*
*/
class Dispatcher
{
	public static function dispatch(Router $router)
	{
		// ob_start();

		$className = ucfirst($router->getController()) . 'Controller';
		$actionName = $router->getAction() . 'Action';
		$controllerFile = CONTROLLER_DIR . '/' . $className . '.class.php';

		if (file_exists($controllerFile)) {
			include_once $controllerFile;
			$app = new $className($router->getParams(), $router->getController());
			$app->$actionName();
			// $output = ob_get_clean();
			// echo $output;
		} else {
			// throw new Exception('Controller not found. className:['.$className.']');
			Log::fatal('Controller not found. className:[%s]', var_export($className,1));
			trigger_error('Controller not found. className:[' . var_export($className,1) . ']', E_USER_ERROR);
		}
	}
}