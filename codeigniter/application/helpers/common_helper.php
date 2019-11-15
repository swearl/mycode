<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('post')) {
	function post($key = NULL, $default = "", $xss = TRUE) {
		$CI =& get_instance();
		$val = $CI->input->post($key, $xss);
		return is_null($val) ? $default : $val;
	}
}

if (!function_exists('get')) {
	function get($key = NULL, $default = "", $xss = TRUE) {
		$CI  =& get_instance();
		$val =  $CI->input->get($key, $xss);
		return is_null($val) ? $default : $val;
	}
}

if (!function_exists('send_request')) {
	function send_request($url, $data = NULL, $referer = FALSE, $cookie = FALSE) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		if(!empty($referer)) {
			curl_setopt($curl, CURLOPT_REFERER, $referer);
		}
		if(!empty($cookie)) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
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


if (!function_exists("currentUrl")) {
	function currentUrl() {
		$pageURL = 'http';

		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}

if(!function_exists("isMobile")) {
	function isMobile($mobile) {
		return preg_match("/^1[3456789]\d{9}$/", $mobile);
	}
}
