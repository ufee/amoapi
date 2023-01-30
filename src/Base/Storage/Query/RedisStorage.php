<?php
/**
 * amoCRM API client Query cache - Redis
 */
namespace Ufee\Amo\Base\Storage\Query;
use Ufee\Amo\ApiClient;
use Ufee\Amo\Base\Models\QueryModel;

class RedisStorage extends AbstractStorage
{
    /**
     * Constructor
	 * @param ApiClient $client
	 * @param array $options
     */
	public function __construct(ApiClient $client, array $options)
	{
		parent::__construct($client, $options);
		
		if (empty($this->options['connection']) || !$this->options['connection'] instanceOf \Redis) {
			throw new \Exception('Redis Storage options[connection] must be instance of \Redis');
		}
	}
	
    /**
     * Get cached query
	 * @param string $hash
	 * @return QueryModel|null
     */
	public function getCached($hash)
	{
		$query = null;
		$cache_key = $this->client->getAuth('domain').'-cache:'.$hash;
		$is_redis = false;
		
		if (isset($this->querys[$hash])) {
			$query = $this->querys[$hash];
		} 
		else if ($query = $this->options['connection']->get($cache_key)) {
			$is_redis = true;
		}
		if ($query) {
			if (time()-$query->end_time > $query->getService()->cacheTime()) {
				unset($this->querys[$hash]);
				if ($is_redis) {
					$this->options['connection']->del($cache_key);
				}
				$query = null;
			}
		}
		return $query;
    }
	
    /**
     * Cache query
	 * @param QueryModel $query
	 * @return bool
     */
    public function cacheQuery(QueryModel $query)
    {
		parent::cacheQuery($query);
		$cache_key = $this->client->getAuth('domain').'-cache:'.$query->hash;
		
		$ttl = $query->getService()->cacheTime();
        return $this->options['connection']->setEx($cache_key, $ttl, $query);
    }
	
    /**
     * Clear query cache
	 * @param QueryModel $query
	 * @return bool
     */
    public function clearQueryCache(QueryModel $query)
    {
		parent::clearQueryCache($query);
		$cache_key = $this->client->getAuth('domain').'-cache:'.$query->hash;
		
		$this->options['connection']->del($cache_key);
		return true;
    }
	
    /**
     * Flush cache queries
	 * @return bool
     */
    public function flush()
    {
        parent::flush();
		$cache_key = $this->client->getAuth('domain').'-cache:*';
		$keys = $this->options['connection']->keys($cache_key);
		
		foreach($keys as $key) {
			$this->options['connection']->del($key);
		}
        return true;
    }
}
