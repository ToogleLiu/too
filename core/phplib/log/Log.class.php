<?php
/**
* 日志类
*/
class Log
{
	//log level
	const LOG_LEVEL_NONE 	= 0x00;
	const LOG_LEVEL_FATAL 	= 0x01;
	const LOG_LEVEL_WARNING = 0x02;
	const LOG_LEVEL_NOTICE 	= 0x04;
	const LOG_LEVEL_TRACE 	= 0x08;
	const LOG_LEVEL_DEBUG 	= 0x10;
	const LOG_LEVEL_ALL 	= 0xFF;

	//log type
	const LOG_TYPE_LOCALLOG = 'LOCAL_LOG';
	const LOG_TYPE_NETLOG	= 'NET_LOG';

	public static $logLevelMap = array(
		self::LOG_LEVEL_NONE 	=> 'NONE',
		self::LOG_LEVEL_FATAL 	=> 'FATAL',
		self::LOG_LEVEL_WARNING => 'WARNING',
		self::LOG_LEVEL_NOTICE 	=> 'NOTICE',
		self::LOG_LEVEL_DEBUG 	=> 'DEBUG',
		self::LOG_LEVEL_ALL 	=> 'ALL',
	);

	public static $logTypes = array(self::LOG_TYPE_LOCALLOG, self::LOG_TYPE_NETLOG);

	protected $type;
	protected $level;
	protected $path;
	protected $filename;
	protected $startTime;
	protected $logId;
	protected $clientIP;

	private static $instance = NULL;
	
	private function __construct(Array $conf, $startTime)
	{
		$this->type 	= $conf['type'];
		$this->level 	= $conf['level'];
		$this->path 	= $conf['path'];
		$this->filename = $conf['filename'];

		$this->startTime = $startTime;
		$this->logId = $this->__logId();
		$this->clientIP = Utils::getClientIP();
		if ($this->type === self::LOG_TYPE_NETLOG) {
			openlog($conf['appname'], LOG_PID | LOG_PERROR, LOG_LOCAL1);
		}
	}

	public static function getInstance()
	{
		if (!self::$instance instanceof self) {
			$startTime = defined('PROCESS_START_TIME') ? PROCESS_START_TIME : microtime(true) * 1000;
			self::$instance = new self($GLOBALS['LOG'], $startTime);
		}
		return self::$instance;
	}

	/**
	* debug日志
	*/
	public static function debug()
	{
		$args = func_get_args();
		return self::getInstance()->writeLog(self::LOG_LEVEL_DEBUG, $args);
	}

	/**
	* trace日志
	*/
	public static function trace()
	{
		$args = func_get_args();
		return self::getInstance()->writeLog(self::LOG_LEVEL_TRACE, $args);
	}

	/**
	* notice日志
	*/
	public static function notice()
	{
		$args = func_get_args();
		return self::getInstance()->writeLog(self::LOG_LEVEL_NOTICE, $args);
	}

	/**
	* 写warning级别的日志
	*/
	public static function warning()
	{
		$args = func_get_args();
		return self::getInstance()->writeLog(self::LOG_LEVEL_WARNING, $args);
	}

	/**
	* 写fatal级别的日志
	*/
	public static function fatal()
	{
		$args = func_get_args();
		return self::getInstance()->writeLog(self::LOG_LEVEL_FATAL, $args);
	}

	/**
	* 写日志
	*/
	protected function writeLog($level, Array $args)
	{
		if ($level > $this->level || !isset(self::$logLevelMap[$level])) {
			return 0;
		}

		$depth = 1;
		if (is_int($args[0])) {
			$depth = array_shift($args) + 1;
		}

		$timeUsed = microtime(true) * 1000 - $this->startTime;

		$str = '';
		if (count($args) == 1) {
			$str = reset($args);
		}
		if (count($args) > 1) {
			$str = vsprintf(array_shift($args), $args);
		}

		if ($level == self::LOG_LEVEL_NOTICE) {
			$str = sprintf("%s:@@%s@@host[%s]@@ip[%s] @@logId[%u]@@uri[%s] @@time_used[%d]@@ %s\n",
					self::$logLevelMap[$level],
					date('Y-m-d H:i:s',time()),
					$_SERVER['HTTP_HOST'],
					$this->clientIP,
					$this->logId,
					isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
					$timeUsed,
					$str
					);
		} else {
			$str = sprintf("%s: %s host[%s] ip[%s] logId[%u] uri[%s] time_used[%d] %s\n",
					self::$logLevelMap[$level],
					date('Y-m-d H:i:s', time()),
					$_SERVER['HTTP_HOST'],
					$this->clientIP,
					$this->logId,
					isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
					$timeUsed,
					$str
					);
		}

		if ($this->type === self::LOG_TYPE_LOCALLOG) {
			$filename = $this->path . '/' . $this->filename;
			if ($level < self::LOG_LEVEL_NOTICE) {
				$filename .= '.wf';
			}
			Utils::mkdirs($this->path);
			$strlen = file_put_contents($filename, $str, FILE_APPEND | LOCK_EX);
			@chmod($filename, 0777);
			return $strlen;
		} else {
			syslog(LOG_DEBUG, $str);
			closelog();
			return strlen($str);
		}
	}

	/**
	* 生成log id
	*/
	private function __logId()
	{
		$arr = gettimeofday();
		return ($arr['sec'] * 1000000 + $arr['usec']) & 0x7FFFFFFF;
	}
}