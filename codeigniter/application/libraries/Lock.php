<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lock {
	public $path = '';
	protected $_lock_fp = [];

	function __construct() {
		$this->path = empty($this->path) ? APPPATH . '/cache/locks' : $this->path;
		if(!file_exists($this->path)) {
			mkdir($this->path, 0777, true);
		}
	}

	function get($name) {
		$fp = @fopen($this->path . '/' . md5($name) . '.lck', 'wb');
		if (is_resource($fp) && flock($fp, LOCK_EX)) {
			fwrite($fp, date('Y-m-d H:i:s'));
			$this->_lock_fp[$name] = $fp;
			return $fp;
		}
		return false;
	}

	function release($name) {
		if(!isset($this->_lock_fp[$name])) {
			return false;
		}
		$fp = $this->_lock_fp[$name];
		if (is_resource($fp)) {
			flock($fp, LOCK_UN);
			fclose($fp);
			unset($this->_lock_fp[$name]);
			return true;
		}
		return false;
	}
}
