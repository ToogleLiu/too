<?php

class IndexController extends Controller
{
	public function indexAction()
	{
		$uid = $this->get('uid');

		$user = new User();

		$condition = array(
			// 'sex' => 0,
			// 'remark' => array('', '!='),
			'uid' => 1052
		);

		$ret = $user->getUserInfoByUid($uid);
		// $ret = $user->getUserInfo($condition);

		$param = array(
			// 'username' => 't_aa_1',
			// 'password' => sha1('111111'),
			// 'sex' => 1,
			// 'reg_time' => time(),
			// 'mobile' => '18882384823',
			'remark' => '',
		);
		// $ret = $user->insert($param);

		// $ret = $user->update($param, $condition);

		$uid = 1053;

		// $ret = $user->updateByUid($param, $uid);

		// $ret = $user->delete($condition);

		echo "<pre>";
		// print_r($ret);
		var_dump($ret);
		die();
		
		$this->assign('userinfo', $ret);
		$this->display('index.tpl');
	}
}