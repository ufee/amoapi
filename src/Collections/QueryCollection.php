<?php
/**
 * amoCRM API Query Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\ApiClient,
    Ufee\Amo\Api,
	Ufee\Amo\Base\Models\QueryModel;

class QueryCollection extends \Ufee\Amo\Base\Collections\Collection
{
    protected 
        $instance,
		$instanceName,
        $delay = 1,
        $cache_path = '/Cache',
        $cookie_file,
        $_listener,
        $logger = null,
        $_logs = false;
    
    /**
     * Boot instance
	 * @param ApiClient $instance
     */
    public function boot(ApiClient &$instance)
    {
        $this->instance = $instance;
		$this->instanceName = substr(strrchr(get_class($instance), "\\"), 1);
        $this->logger = Api\Logger::getInstance($instance->getAuth('domain').'.log');
        $this->cachePath(AMOAPI_ROOT.$this->cache_path);
		
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
		if (file_exists($this->cookie_file) && $cookies = file_get_contents($this->cookie_file)) {
			if (preg_match('#session_id\s(.+)\s#Uis', $cookies, $match) && !empty($match[1])) {
                clearstatcache(true, $this->cookie_file);
                $this->instance->setSession($match[1], filemtime($this->cookie_file));
            }
        }
        return $this;
    }

    /**
     * Set cache path
	 * @param string $val
     * @return QueryCollection
     */
    public function cachePath($val)
    {
		$instanceClass = get_class($this->instance);
        $this->cache_path = $val.'/'.$this->instance->getAuth('domain');
		
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0777, true);
        }
        if ($caches = glob($this->cache_path.'/*.'.$this->instanceName.'.cache')) {
            foreach ($caches as $cache_file) {
                if ($cacheQuery = unserialize(file_get_contents($cache_file))) {
					if (!$instanceClass::hasInstance($cacheQuery->account_id)) {
						continue;
					}
                    $service = $cacheQuery->getService();
                    if ($service::canCache() && microtime(1)-$cacheQuery->end_time <= $service::cacheTime()) {
                        array_push($this->items, $cacheQuery);
                    } elseif(is_file($cache_file)) {
                        @unlink($cache_file);
                    }
                }
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
     * Get cached query
	 * @param string $hash
	 * @return QueryModel|null
     */
	public function getCached($hash)
	{    
        $queries = $this->find('hash', $hash)->filter(function(QueryModel $query) {
            return $query->getService()->canCache() && microtime(1)-$query->end_time <= $query->getService()->cacheTime();
        });
        return $queries->first();
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
     * Flush cache queries
	 * @return QueryCollection
     */
    public function flush()
    {
        array_map('unlink', glob($this->cache_path.'/*.'.$this->instanceName.'.cache'));
        $this->items = [];
        return $this;
    }
    
    /**
     * Cache queries
	 * @param QueryModel $query
	 * @return bool
     */
    public function cacheQuery(QueryModel $query)
    {
        return file_put_contents($this->cache_path.'/'.$query->hash.'.'.$this->instanceName.'.cache', serialize($query));
    }
}
