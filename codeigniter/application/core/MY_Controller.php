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
		$this->load->library("Lock");
		$this->_set("page_id", $this->_page_name());
	}

	public function m($model) {
		$filename = ucfirst($model) . "_model";
		$classname = "model_" . $model;
		if(empty($this->$classname)) {
			$this->load->model($filename, $classname);
		}
		return $this->$classname;
	}

	protected function _script($scripts) {
		if(is_array($scripts)) {
			foreach($scripts as $s) {
				$this->_script($s);
			}
		} else {
			if(strpos($scripts, "http") !== false) {
				$this->_scripts[] = $scripts;
			} else {
				$this->_scripts[] = base_url($scripts . ".js");
			}
		}
	}

	protected function _style($styles) {
		if(is_array($styles)) {
			foreach($styles as $s) {
				$this->_style($s);
			}
		} else {
			if(strpos($styles, "http") !== false) {
				$this->_styles[] = $styles;
			} else {
				$this->_styles[] = base_url($styles . ".css");
			}
		}
	}

	protected function _set($key, $value) {
		$this->_data[$key] = $value;
	}

	protected function _set_result($msg, $status = 1, $data = []) {
		$this->_result["status"] = $status;
		$this->_result["msg"] = $msg;
		if(!empty($data)) {
			$this->_result["data"] = $data;
		}
	}
	protected function _html($view) {
		$this->_data["scripts"] = $this->_scripts;
		$this->_data["styles"] = $this->_styles;
		$this->load->view($view, $this->_data);
	}

	protected function _json($data = false) {
		if(!empty($data)) {
			$this->_set("result", $data);
		} else {
			$this->_set("result", $this->_result);
		}
		$this->load->view("json", $this->_data);
		// echo json_encode($this->_data["result"], JSON_UNESCAPED_UNICODE);
		// return true;
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
	// protected function _page() {
	// 	$page = (int)get("page");
	// 	if($page <= 0) {
	// 		$page = 1;
	// 	}
	// 	return $page;
	// }
	private function _page_name() {
		$name = [];
		if(!empty($this->router->directory)) {
			$name[] = trim($this->router->directory, "\\/");
		}
		$name[] = $this->router->class;
		$name[] = $this->router->method;
		return implode("-", $name);
	}
}

class Front_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->_set("assets_url", base_url("assets") . "/");
	}

	protected function _html($view) {
		$this->_data["scripts"] = $this->_scripts;
		$this->_data["styles"] = $this->_styles;
		$this->load->view("front/" . $view, $this->_data);
	}
}

class Back_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	protected function _html($view) {
		$this->load->view("admin/" . $view, $this->_data);
	}
}

class Admin_Controller extends Back_Controller {
	public function __construct() {
		parent::__construct();
	}
}