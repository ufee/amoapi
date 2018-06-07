<?php
/**
 * amoCRM API Query Collection class
 */
namespace Ufee\Amo\Collections;
use \Ufee\Amo\Api;

class QueryCollection extends \Ufee\Amo\Base\Collections\Collection
{
    protected 
        $_listener,
        $_logs = false;
    
    /**
     * Push new queries
	 * @param Query $query
	 * @return Colection
     */
    public function pushQuery(Api\Query $query)
    {
        array_push($this->items, $query);
        if ($this->_logs) {
            Api\Logger::getInstance($query->instance->getAuth('domain').'.log')->log(
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
        return $this;
	}

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

    /**
     * Debug queries
	 * @param bool $flag
     * @return QueryCollection
     */
    public function logs($flag)
    {
        $this->_logs = (bool)$flag;
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
}
