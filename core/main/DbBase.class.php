<?php
/**
* 数据库操作通用类
*
*/
class DbBase
{

	public $conn = NULL;

	public static $table_name = '';		//表名

	public static $columnRules = array();	//字段规则

	public function __construct()
	{
		$this->conn = DbConnect::getInstance()->connectDb();
	}

	public function setTableName($table_name)
	{
		self::$table_name = $table_name;
	}

	public function setColumnRules($columnRules)
	{
		self::$columnRules = $columnRules;
	}

	/**
	* 获取字段数组
	*/
	public static function getColumn()
	{
		return array_keys(self::$columnRules);
	}

	//验证表名
	public function checkTableName()
	{
		if (!is_string(self::$table_name) || empty(self::$table_name)) {
			Utils::throwException('The table name is invalid:[%s]', var_export($table_name,1));
		}
	}

	/**
	* 查询数据，默认是返回一条数据
	* @param array  $column_arr 	要查询的列，为空则查询所有列 eg: array('id','username','count(1) as total')
	* @param array  $condition  	条件 (参照 buildWhere 方法)
	* @param array 	$order 			排序 (参照 buildOrder 方法)
	* @param array 	$offset 		查询范围 eg: array(10,20) = limit 10,20  or  array(10) = limit 10
	* @param const  $fetch_style 	确定返回的数组采用何种索引类型
	* @param bool 	$is_all 		是返回所有符合条件的数据还是返回一条数据 true 所有  false 一条
	* @return array 返回结果数据
	*/
	public function find($column_arr = array(), $condition = array(), $order = array(), $offset = array(), $is_all = false, $fetch_style = PDO::FETCH_ASSOC)
	{
		if (!is_array($column_arr) || !is_array($condition) || !is_array($order) || !is_array($offset)) {
			return false;
		}
		//验证表名
		$this->checkTableName();

		$column_str = '*';
		if (!empty($column_arr)) {
			$column_str = implode(',', $column_arr);
		}

		$whereSql = '';
		if (!empty($condition)) {
			$where = $this->buildWhereByQuestionMark($condition);
			if (!empty($where['where_arr'])) {
				$whereSql .= ' WHERE ' . $where['whereSql'];
			}
		}

		$sql = 'SELECT ' . $column_str . ' FROM ' . self::$table_name . $whereSql;
		if (!empty($order)) {
			$sql .= $this->buildOrder($order);
		}
		
		if ($is_all) {
			if (!empty($offset)) {
				if (count($offset) == 1) {
					$sql .= ' LIMIT ' . $offset[0];
				}
				if (count($offset) == 2) {
					$sql .= ' LIMIT ' . implode(',', $offset);
				}
			}
		} else {
			$sql .= ' LIMIT 1';
		}

		$stmt = $this->conn->prepare($sql);
		if (isset($where['where_arr']) && !empty($where['where_arr'])) {
			$stmt->execute($where['where_arr']);
		} else {
			$stmt->execute();
		}
		if ($is_all) {
			return $stmt->fetchAll($fetch_style);
		} else {
			return $stmt->fetch($fetch_style);
		}
	}

	/**
	* 查询并所有符合条件的数据
	* @param array  $column_arr 	要查询的列 eg: array('id','username','count(1) as total')
	* @param array  $condition  	条件 (参照 buildWhere 方法)
	* @param array 	$order 			排序 (参照 buildOrder 方法)
	* @param array 	$offset 		查询范围 eg: array(10,20) = limit 10,20
	* @param const  $fetch_style 	确定返回的数组采用何种索引类型
	* @return array 返回结果数据
	*/
	public function findAll($column_arr = array(), $condition = array(), $order = array(), $offset = array(), $fetch_style = PDO::FETCH_ASSOC)
	{
		return $this->find($column_arr, $condition, $order, $offset, true, $fetch_style);
	}

	/**
	* 查询单条数据
	* @param  string $sql 			准备执行的SQL语句
	* @param  array  $param_arr 	参数数组
	* @param  const  $fetch_style 	确定返回的数组采用何种索引类型
	* @return 返回查询结果，有数据返回数组，无数组据返回false
	*/
	public function findBySql($sql, $param_arr = array(), $fetch_style = PDO::FETCH_ASSOC)
	{
		if (empty($sql) || !is_array($param_arr)) {
			return false;
		}
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($param_arr);
		return $stmt->fetch($fetch_style);
	}

	/**
	* 查询并所有符合条件的数据
	* @param  string $sql 			准备执行的SQL语句
	* @param  array  $param_arr 	参数数组
	* @param  const  $fetch_style 	确定返回的数组采用何种索引类型
	* @return 返回查询结果，有数据返回数组，无数组据返回false
	*/
	public function findAllBySql($sql, $param_arr = array(), $fetch_style = PDO::FETCH_ASSOC)
	{
		if (empty($sql) || !is_array($param_arr)) {
			return false;
		}
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($param_arr);
		return $stmt->fetchAll($fetch_style);
	}

	/**
	* 插入数据
	* @param array  $param_arr  要插入的数据 eg: array('username'=>'root','mobile'=>'13432343443')
	* @return 成功返回新增的id，失败返回false
	*/
	public function insert(array $param_arr)
	{
		if (!is_array($param_arr) || empty($param_arr)) {
			return false;
		}
		//验证表名
		$this->checkTableName();

		$fields = implode(',', array_keys($param_arr));
		$place_holders = implode(',', array_fill(0, count($param_arr), '?'));

		$sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', self::$table_name, $fields, $place_holders);

		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array_values($param_arr));
		$id = $this->conn->lastInsertId();
		if ($id > 0) {
			return $id;
		}
		return false;
	}

	/**
	* 更新数据
	* @param array  $param_arr  要更新的数据 eg: array('username'=>'root','mobile'=>'13432343443')
	* @param array  $condition  条件 (参照 buildWhere 方法) eg: array('id' => 33)
	* @return 返回受影响的行数，失败则返回false
	*/
	public function update(array $param_arr, array $condition = array())
	{
		if (!is_array($param_arr) || empty($param_arr) || !is_array($condition)) {
			return false;
		}
		//验证表名
		$this->checkTableName();

		$fieldSql = '';
		$first = true;
		foreach ($param_arr as $key => $value) {
			if ($first == true) {
				$first = false;
				$fieldSql .= $key . '=:' . $key;
			} else {
				$fieldSql .= ',' . $key . '=:' . $key;
			}
		}

		$whereSql = '';
		if (!empty($condition)) {
			// $whereSql = ' WHERE ';
			// $is_first = true;
			// $condition_new = array();
			// $unique = 'con_' . time() . '_';	//避免key与param_arr的key相同
			// foreach ($condition as $key => $value) {
			// 	if ($is_first == true) {
			// 		$is_first = false;
			// 		$whereSql .= $key . '=:' . $unique . $key;
			// 	} else {
			// 		$whereSql .= ' AND ' . $key . '=:' . $unique . $key;
			// 	}
			// 	$condition_new[$unique.$key] = $value;
			// }
			// $param_arr = array_merge($param_arr, $condition_new);

			$where = $this->buildWhere($condition);
			if (!empty($where['where_arr'])) {
				$whereSql .= ' WHERE ' . $where['whereSql'];
				$param_arr = array_merge($param_arr, $where['where_arr']);
			}
		}

		$sql = 'UPDATE '. self::$table_name .' SET ' . $fieldSql . $whereSql;

		$stmt = $this->conn->prepare($sql);
		$stmt->execute($param_arr);
		$count = $stmt->rowCount();
		if ($count > 0) {
			return $count;
		}
		return false;
	}

	/**
	* 删除数据
	* @param $condition 条件 (参照 buildWhere 方法) eg: array('id' => 33)  (验证了不能为空，以免删除整张表)
	* @return  返回受影响的行数，失败则返回false
	*/
	public function delete(array $condition)
	{
		if (!is_array($condition) || empty($condition)) {
			return false;
		}
		//验证表名
		$this->checkTableName();

		$whereSql = '';
		if (!empty($condition)) {
			$where = $this->buildWhereByQuestionMark($condition);
			if (!empty($where['where_arr'])) {
				$whereSql .= ' WHERE ' . $where['whereSql'];
			}
		}

		$sql = 'DELETE FROM ' . self::$table_name . $whereSql;
		$stmt = $this->conn->prepare($sql);
		if (isset($where['where_arr']) && !empty($where['where_arr'])) {
			$stmt->execute($where['where_arr']);
		} else {
			$stmt->execute();
		}
		$count = $stmt->rowCount();
		if ($count > 0) {
			return $count;
		}
		return false;
	}

	/**
	* 用来执行复杂的写操作
	* @param $sql  sql
	* @param $param_arr 数据
	* @return 成功返回受影响的行数，失败返回false
	*/
	public function executeSql($sql, array $param_arr)
	{
		if (empty($sql) || empty($param_arr)) {
			return false;
		}

		$stmt = $this->conn->prepare($sql);
		$stmt->execute($param_arr);
		$count = $stmt->rowCount();
		if ($count > 0) {
			return $count;
		}
		return false;
	}

	/**
	* 拼接where语句  只能全部是and  （通过':'拼接，eg: where id=:id and username=:username）
	* @param array $param_arr  可以是下面任一种或几种组合
	* $param_arr = array(
	*	 	'id' 		=> 23,	// id=23
	*	 	'time' 		=> array(242342, '<='),		// time<=242342
	*	 	'regtime' 	=> array(array(23424, '>='), array(334233, '<=')),	// regtime>=23424 and regtime<=334233
	*	 	'uid' 		=> array(array(3,5,2,9), 'in'),	// uid in (3,5,2,9)
	*	 	'remark' 	=> array('%ee%', 'like'),	// like '%ee%'
	*	);
	*/
	public function buildWhere($param_arr)
	{
		if (!is_array($param_arr) || empty($param_arr)) {
			return '';
		}
		$whereSql = ' 1=1 ';
		$where_arr = array();
		$unique = 'c_'.rand(0,9).'_';
		foreach ($param_arr as $key => $value) {
			if (is_array($value)) {
				if (count($value) == 1 && is_scalar($value[0])) {
					$whereSql .= ' AND ' . $key . '=:' . $unique . $key;
					$where_arr[':' . $unique . $key] = $value[0];
				} elseif (count($value) == 2) {
					if (is_array($value[0])) {
						if (is_array($value[1])) {
							$whereSql .= ' AND ' . $key . ' ' . $value[0][1] . ' ' . ':' . $unique . $key . '_1';
							$whereSql .= ' AND ' . $key . ' ' . $value[1][1] . ' ' . ':' . $unique . $key . '_2';
							$where_arr[':' . $unique . $key . '_1'] = $value[0][0];
							$where_arr[':' . $unique . $key . '_2'] = $value[1][0];
						}
						if (is_scalar($value[1])) {
							$in_arr = array();
							foreach ($value[0] as $kk => $vv) {
								array_push($in_arr, ':' . $unique . $key . '_' . $kk);
								$where_arr[':' . $unique . $key . '_' . $kk] = $vv;
							}
							$whereSql .= ' AND ' . $key . ' IN (' . implode(',', $in_arr) . ')';
						}
					}
					if (is_scalar($value[0]) && is_scalar($value[1])) {
						$whereSql .= ' AND ' . $key . ' ' . $value[1] . ' ' . ':' . $unique . $key;
						$where_arr[':' . $unique . $key] = $value[0];
					}
				}
			}

			if (is_scalar($value)) {
				$whereSql .= ' AND ' . $key . '=:' . $unique . $key;
				$where_arr[':' . $unique . $key] = $value;
			}
		}
		return array('whereSql' => $whereSql, 'where_arr' => $where_arr);
	}

	/**
	* 拼接where语句  只能全部是and  （通过'?'拼接，eg: where id=? and username=?）
	* @param array $param_arr  可以是下面任一种或几种组合
	* $param_arr = array(
	*	 	'id' 		=> 23,	// id=23
	*	 	'time' 		=> array(242342, '<='),		// time<=242342
	*	 	'regtime' 	=> array(array(23424, '>='), array(334233, '<=')),	// regtime>=23424 and regtime<=334233
	*	 	'uid' 		=> array(array(3,5,2,9), 'in'),	// uid in (3,5,2,9)
	*	 	'remark' 	=> array('%ee%', 'like'),	// like '%ee%'
	*	);
	*/
	public function buildWhereByQuestionMark($param_arr)
	{
		if (!is_array($param_arr) || empty($param_arr)) {
			return '';
		}

		$whereSql = ' 1=1 ';
		$where_arr = array();
		foreach ($param_arr as $key => $value) {
			if (is_array($value)) {
				if (count($value) == 1 && is_scalar($value[0])) {
					$whereSql .= ' AND ' . $key . '=?';
					array_push($where_arr, $value[0]);
				} elseif (count($value) == 2) {
					if (is_array($value[0])) {
						if (is_array($value[1])) {
							$whereSql .= ' AND ' . $key . ' ' . $value[0][1] . ' ?';
							$whereSql .= ' AND ' . $key . ' ' . $value[1][1] . ' ?';
							array_push($where_arr, $value[0][0], $value[1][0]);
						}
						if (is_scalar($value[1])) {
							$in_arr = array_fill(0, count($value[0]), '?');
							$where_arr = array_merge($where_arr, $value[0]);
							$whereSql .= ' AND ' . $key . ' IN (' . implode(',', $in_arr) . ')';
						}
					}
					if (is_scalar($value[0]) && is_scalar($value[1])) {
						$whereSql .= ' AND ' . $key . ' ' . $value[1] . ' ?';
						array_push($where_arr, $value[0]);
					}
				}
			}

			if (is_scalar($value)) {
				$whereSql .= ' AND ' . $key . '=?';
				array_push($where_arr, $value);
			}
		}
		return array('whereSql' => $whereSql, 'where_arr' => $where_arr);
	}

	/**
	* 拼接order by语句
	* @param array $order  可以按下面这样传参
	*	array('id')
	*	array('id','desc')
	*	array('id'=>'asc')
	*	array('id'=>'desc', 'bid'=>'asc')
	* @return 'order by ...'
	*/
	public function buildOrder($order = array())
	{
		if (!is_array($order)) {
			return '';
		}
		$sort_arr = array('ASC','DESC');
		$orderStr = '';
		if (count($order) == 1) {
			if (isset($order[0])) {
				$orderStr .= $order[0] . ' ASC ';
			} elseif (is_string(key($order)) && in_array(strtoupper(current($order)), $sort_arr)) {
				$orderStr .= key($order) . ' ' . current($order);
			}
		} elseif (count($order) > 1) {
			if (isset($order[0]) && isset($order[1])) {
				if (in_array(strtoupper($order[1]), $sort_arr)) {
					$orderStr .= $order[0] . ' ' . $order[1];
				}
			} else {
				foreach ($order as $key => $value) {
					if (is_string($key) && in_array(strtoupper($value), $sort_arr)) {
						if (!empty($orderStr)) {
							$orderStr .= ',' . $key . ' ' . $value;
						} else {
							$orderStr = $key . ' ' . $value;
						}
					}
				}
			}
		}

		return !empty($orderStr) ? ' ORDER BY ' . $orderStr : '';
	}


	/**
	* 执行一条 SQL 语句，并返回受影响的行数，复杂的写操作sql语句可以用这个方法（最好不用）
	* @return int 大于或等于0的数值，注意：也可能返回false
	*/
	// public function exec($sql)
	// {
	// 	if (!is_string($sql)) {
	// 		return false;
	// 	}
	// 	return $this->conn->exec($sql);
	// }
	
	/**
	* 执行查询语句（最好不用）
	* @param $sql
	* @param $is_all true: 返回全部 false: 返回一行
	*/
	// public function query($sql, $is_all = false)
	// {
	// 	if (!is_string($sql)) {
	// 		return false;
	// 	}
	// 	$stmt = $this->conn->query($sql);
	// 	if ($is_all) {
	// 		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	// 	} else {
	// 		return $stmt->fetch(PDO::FETCH_ASSOC);
	// 	}
	// }

	/**
	* 返回 SQLSTATE
	* @return string 发生错误返回一个由5个字母或数字组成的在 ANSI SQL 标准中定义的标识符；无错误返回‘0000’
	*/
	public function errorCode()
	{
		return $this->conn->errorCode();
	}

	/**
	* 返回最后一次操作数据库的错误信息
	* @return array
	*/
	public function errorInfo()
	{
		return $this->conn->errorInfo();
	}

	
}