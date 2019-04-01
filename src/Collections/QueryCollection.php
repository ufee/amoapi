<?php
/**
 * amoCRM API Query Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Amoapi,
    Ufee\Amo\Api;

class QueryCollection extends \Ufee\Amo\Base\Collections\Collection
{
    protected 
        $instance,
        $cache_path = '/Cache',
        $_listener,
        $logger = null,
        $_logs = false;
    
    /**
     * Boot instance
	 * @param Amoapi $instance
     */
    public function boot(Amoapi &$instance)
    {
        $this->instance = $instance;
        $this->logger = Api\Logger::getInstance($instance->getAuth('domain').'.log');
        $this->cachePath(AMOAPI_ROOT.$this->cache_path);
    }

    /**
     * Set cache path
	 * @param string $val
     * @return QueryCollection
     */
    public function cachePath($val)
    {
        $this->cache_path = $val.'/'.$this->instance->getAuth('domain');
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0777, true);
        }
        if ($caches = glob($this->cache_path.'/*.cache')) {
            foreach ($caches as $cache_file) {
                if ($cacheQuery = unserialize(file_get_contents($cache_file))) {
                    if ($cacheQuery->getService()->canCache() && microtime(1)-$cacheQuery->end_time <= $cacheQuery->getService()->cacheTime()) {
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
	 * @param Query $query
     * @param bool $save
	 * @return QueryCollection
     */
    public function pushQuery(Api\Query $query, $save = true)
    {
        if ($this->_logs) {
            $this->logger->log(
                '['.$query->method.'] '.$query->url.' -> '.$query->getUrl(),
                $query->headers,
                $query->post_data,
                'Start: '.$query->startDate('H:i:s').' ('.$query->start_time.')',
                'End:   '.$query->endDate('H:i:s').' ('.$query->end_time.')',
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
            if ($query->getService()->canCache()) {
                $this->cacheQuery($query);
            }
        }
        return $this;
	}

    /**
     * Get cached query
	 * @param string $hash
	 * @return Query|null
     */
	public function getCached($hash)
	{    
        $queries = $this->find('hash', $hash)->filter(function(Api\Query $query) {
            return $query->getService()->canCache() && microtime(1)-$query->end_time <= $query->getService()->cacheTime();
        });
        return $queries->first();
    }

    /**
     * Debug queries
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
        array_map('unlink', glob($this->cache_path.'/*.cache'));
        $this->items = [];
        return $this;
    }
    
    /**
     * Cache queries
	 * @param Query $query
	 * @return bool
     */
    public function cacheQuery(Api\Query $query)
    {
        return file_put_contents($this->cache_path.'/'.$query->hash.'.cache', serialize($query));
	}
}
