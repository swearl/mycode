<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	protected $_data = array();
	protected $_result = array(
		"status" => 1,
		"msg"    => "",
	);
	protected $_scripts = array();
	protected $_styles = array();

	public function __construct() {
		parent::__construct();
		@session_start();
	}

	public function t($model) {
		$filename = "table/" . ucfirst($model) . "_model";
		$classname = "model_" . $model;
		if(empty($this->$classname)) {
			$this->load->model($filename, $classname);
		}
		return $this->$classname;
	}

	protected function _script($scripts) {
		if(is_array($scripts)) {
			foreach($scripts as $s) {
				$this->_scripts[] = base_url($s . ".js");
			}
		} else {
			$this->_scripts[] = base_url($scripts . ".js");
		}
	}

	protected function _style($styles) {
		if(is_array($styles)) {
			foreach($styles as $s) {
				$this->_styles[] = base_url($s . ".css");
			}
		} else {
			$this->_styles[] = base_url($styles . ".css");
		}
	}

	protected function _set($key, $value) {
		$this->_data[$key] = $value;
	}

	protected function _html($view) {
		$this->_data["scripts"] = $this->_scripts;
		$this->_data["styles"] = $this->_styles;
		$this->load->view($view, $this->_data);
	}

	protected function _json($data) {
		$this->_set("result", $data);
		$this->load->view("json", $this->_data);
	}

	protected function _querystring($page = false) {
		$qs = $_SERVER['QUERY_STRING'];
		if(empty($qs)) {
			return "";
		}
		parse_str($qs, $arr);
		if(!$page && !empty($arr["page"])) {
			unset($arr["page"]);
		}
		return http_build_query($arr);
	}
}

class Front_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}
}

class Back_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	protected function _html($view) {
		// $this->load->view("admin/" . $view, $this->_data);
	}
}

class Admin_Controller extends Back_Controller {
	public function __construct() {
		parent::__construct();
	}
}