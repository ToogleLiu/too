<?php
/**
* 用户表
* 操作用户数据类
*/
class User
{
	const TABLE_NAME = 'user';	//表名

	//表字段及其规则
	public static $columnRules = array(
		'uid' 		=> array(
							'type' 		=> 'int',
							'min_value' => 1,
							'max_value' => NULL,
							'comment' 	=> 'user id',
						),
		'username' 	=> array(
							'type' 		=> 'string',
							'min_len' 	=> 2,
							'max_len' 	=> 20,
							'comment' 	=> 'user name',
						),
		'password' 	=> array(
							'type' 		=> 'string',
							'min_len' 	=> 40,
							'max_len' 	=> 40,
							'comment' 	=> 'user password',
						),
		'sex' 		=> array(
							'type' 		=> 'int',
							'min_value' => 0,
							'max_value' => 1,
							'comment' 	=> 'user gender',
						),
		'reg_time' 	=> array(
							'type' 		=> 'int',
							'min_value' => NULL,
							'max_value' => NULL,
							'comment' 	=> 'user register time',
						),
		'mobile' 	=> array(
							'type' 		=> 'string',
							'min_len' 	=> 11,
							'max_len' 	=> 11,
							'comment' 	=> 'user mobile',
						),
		'remark' 	=> array(
							'type' 		=> 'string',
							'min_len' 	=> NULL,
							'max_len' 	=> NULL,
							'comment' 	=> 'user name',
						),
	);

	protected $mysql = NULL;

	public function __construct()
	{
		$this->mysql = new DbBase();
		$this->mysql->setTableName(self::TABLE_NAME);
		$this->mysql->setColumnRules(self::$columnRules);
	}

	/**
	* 获取字段数组
	*/
	public static function getColumn()
	{
		return array_keys(self::$columnRules);
	}

	/**
	* 根据uid查询用户信息
	* @param int $uid 用户id
	* @return 用户信息数组
	*/
	public function getUserInfoByUid($uid)
	{
		Utils::checkCondition(array('uid' => $uid), self::$columnRules);	//验证参数
		return $this->mysql->find(array(), array('uid' => $uid));
	}

	/**
	* 根据条件获取用户信息
	* @param array $condition  	条件数组 eg: array('sex' => 1, 'reg_time' => array(143233323,'<='))
	* @param array $order 		排序 eg: array('id','desc') = order by id desc
	* @param array $offset 		范围 eg: array(10,20) = limit 10,20
	* @return 用户信息数组
	*/
	public function getUserInfo($condition = array(), $order = array(), $offset = array())
	{
		if (!is_array($condition)) {
			return false;
		}
		Utils::checkCondition($condition, self::$columnRules);
		return $this->mysql->findAll(array(),$condition,$order,$offset);
	}

	/**
	* 插入数据
	* @param array $param_arr 要插入的数据 eg: array('username' => 'aaa',...)
	* @return 成功返回新增的id，失败返回false
	*/
	public function insert($param_arr)
	{
		if (!is_array($param_arr) || empty($param_arr)) {
			return false;
		}
		Utils::checkColumnData($param_arr, self::$columnRules);

		return $this->mysql->insert($param_arr);
	}

	/**
	* 根据UID更新数据
	* @param array  $param_arr  要更新的数据 eg: array('username'=>'root','mobile'=>'13432343443')
	* @param int 	$uid 		用户uid
	* @return 返回受影响的行数，失败则返回false
	*
	*/
	public function updateByUid($param_arr, $uid)
	{
		return $this->update($param_arr, array('uid'=>$uid));
	}

	/**
	* 更新数据
	* @param array  $param_arr  要更新的数据 eg: array('username'=>'root','mobile'=>'13432343443')
	* @param array  $condition  条件 eg: array('id' => 33)
	* @return 返回受影响的行数，失败则返回false
	*
	*/
	public function update($param_arr, $condition)
	{
		if (!is_array($param_arr) || empty($param_arr) || !is_array($condition) || empty($condition)) {
			return false;
		}
		Utils::checkColumnData($param_arr, self::$columnRules);
		Utils::checkCondition($condition, self::$columnRules);
		return $this->mysql->update($param_arr, $condition);
	}

	/**
	* 删除数据
	* @param $condition 条件 eg: array('id' => 33)  (验证了不能为空，以免删除整张表)
	* @return  返回受影响的行数，失败则返回false
	*/
	public function delete($condition)
	{
		if (!is_array($condition) || empty($condition)) {
			return false;
		}
		Utils::checkCondition($condition, self::$columnRules);
		return $this->mysql->delete($condition);
	}
}