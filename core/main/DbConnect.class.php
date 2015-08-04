<?php

/**
* 连接数据库
*/
class DbConnect
{
	private static $instance = NULL;

	private function __construct()
	{

	}

	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public static function getInstance()
	{
		if (!self::$instance instanceof self) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function connectDb()
	{
		try {
			$conn = new PDO('mysql:dbname='. DBConfig::DB_NAME .';host='. DBConfig::DB_HOST .';charset=' . DBConfig::CHARSET, DBConfig::USER_NAME, DBConfig::PASSWORD);
			$conn->query("set names " . DBConfig::CHARSET);
			$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);	//php 5.3.6 以前需要设置为false，以防止SQL注入
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch (PDOException $e) {
			echo 'Connect db failed:'.$e->getMessage();
			return FALSE;
		}
	}
}