<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
}

class DB_Model extends MY_Model {
	protected $_table = null;
	protected $_key = null;
	protected $_fields = array();
	public $row = null;

	// for list
	protected $_query = false;
	protected $_limit = 20;
	protected $_filter = false;
	protected $_order = false;
	protected $_total = 0;

	public function __construct($table, $key) {
		parent::__construct();
		$this->_table = $table;
		$this->_key = $key;
		$this->row = new stdClass();
		$this->_get_fields();
	}

	protected function t($model) {
		$filename = "table/" . ucfirst($model) . "_model";
		$classname = "model_" . $model;
		if(empty($this->$classname)) {
			$this->load->model($filename, $classname);
		}
		return $this->$classname;
	}

	protected function bind($src) {
		if(!is_object($src) && !is_array($src)) {
			return false;
		}
		if(is_object($src)) {
			$src = (array)$src;
		}
		foreach($this->_fields as $v) {
			if(isset($src[$v])) {
				$this->row->$v = $src[$v];
			}
		}
		return true;
	}

	public function check() {
		return true;
	}

	public function save($src) {
		if(!$this->bind($src)) {
			return false;
		}
		if(!$this->check()) {
			return false;
		}
		return $this->store();
	}

	public function store() {
		$keyname = $this->_key;
		if(empty($this->row->$keyname)) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}

	public function insert() {
		foreach($this->_fields as $v) {
			if($v != $this->_key) {
				$this->db->set($v, $this->row->$v);
			}
		}
		$this->db->insert($this->_table);
		$id = $this->db->insert_id();
		return $this->get($id);
	}

	public function update() {
		$keyname = $this->_key;
		foreach($this->_fields as $v) {
			if($v == $keyname) {
				$this->db->where($v, $this->row->$v);
			} else {
				$this->db->set($v, $this->row->$v);
			}
		}
		$this->db->update($this->_table);
		return $this->get($this->row->$keyname);
	}

	public function delete($pks) {
		if(empty($pks)) {
			return false;
		}
		if(!is_array($pks)) {
			$this->db->where($this->_key, $pks);
		} else {
			$this->db->where_in($this->_key, $pks);
		}
		$this->db->delete($this->_table);
		return true;
	}

	public function publish($pks, $status = 1) {
		if(empty($pks)) {
			return false;
		}
		if(!is_array($pks)) {
			$this->db->where($this->_key, $pks);
		} else {
			$this->db->where_in($this->_key, $pks);
		}
		$this->db->set("status", $status);
		$this->db->update($this->_table);
		return true;
	}

	public function get_by($field, $value, $empty = false) {
		$this->db->where($field, $value);
		$query = $this->db->get($this->_table, 1);
		if($query->num_rows() == 1) {
			$this->row = $query->first_row();
			return $this->row;
		}
		if($empty) {
			$this->row = new stdClass;
			foreach($this->_fields as $v) {
				$this->row->$v = "";
			}
			return $this->row;
		}
		return false;
	}

	public function get($key, $empty = false) {
		return $this->get_by($this->_key, $key, $empty);
	}

	public function reset_row() {
		$this->row = null;
		$this->row = new stdClass();
	}

	private function _get_fields() {
		$fields = $this->db->field_data($this->_table);
		foreach($fields as $f) {
			$field = $f->name;
			$this->_fields[] = $field;
			$this->row->$field = "";
		}
		return $this;
	}


	// List start

	// get full list
	public function get_list() {
		$params = func_get_args();
		$params_length = count($params);
		if($params_length % 2) {
			throw new Exception("get_list params length error");
		}
		for($i = 0; $i < $params_length; $i += 2) {
			$this->db->where($params[$i], $params[$i + 1]);
		}
		$query = $this->db->get($this->_table);
		return $query->result();
	}

	// get search result
	public function set_query() {
		if(!$this->_query) {
			$this->db->select("*")->from($this->_table);
			$this->_query = true;
		}
	}

	public function set_filter() {
		if(!$this->_filter) {
			$this->_filter = true;
		}
	}

	public function set_limit($limit = 0) {
		if(!empty($limit)) {
			$this->_limit = $limit;
		}
	}

	public function set_order() {
		if(!empty($this->_order)) {
			$this->db->order_by($this->_order);
		}
	}

	public function get_total() {
		$this->set_query();
		$this->set_filter();
		$this->_total = $this->db->count_all_results("", false);
		return $this->_total;
	}

	public function get_pages() {
		return ceil($this->_total / $this->_limit);
	}

	public function get_result() {
		$this->set_query();
		$this->set_filter();
		$this->set_order();
		if($this->_limit != "all") {
			$start = $this->get_start();
			$this->db->limit($this->_limit, $start);
		}
		$query = $this->db->get();
		$this->reset_result();
		return $query->result();
	}

	public function get_start() {
		$page = $this->get_page();
		$start = ($page - 1) * (int)$this->_limit;
		return $start;
	}

	public function get_page() {
		$page = intval(get("page"));
		if($page < 1) $page = 1;
		return $page;
	}

	public function reset_result() {
		$this->_query = $this->_filter = false;
		$this->_total = 0;
	}
}