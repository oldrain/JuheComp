<?php

class JuheAgent
{
	const DATA_TYPE_JSON	= 'json';	// 返回数据类型为 json
	const DATA_TYPE_XML		= 'xml';	// 返回数据类型为 xml
	const DATA_TYPE_JSONP	= 'jsonp';	// 返回数据类型为 jsonp

	const ERROR_CODE_DEFAULT	= 0;

	public $url;
	public $params;
	public $isPost;

	public function __construct($url, $params, $isPost = false)
	{
		$this->_setUrl($url);

		$this->_setParams($params);

		$this->_setIsPost($isPost);
	}

	public function getContent()
	{
		return $this->_juheCurl($this->url, $this->params, $this->isPost);
	}

	protected function _setUrl($url)
	{
		$this->url = $url;
	}

	protected function _setParams($params)
	{
		if ($params && is_array($params)) {
			$this->params = $params;
		} else {
			$this->params = array();
		}
	}

	protected function _setIsPost($isPost)
	{
		$this->isPost = $isPost;
	}

	protected function _juheCurl($url, $params, $isPost)
	{
		//$httpInfo = array();

		$ch = curl_init();

		$query = http_build_query($params);

		curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_USERAGENT , 'JuheData');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 60);
		curl_setopt($ch, CURLOPT_TIMEOUT , 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if ($isPost) {
			curl_setopt($ch , CURLOPT_POST , true);
			curl_setopt($ch , CURLOPT_POSTFIELDS , $query);
			curl_setopt($ch , CURLOPT_URL , $url);
		} else {
			if ($query) {
				curl_setopt($ch , CURLOPT_URL , $url.'?'.$query);
			} else {
				curl_setopt($ch , CURLOPT_URL , $url);
			}
		}

		$response = curl_exec($ch);

		if ($response === false) {
			//echo "cURL Error: " . curl_error($ch);
			return false;
		}

		//$httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE);
		//$httpInfo = array_merge($httpInfo , curl_getinfo($ch));

		curl_close($ch);

		return $response;
	}

}



