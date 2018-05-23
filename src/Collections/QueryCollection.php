<?php
/**
 * amoCRM API Query Collection class
 */
namespace Ufee\Amo\Collections;

class QueryCollection extends \Ufee\Amo\Base\Collections\Collection
{
    /**
     * Get cached query
	 * @param string $hash
     * @param mixed $cache_time
	 * @return Query|null
     */
	public function getCached($hash, $cache_time = false)
	{
        if ($cache_time === false) {
           return false;
        }        
        $queries = $this->find('hash', $hash)->filter(function($query) use($cache_time) {
            return $cache_time === 0 || microtime(1)-$query->end_time <= $cache_time;
        });
        return $queries->first();
    }
}
