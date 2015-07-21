<?php

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
			throw new Exception('Controller not found. className:['.$className.']');
		}
	}
}