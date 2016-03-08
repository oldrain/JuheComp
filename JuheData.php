<?php

class JuheData
{
	const ID_VTR		= '36'; 	// 全国车辆违章(violation of traffice regulation)
	const ID_VTR_CITY	= '36_city';// 全国车辆违章城市列表
	const ID_WEATHER	= '73'; 	// 天气预报
	const ID_HIVTR		= '91'; 	// 违章高发地(high incidence of violation of traffice regulation)
	
	const VTR_CITY_FORMAT_ASSOCIATE	= 1; // 省份代码作为 key
	const VTR_CITY_FORMAT_INDEX		= 2; // 索引数组
	
	const HIVTR_RADIUS_DEFAULT	= 2000;	// 违章高发地默认半径
	
	const MC_JUHE_DATA	= "juhe_data_cache_%s_%s_%s";
	
	const CACHE_EXPIRE_DEFAULT	= 86400;	// default
	const CACHE_EXPIRE_VTR		= 86400;	// 1 day
	const CACHE_EXPIRE_VTR_CITY	= 604800;	// 7 days
	const CACHE_EXPIRE_WEATHER	= 10800;	// 3 hours
	const CACHE_EXPIRE_HIVTR	= 86400;	// 7 day
	
	public function __construct()
	{
		
	}
	
	public function getVtr($city, $hphm, $hpzl, $engineno, $classno, $isFromCache = true)
	{
		$paramArr = array(
				'city'		=> $city,
				'hphm'		=> $hphm,
				'hpzl'		=> $hpzl,
				'engineno'	=> $engineno,
				'classno'	=> $classno,
				'key'		=> self::_getJuheKey(self::ID_VTR),
				'dtype'		=> JuheAgent::DATA_TYPE_JSON,
		);
		
		return $this->_getJuheData(self::ID_VTR, $paramArr, $isFromCache);
	}
	
	public function getVtrCityAll($format = self::VTR_CITY_FORMAT_INDEX, $isFromCache = true)
	{
		$paramArr = array(
				'format'=> $format,
				'key'	=> self::_getJuheKey(self::ID_VTR_CITY),
				'dtype'	=> JuheAgent::DATA_TYPE_JSON,
		);
		
		return $this->_getJuheData(self::ID_VTR_CITY, $paramArr, $isFromCache);
	}
	
	public function getWeather($city, $isFromCache = true)
	{
		$paramArr = array(
				'cityname'	=> $city,
				'key'		=> self::_getJuheKey(self::ID_WEATHER),
				'dtype'		=> JuheAgent::DATA_TYPE_JSON,
		);
		
		return $this->_getJuheData(self::ID_WEATHER, $paramArr, $isFromCache);
	}
	
	public function getHivrt($lat, $lon, $r = self::HIVTR_RADIUS_DEFAULT, $isFromCache = true)
	{
		if (!Functions::isLegalLatitude($lat) || !Functions::isLegalLongitude($lon)) {
			return array();
		}
		
		$paramArr = array(
				'lat'	=> (string)sprintf("%.4f", $lat),
				'lon'	=> (string)sprintf("%.4f", $lon),
				'r'		=> $r,
				'key'	=> self::_getJuheKey(self::ID_HIVTR),
				'dtype'		=> JuheAgent::DATA_TYPE_JSON,
		);
		
		return $this->_getJuheData(self::ID_HIVTR, $paramArr, $isFromCache);
	}
	
	protected function _getJuheData($juheId, $paramArr, $isFromCache)
	{
		$key = self::_createCacheKey($juheId, $paramArr);
		
		if ($isFromCache) {
			if ($data = $this->_getDataFromCache($key)) {
				return $data;
			}
		}
		
		$newData = $this->_callApi($juheId, $paramArr);
		
		if ($newData && $newData['error_code'] == JuheAgent::ERROR_CODE_DEFAULT) {
			self::_setDataToCache($key, $newData, self::_getExpireTime($juheId));
		}
		
		return $newData;
	}
	
	protected function _callApi($juheId, $paramArr)
	{
		$retArr = array();
		
		$juheAgent = new JuheAgent(self::_getJuheUrl($juheId), $paramArr);
		
		$content = $juheAgent->getContent();
		$data = json_decode($content, true);
		
		if ($data) {
			$retArr = $data;
			VtrLog::juhe($juheId, json_encode($paramArr), json_encode($data));
		}
		
		return $retArr;
	}
	
	protected function _getDataFromCache($key)
	{
		$vCache = new VCache();
		return $vCache->get($key);
	}
	
	protected function _setDataToCache($key, $data, $expire)
	{
		$vCache = new VCache();
		$vCache->set($key, $data, $expire);
	}
	
	protected static function _createCacheKey($juheId, $paramArr)
	{
		$range = '';
		switch ($juheId) {
			case self::ID_VTR:
				$range = date('Ymd');
				break;
			case self::ID_VTR_CITY:
				$range = date('w');
				break;
			case self::ID_WEATHER:
				$range = date('Ymd');
				break;
			case self::ID_HIVTR:
				$range = date('w');
				break;
		}
		return sprintf(self::MC_JUHE_DATA, $juheId, $range, md5(json_encode($paramArr)));
	}
	
	protected static function _getExpireTime($juheId)
	{
		switch ($juheId) {
			case self::ID_VTR:
				return self::CACHE_EXPIRE_VTR;
			case self::ID_VTR_CITY:
				return self::CACHE_EXPIRE_VTR_CITY;
			case self::ID_WEATHER:
				return self::CACHE_EXPIRE_WEATHER;
			case self::ID_HIVTR:
				return self::CACHE_EXPIRE_HIVTR;
			default:
				return self::CACHE_EXPIRE_DEFAULT;
		}
	}
	
	protected static function _getJuheUrl($juheId)
	{
		return Config::get('juhe.api.' .$juheId. '.url');
	}
	
	protected static function _getJuheKey($juheId)
	{
		return Config::get('juhe.api.' .$juheId. '.appkey');
	}
	
}


