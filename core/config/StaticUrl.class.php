<?php
/**
* url伪静态规则
*
*/
class StaticUrl
{
	public static $config = array(
		'/index/index/u/(\d+).html' => 'uid',
		'/user/login/u(\d+)/p(\d+)/t(\d+).html' => array('uid','pid','tid'),
	);

}