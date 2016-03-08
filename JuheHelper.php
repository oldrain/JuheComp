<?php

class JuheHelper
{
	const WEATHER_TYPE_DEFAULT	= 0;	
	const WEATHER_TYPE_SUNNY	= 1;
	const WEATHER_TYPE_CLOUDY	= 2;
	const WEATHER_TYPE_OVERCAST	= 3;
	const WEATHER_TYPE_RAIN		= 4;
	const WEATHER_TYPE_SNOW		= 5;
	
	public static function getWeatherType($wid)
	{
		if (array_key_exists($wid, self::getWeatherCateListSunny())) {
			return self::WEATHER_TYPE_SUNNY;
		} else if (array_key_exists($wid, self::getWeatherCateListCloudy())) {
			return self::WEATHER_TYPE_CLOUDY;
		} else if (array_key_exists($wid, self::getWeatherCateListOvercast())) {
			return self::WEATHER_TYPE_OVERCAST;
		} else if (array_key_exists($wid, self::getWeatherCateListRain())) {
			return self::WEATHER_TYPE_RAIN;
		} else if (array_key_exists($wid, self::getWeatherCateListSnow())) {
			return self::WEATHER_TYPE_SNOW;
		} else {
			return self::WEATHER_TYPE_DEFAULT;
		}
	}
	
	public static function getWeatherCateListSunny()
	{
		return Config::get('juhe.weather.categoty.sunny');
	}
	
	public static function getWeatherCateListCloudy()
	{
		return Config::get('juhe.weather.categoty.cloudy');
	}
	
	public static function getWeatherCateListOvercast()
	{
		return Config::get('juhe.weather.categoty.overcast');
	}
	
	public static function getWeatherCateListRain()
	{
		return Config::get('juhe.weather.categoty.rain');
	}
	
	public static function getWeatherCateListSnow()
	{
		return Config::get('juhe.weather.categoty.snow');
	}
	
}



