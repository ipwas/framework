<?php

declare(strict_types=1);
namespace happcn;

class App{
	
    public function register(){
		
		
		
	}
	
	public function httpRequest($url, $type, $data = false, $header = [], $gzip = false, $redirect = false, $timeout = 30){
	 	$cl = curl_init();
	 	// 兼容HTTPS
	 	if (stripos($url, 'https://') !== FALSE) {
	 		curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, 0);
	 		curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, 0);
	 		curl_setopt($cl, CURLOPT_SSLVERSION, 1);
	 	}
	 	// GZIP压缩
	 	if($gzip){
	 		curl_setopt($cl, CURLOPT_ENCODING, "gzip");
	 	}
	 
	 	// 允许请求跳转
	 	if($redirect && ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
	 		curl_setopt($cl, CURLOPT_FOLLOWLOCATION, TRUE);
	 	}
	 
	 	// 设置返回内容做变量存储
	 	curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
	 
	 	// 设置需要返回Header
	 	curl_setopt($cl, CURLOPT_HEADER, true);
	 
	 	// 设置请求头
	 	if (count($header) > 0) {
	 		curl_setopt($cl, CURLOPT_HTTPHEADER, $header);
	 	}
	 
	 	// 设置需要返回Body
	 	curl_setopt($cl, CURLOPT_NOBODY, 0);
	 
	 	// 设置超时时间
	 	if ($timeout > 0) {
	 		curl_setopt($cl, CURLOPT_TIMEOUT, $timeout);
	 	}
	 
	 	// POST/GET参数处理
	 	$type = strtoupper($type);
	 		if ($type == 'POST') {
	 		curl_setopt($cl, CURLOPT_POST, true);
	 		if (class_exists('\CURLFile') && is_array($data)) {
	 			foreach ($data as $k => $v) {
	 				if (is_string($v) && strpos($v, '@') === 0) {
	 					$v = ltrim($v, '@');
	 					$data[$k] = new \CURLFile($v);
	 				}
	 			}
	 		}
	 		
	 		curl_setopt($cl, CURLOPT_POSTFIELDS, $data);
	 	}
	 	if ($type == 'GET' && is_array($data)) {
	 		if (stripos($url, "?") === FALSE) {
	 			$url .= '?';
	 		}
	 		$url .= http_build_query($data);
	 	}
	 	if ($type == 'DELETE') {
	 		curl_setopt($cl, CURLOPT_CUSTOMREQUEST, 'DELETE');
	 	}
	 	if ($type == 'PUT') {
	 		curl_setopt($cl, CURLOPT_CUSTOMREQUEST, 'PUT');
	 		curl_setopt($cl, CURLOPT_POSTFIELDS, $data);
	 	}
		if ($type == 'PATCH') {
			curl_setopt($cl, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($cl, CURLOPT_POSTFIELDS, $data);
		}
	 	curl_setopt($cl, CURLOPT_URL, $url);
	 	// 读取获取内容
	 	$response = curl_exec($cl);
	 	// 读取状态
	 	$status = curl_getinfo($cl);
	 	// 读取错误号
	 	$errno  = curl_errno($cl);
	 	// 读取错误详情
	 	$error = curl_error($cl);
	 	//http code
	 	$code = curl_getinfo($cl,CURLINFO_HTTP_CODE);
	 	// 关闭Curl
	 	curl_close($cl);
	 	if ($errno == 0 && isset($status['http_code'])) {
	 		$header = substr($response, 0, $status['header_size']);
	 		$body = substr($response, $status['header_size']);
	 		return array($code,$body, $header, $status, 0, '');
	 	} else {
	 		return array('', '', $status, $errno, $error);
	 	}
	}
	
	
}