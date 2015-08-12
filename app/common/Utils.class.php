<?php
/**
* 公用的小工具
*
*/
class Utils
{
	/**
	* 清除数组中的空元素
	* @param $array 需要进行过滤的数组，一元或多元数组
	* @param $notCleanArray array 不过滤的元素，比如array(false,0)，将保留为false和0的元素
	* @return array 返回过滤后数组
	*/
	public static function cleanArray($array, $notCleanArray = array())
	{
		foreach ($array as $key => $value) {
			if (!empty($notCleanArray) && is_array($notCleanArray)) {
				$is_continue = false;
				foreach ($notCleanArray as $val) {
					if ($value === $val) {
						$is_continue = true;
						break;
					}
				}
				if ($is_continue == true) {
					continue;
				}
			}
			
			if (is_array($value) && !empty($value)) {
				$array[$key] = self::cleanArray($value, $notCleanArray);
			} elseif (empty($value)) {
				unset($array[$key]);
			}
		}
		return array_values($array);
	}

	/**
	* 验证是否为整数，若传了$min, $max 则表示$min <= $value <= $max
	* @param $value 需要验证的值
	* @param $min 最小值
	* @param $max 最大值
	* @return bool 为true表示验证通过，false反之
	*/
	// public static function check_int($value, $min = NULL, $max = NULL)
	// {
	// 	if (!preg_match('/^\d+$/', $value)) {
	// 		return false;
	// 	}

	// 	if (!is_null($min) && is_null($max)) {
	// 		if ($value >= $min) {
	// 			return true;
	// 		} else {
	// 			return false;
	// 		}
	// 	} elseif (is_null($min) && !is_null($max)) {
	// 		if ($value <= $max) {
	// 			return true;
	// 		} else {
	// 			return false;
	// 		}
	// 	} elseif (!is_null($min) && !is_null($max)) {
	// 		if ($value >= $min && $value <= $max) {
	// 			return true;
	// 		} else {
	// 			return false;
	// 		}
	// 	} else {
	// 		return true;
	// 	}
	// }

	/**
	* 验证是否为整数，若传了$min, $max 则表示$min <= $value <= $max
	* @param $value 需要验证的值
	* @param $min 最小值
	* @param $max 最大值
	* @return bool 为true表示验证通过，false反之
	*/
	public static function check_int($value, $min = NULL, $max = NULL)
	{
		
		$int_options = array('options' => array());

		if (!is_null($min)) {
			$int_options['options']['min_range'] = $min;
		}
		if (!is_null($max)) {
			$int_options['options']['max_range'] = $max;
		}

		if (($var = filter_var($value, FILTER_VALIDATE_INT, $int_options)) !== FALSE) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* 验证是否为合法的整数（同上）
	* @param array $arr  eg: array($num, 100, 500) 表示100 <= $num <= 500
	* @return bool
	*/
	public static function check_int_arr($arr)
	{
		if (!is_array($arr) || empty($arr)) {
			return FALSE;
		}
		$value = isset($arr[0]) ? $arr[0] : NULL;
		$min = isset($arr[1]) ? $arr[1] : NULL;
		$max = isset($arr[2]) ? $arr[2] : NULL;
		return self::check_int($value, $min, $max);
	}

	/**
	* 验证是否为合法的字符串
	* @param $str 需要验证的参数
	* @param $min_length 最小长度
	* @param $max_length 最大长度
	* @return bool 为true表示验证通过，false反之
	*/
	public static function check_string($str, $min_length = NULL, $max_length = NULL)
	{
		if (!is_scalar($str)) {
			return FALSE;
		}

		if (!is_null($min_length) && mb_strlen($str, 'utf-8') < $min_length) {
			return FALSE;
		}

		if (!is_null($max_length) && mb_strlen($str, 'utf-8') > $max_length) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	* 验证是否为合法的字符串
	* @param array $arr eg: array($str, $min_length, $max_length)
	* @return bool
	*/
	public static function check_string_arr($arr)
	{
		if (!is_array($arr) || empty($arr)) {
			return FALSE;
		}
		$value = isset($arr[0]) ? $arr[0] : NULL;
		$min = isset($arr[1]) ? $arr[1] : NULL;
		$max = isset($arr[2]) ? $arr[2] : NULL;
		return self::check_string($value, $min, $max);
	}

	/**
	* 验证参数是否为数字、字符串
	* @param array 例子：$checkData = array(
	*							'int' 	 => array(
	*											'uid' => array($uid, 1),
	*											'sex' => array($sex, 1, 2),
	*											'age' => $age,
	*										),
	*							'string' => array('username' => array($username, 2, 30)),	//与int一样
	*						)
	* @return 如果验证失败，抛异常
	*/
	public static function checkData(array $checkData)
	{
		if (!is_array($checkData) || empty($checkData)) {
			self::throwException('param error: checkData[%s].', var_export($checkData, 1));
		}
		foreach ($checkData as $key => $value) {
			if (!is_array($value) || empty($value)) {
				continue;
			}
			
			foreach ($value as $kk => $vv) {
				if (is_array($vv)) {
					if (($key == 'int' && self::check_int_arr($vv) == FALSE) || ($key == 'string' && self::check_string_arr($vv) == FALSE)) {
						self::throwException('param error: %s[%s], it must be %s.', $kk, var_export($vv, 1), $key);
						break;
					}
				} else {
					if (($key == 'int' && self::check_int($vv) == FALSE) || ($key == 'string' && self::check_string($vv) == FALSE)) {
						self::throwException('param error: %s[%s], it must be %s.', $kk, var_export($vv, 1), $key);
					}
				}
			}
		}
	}

	/**
	* 抛异常
	*/
	public static function throwException()
	{
		$args = func_get_args();
		$args_num = func_num_args();
		if ($args_num < 1) {
			throw new Exception('Error.');
		}
		if ($args_num == 1) {
			throw new Exception($args[0]);
		}
		if ($args_num > 1) {
			$content = vsprintf(array_shift($args), $args);
			throw new Exception($content);
		}
	}

	/**
	* 验证字段数据
	* @param $param_arr 数据 array('uid'=>33,'username'=>'root')
	* @param $clolum 表字段数组及其规则
	* @return 不合法则抛异常
	*/
	public static function checkColumnData(array $param_arr, array $column)
	{
		if (empty($param_arr)) {
			return false;
		}
		if (empty($column)) {
			self::throwException('The column data is empty.');
		}
		$checkData = array();
		foreach ($param_arr as $key => $value) {
			if (in_array($key, array_keys($column))) {
				switch ($column[$key]['type']) {
					case 'int':
						$checkData['int'][$key] = array($value, $column[$key]['min_value'], $column[$key]['max_value']);
						break;
					case 'string':
						$checkData['string'][$key] = array($value, $column[$key]['min_len'], $column[$key]['max_len']);
						break;
					default:
						break;
				}
			} else {
				self::throwException('[%s] is not a column in the table.', var_export($key,1));
			}
		}
		if (!empty($checkData)) {
			self::checkData($checkData);
		}
	}

	/**
	* 验证where条件
	* @param $condition = array(
	*	 	'id' 		=> 23,	// id=23
	* 		'id' 		=> array(332),
	*	 	'time' 		=> array(242342, '<='),		// time<=242342
	*	 	'regtime' 	=> array(array(23424, '>='), array(334233, '<=')),	// regtime>=23424 and regtime<=334233
	*	 	'uid' 		=> array(array(3,5,2,9), 'in'),	// uid in (3,5,2,9)
	*	 	'remark' 	=> array('%ee%', 'like'),	// like '%ee%'
	*	);
	* @param $column 表的字段及其规则
	* @return 验证不通过则抛异常
	*/
	public function checkCondition(array $condition, array $column)
	{
		if (empty($condition)) {
			return false;
		}
		if (empty($column)) {
			self::throwException('The column data is empty.');
		}

		$check_arr = array();
		$check_more_arr = array();
		foreach ($condition as $key => $value) {
			if (is_scalar($value)) {
				$check_arr[$key] = $value;
			} elseif (is_array($value)) {
				if (count($value) == 1) {
					$check_arr[$key] = current($value);
				} elseif (count($value) == 2) {
					if (is_array(reset($value))) {
						if (isset($value[0]) && isset($value[1])) {
							if (is_array($value[1])) {
								$check_more_arr[$key] = array(reset($value[0]), reset($value[1]));
							} elseif (is_scalar($value[1]) && strtolower($value[1]) == 'in') {
								$check_more_arr[$key] = $value[0];
							} else {
								self::throwException('The value (%s) of the column (%s) is a invalid type.', var_export($value, 1), var_export($key, 1));
							}
						} else {
							self::throwException('The value (%s) of the column (%s) is invalid.', var_export($value, 1), var_export($key, 1));
						}
					} else {
						$check_arr[$key] = reset($value);
					}
				} else {
					self::throwException('The column (%s) value (%s). The array\'s length should be 1 or 2.', var_export($key, 1), var_export($value, 1));
				}
			} else {
				self::throwException('The value (%s) of the column (%s) is a invalid type.', var_export($value, 1), var_export($key, 1));
			}
		}

		if (count($check_arr) > 0) {
			self::checkColumnData($check_arr, $column);
		}
		if (count($check_more_arr) > 0) {
			foreach ($check_more_arr as $key => $value) {				
				foreach ($value as $vv) {
					self::checkColumnData(array($key => $vv), $column);
				}
			}
		}
	}

	/**
	* 获取客户端IP
	*
	*/
	public static function getClientIP()
	{
		$ip = '';
		if (isset($_SERVER['HTTP_CDN_SRC_IP']) && !empty($_SERVER['HTTP_CDN_SRC_IP'])) {
			$ip = $_SERVER['HTTP_CDN_SRC_IP'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if ($pos !== false) {
				unset($arr[$pos]);
			}
			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	* 创建文件夹
	* @param $dir 要创建的目录
	*/
	public static function mkdirs($dir)
	{
		if (is_dir($dir)) {
			return true;
		}

		$parent = dirname($dir);
		if (is_dir($parent) || self::mkdirs($parent)) {
			return @mkdir($dir, 0777);
		}

		return false;
	}


	/**
	* 404错误页
	*
	*/
	public static function notFound()
	{
		ob_get_clean();
		header('HTTP/1.0 404 Not Found');
		include SMARTY_TEMPLATE_DIR . '/error/404.html';
		exit;
	}
}