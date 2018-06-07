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
     * Can cache queries
	 * @return bool
     */
	public function canCache()
	{
		return $this->cache_time !== false && is_numeric($this->cache_time);
	}
}
