<?php
/**
* 获取smarty对象，并封装smarty常用方法
*
*/
class Base
{
	public $controller = '';	//controller name
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