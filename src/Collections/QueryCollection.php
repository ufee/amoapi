<?php
/**
 * amoCRM API Query Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\ApiClient;
use Ufee\Amo\Api;
use Ufee\Amo\Base\Models\QueryModel;
use Ufee\Amo\Base\Storage;

if (!defined('AMOAPI_ROOT')) {
	define('AMOAPI_ROOT', substr(dirname(__FILE__), 0, -12));
}
class QueryCollection extends \Ufee\Amo\Base\Collections\Collection
{
	protected $cacheStorage;
    protected 
        $instance,
        $delay = 0.15,
        $refresh_time = 900,
        $curl_interfaces = [],
        $cookie_file = '',
        $_listener,
        $_listener_by_code = [],
        $logger = null,
        $_logs = false;
    
    /**
     * Boot instance
	 * @param ApiClient $instance
     */
    public function boot(ApiClient &$instance)
    {
        $this->instance = $instance;
        $this->logger = Api\Logger::getInstance($instance->getAuth('domain').'.log');

		$this->setCacheStorage(
			new Storage\Query\FileStorage($this->instance, ['path' => AMOAPI_ROOT.'/Cache'])
		);
		if ($instance instanceof Amoapi) {
			$this->cookie_file = AMOAPI_ROOT.DIRECTORY_SEPARATOR.'Cookies'.DIRECTORY_SEPARATOR.$instance->getAuth('domain').'.cookie';
			$this->refreshSession();
		}
    }

    /**
     * Get query delay
     * @return integer
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set query delay
     * @param integer $value
     */
    public function setDelay($value)
    {
        $this->delay = $value;
    }
	
	/**
	 * Set curl interfaces
	 * @param array $interfaces
	 */
	public function viaInterfaces(array $interfaces)
	{
		$this->curl_interfaces = $interfaces;
	}
	
	/**
	 * Get curl interface
	 * @return string|null
	 */
	public function getInterface()
	{
		if ($this->curl_interfaces === []) {
			return null;
		}
		return $this->curl_interfaces[array_rand($this->curl_interfaces)];
	}
	
    /**
     * Get token refresh time
     * @return integer
     */
    public function getRefreshTime()
    {
        return $this->refresh_time;
    }

    /**
     * Get cookie path
     * @return string
     */
    public function getCookiePath()
    {
        return $this->cookie_file;
    }

    /**
     * Refresh current session
     * @return string
     */
    public function refreshSession()
    {
		if ($this->cookie_file && file_exists($this->cookie_file) && ($cookies = file_get_contents($this->cookie_file))) {
			if (preg_match('#session_id\s(.+)\s#Uis', $cookies, $match) && !empty($match[1])) {
                clearstatcache(true, $this->cookie_file);
                $this->instance->setSession($match[1], filemtime($this->cookie_file));
            }
        }
        return $this;
    }
    
    /**
     * Push new queries
	 * @param QueryModel $query
     * @param bool $save
	 * @return QueryCollection
     */
    public function pushQuery(QueryModel $query, $save = true)
    {
        if ($this->_logs) {
            $this->logger->log(
                '['.$query->method.'] '.$query->url.' -> '.$query->getUrl(),
                $query->headers,
                count($query->json_data) ? $query->json_data : $query->post_data,
                'Start: '.$query->startDate('H:i:s').' ('.$query->start_time.')',
                'End:   '.$query->endDate('H:i:s').' ('.$query->end_time.'), retries: '.$query->getRetries(),
                'Execution time: '.$query->execution_time.' (sleep: '.(float)$query->sleep_time.')',
                'Memory used: '.$query->memory_usage.' mb',
                'Response code: '.$query->response->getCode(),
                $query->response->getData()
            );
        }
        if (is_callable($this->_listener)) {
            call_user_func($this->_listener, $query);
        }
        if ($save) {
            array_push($this->items, $query);
            $service = $query->getService();
            if ($service && $service->canCache()) {
                $this->cacheQuery($query);
            }
        }
        return $this;
	}
	
    /**
     * Push queries by code
     * @param integer $code - 200,403,429,500,502,504,..
	 * @param QueryModel $query
	 * @return QueryCollection
     */
    public function pushByCode($code, QueryModel $query)
    {
		if (array_key_exists($code, $this->_listener_by_code) && is_callable($this->_listener_by_code[$code])) {
			call_user_func($this->_listener_by_code[$code], $query);
		}
		return $this;
	}

    /**
     * Log queries
	 * @param mixed $val
     * @return QueryCollection
     */
    public function logs($val)
    {
        if (is_bool($val)) {
            if ($this->_logs = $val) {
                $this->logger->setDefaultPath();
            }
        }
        if (is_string($val)) {
            $this->_logs = true;
            $this->logger->setCustomPath($val);
        }
        return $this;
	}

    /**
     * Debug queries
	 * @param callable $callback
	 * @return QueryCollection
     */
    public function listen(callable $callback)
    {
        $this->_listener = $callback;
        return $this;
    }
	
    /**
     * Debug queries by code
	 * @param integer $code - 200,403,429,500,502,504,..
	 * @param callable $callback
	 * @return QueryCollection
     */
    public function onResponseCode($code, callable $callback)
    {
        $this->_listener_by_code[intval($code)] = $callback;
        return $this;
    }

    /**
     * Flush cache queries
	 * @return QueryCollection
     */
    public function flush()
    {
        $this->items = [];
		$this->cacheStorage->flush();
        return $this;
    }
    
    /**
     * Cache query
	 * @param QueryModel $query
	 * @return bool
     */
    public function cacheQuery(QueryModel $query)
    {
		return $this->cacheStorage->cacheQuery($query);
    }
	

    /**
     * Get cached query
	 * @param string $hash
	 * @return QueryModel|null
     */
	public function getCached($hash)
	{
		return $this->cacheStorage->getCached($hash);
    }

	/**
	 * Set query cache path - deprecated
	 * @param string $path
	 * @return Oauthapi
	 */
	public function setCachePath($path)
	{
		$this->setCacheStorage(
			new Storage\Query\FileStorage($this->instance, ['path' => $path])
		);
		return $this;
	}
	
	/**
	 * Get cache storage handler
	 * @return AbstractStorage
	 */
	public function getCacheStorage()
	{
		return $this->cacheStorage;
	}
	
	/**
	 * Set cache storage handler
	 * @param AbstractStorage $storage
	 * @return void
	 */
	public function setCacheStorage(Storage\Query\AbstractStorage $storage)
	{
		$this->cacheStorage = $storage;
	}
	
    /**
     * Clear query cache
	 * @param QueryModel $query
	 * @return bool
     */
    public function clearQueryCache(QueryModel $query)
    {
		return $this->cacheStorage->clearQueryCache($query);
    }
}
