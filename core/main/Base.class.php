<?php

class Base
{
	public $controller = '';
	public $smarty = NULL;

	public function __construct($controller)
	{
		$this->controller = $controller;
		$this->smarty = ResourceFactory::getSmartyInstance();
	}

	public function display($tpl, $cache_id = '', $compile_id = '')
	{
		$this->smarty->display($this->controller . '/' . $tpl, $cache_id, $compile_id);
	}

	public function fetch($tpl, $cache_id = '', $compile_id = '')
	{
		$this->smarty->fetch($this->controller . '/' . $tpl, $cache_id, $compile_id);
	}

	public function assign($key, $value)
	{
		$this->smarty->assign($key, $value);
	}

	


}