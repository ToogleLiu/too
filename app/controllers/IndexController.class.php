<?php

class IndexController extends Controller
{
	public function indexAction()
	{
		$user = new User();
		$param_arr = array(
			// 'username' => 'too4',
			// 'password' => sha1('222222'),
			// 'sex' => 1,
			'reg_time' => time(),
			// 'mobile' => '15888888888',
			'remark' => "hello,rld.",
		);
		$condition = array(
			// 'remark' => array('%>rld.','like'),
			'uid' => 1058,
			'sex' => 1,
		);

		// $ret = $user->insert($param_arr);

		// $ret = $user->updateByUid($param_arr, 1058);
		// $ret = $user->update($param_arr, $condition);

		// $ret = $user->delete(array('uid'=>1051));

		// $ret = $user->getUserInfoByUid(1058);

		$condition = array(
			// 'remark' => array('%>rld.','like'),
			'uid' => 1058
		);

		$ret = $user->getUserInfo($condition);

		var_dump($ret);
		echo "<pre>";
		// print_r($ret);

		die();
		
		$this->assign('userinfo', $ret);
		$this->display('index.tpl');
	}
}