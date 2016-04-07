<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
$this->load->library("Lock");
$lock = $this->lock->get('lock_name');
$this->lock->release($lock);
*/

class Lock {
	var $path = '';

	function __construct(array $params = array()) {
		foreach ($params as $key => $val) {
			if (isset($this->$key)) {
				$this->$key = $val;
			}
		}
		$this->path = empty($this->path) ? APPPATH.'/cache/locks' : $this->path;
		if(!file_exists($this->path)) {
			mkdir($this->path, 0777, true);
		}
	}

	function set_path($path) {
		$this->path = $path;
	}

	function get_path() {
		return $this->path;
	}

	function get($name = '') {
		!empty($name) or die('param "name" can not be null.');
		$fp = @fopen($this->path.'/'.md5($name).'.lck', 'wb');
		if (is_resource($fp) && flock($fp, LOCK_EX)) {
			//echo 'LOCK_OK';
			fwrite($fp, date('Y-m-d H:i:s'));
			return $fp;
		}
		return FALSE;
	}

	function release(&$fp) {
		if (is_resource($fp)) {
			flock($fp, LOCK_UN);
			fclose($fp);
			//echo 'UNLOCK_OK';
			return TRUE;
		}
		return FALSE;
	}
}
?>