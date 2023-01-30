<?php
/**
 * amoCRM API client Query cache - files
 */
namespace Ufee\Amo\Base\Storage\Query;
use Ufee\Amo\ApiClient;
use Ufee\Amo\Base\Models\QueryModel;

class FileStorage extends AbstractStorage
{
    /**
     * Constructor
	 * @param ApiClient $client
	 * @param array $options
     */
	public function __construct(ApiClient $client, array $options)
	{
		parent::__construct($client, $options);
		
		if (empty($this->options['path'])) {
			throw new \Exception('File Storage options[path] must be string path');
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
		$cache_path = $this->options['path'].'/'.$this->client->getAuth('domain').'_'.$hash.'.cache';
		$is_file = false;
		
		if (isset($this->querys[$hash])) {
			$query = $this->querys[$hash];
		} 
		else if (file_exists($cache_path)) {
			$query = unserialize(file_get_contents($cache_path));
			$is_file = true;
		}
		if ($query) {
			if (time()-$query->end_time > $query->getService()->cacheTime()) {
				unset($this->querys[$hash]);
				if ($is_file) {
					@unlink($cache_path);
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
		
        return file_put_contents(
			$this->options['path'].'/'.$this->client->getAuth('domain').'_'.$query->hash.'.cache', serialize($query)
		);
    }
	
    /**
     * Clear query cache
	 * @param QueryModel $query
	 * @return bool
     */
    public function clearQueryCache(QueryModel $query)
    {
		parent::clearQueryCache($query);
		$cache_path = $this->options['path'].'/'.$this->client->getAuth('domain').'_'.$query->hash.'.cache';
		
		if (file_exists($cache_path)) {
			@unlink($cache_path);
		}
		return true;
    }
	
    /**
     * Flush cache queries
	 * @return bool
     */
    public function flush()
    {
        parent::flush();
		array_map('unlink', glob($this->options['path'].'/'.$this->client->getAuth('domain').'_*.cache'));
        return true;
    }
}
