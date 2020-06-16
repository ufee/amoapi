<?php
/**
 * amoCRM API client GET Cached service
 */
namespace Ufee\Amo\Base\Services;

class Cached extends Service
{
	protected static $_cache_time = false;
	protected $modified_from = false;
	
    /**
     * Get cache time
	 * @return integer
     */
	public static function cacheTime()
	{
		return static::canCache() ? static::$_cache_time : -1;
	}

    /**
     * Set cache time
	 * @param integer $value
     */
	public static function setCacheTime($value)
	{
		static::$_cache_time = $value;
	}

    /**
     * Can cache queries
	 * @return bool
     */
	public static function canCache()
	{
		return is_numeric(static::$_cache_time);
	}
}
