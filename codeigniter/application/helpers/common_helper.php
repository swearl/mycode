<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('post')) {
	function post($key = NULL, $allow_tags = '', $xss = TRUE) {
		$CI = & get_instance();
		$val = $CI->input->post($key, $xss, $allow_tags);
		return $val;
	}
}

if (!function_exists('get')) {
	function get($key = NULL, $allow_tags = '', $xss = TRUE) {
		$CI  = & get_instance();
		$val =  $CI->input->get($key, $xss, $allow_tags);
		return $val;
	}
}

if (!function_exists('get_post')) {
	function get_post($key = NULL, $allow_tags = '', $xss = TRUE) {
		$CI = & get_instance();
		$val= $CI->input->get_post($key, $xss, $allow_tags);
		return $val;
	}
}

if (!function_exists('send_request')) {
	function send_request($url, $data = NULL, $ssl = FALSE, $ctype = "application/x-www-form-urlencoded") {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl === TRUE) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Content-Type: ' . $ctype,
				'Content-Length: ' . strlen($data)
			));
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		$errno  = curl_errno($curl);
		curl_close($curl);
		return $output;
	}
}

if (!function_exists('ip')) {
	function ip() {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : 'localhost';
	}
}

if (!function_exists('human2unix')) {
	function human2unix($datestr) {
		if (empty($datestr)) {
			return FALSE;
		}
		return strtotime($datestr);
	}
}

if (!function_exists('unix2human')) {
	function unix2human($time = 0,$format = '') {
		$time = $time <= 0 ? now_time() : $time;
		if (empty($format))
			return date('Y-m-d H:i:s',$time);
		else
			return date($format,$time);
	}
}

if (!function_exists('now_time')) {
	function now_time($m = 0) {
		$time = time();
		if ($m === 1) {
			//去除日期时间部分
			$human_time = unix2human($time,'Y-m-d');
			return human2unix($human_time);
		}
		return $time;
	}
}
