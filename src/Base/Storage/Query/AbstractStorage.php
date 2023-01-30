<?php
/**
 * amoCRM API client Query cache interface
 */
namespace Ufee\Amo\Base\Storage\Query;
use Ufee\Amo\ApiClient;
use Ufee\Amo\Base\Models\QueryModel;

class AbstractStorage
{
	protected $client;
	protected $options = [];
	protected $querys = [];
	protected static $_initialized = false;
	
	
    /**
     * Constructor
	 * @param ApiClient $client
	 * @param array $options
     */
	public function __construct(ApiClient $client, array $options)
	{
		$this->client = $client;
		$this->options = $options;
	}
	
    /**
     * Get cached query
	 * @param string $hash
	 * @return QueryModel|null
     */
	public function getCached($hash)
	{
		$query = null;
		if (isset($this->querys[$hash])) {
			$query = $this->querys[$hash];
		} 
		if ($query) {
			if (time()-$query->end_time > $query->getService()->cacheTime()) {
				unset($this->querys[$hash]);
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
		$this->querys[$query->hash] = $query;
        return true;
    }
	
    /**
     * Clear query cache
	 * @param QueryModel $query
	 * @return bool
     */
    public function clearQueryCache(QueryModel $query)
    {
		if (isset($this->querys[$query->hash])) {
			unset($this->querys[$query->hash]);
		}
		return true;
    }
	
    /**
     * Flush cache queries
	 * @return bool
     */
    public function flush()
    {
        $this->querys = [];
        return true;
    }
}
