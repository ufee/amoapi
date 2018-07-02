<?php
/**
 * amoCRM API client GET Cached service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\Amoapi;

class Cached extends Service
{
	protected
		$cache_time = false,
		$modified_from = false;
	
    /**
     * Get cache time
	 * @return integer
     */
	public function cacheTime()
	{
		return $this->canCache() ? $this->cache_time : -1;
	}

    /**
     * Can cache queries
	 * @return bool
     */
	public function canCache()
	{
		return is_numeric($this->cache_time);
	}
}
