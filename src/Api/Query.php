<?php
/**
 * amoCRM API Query model
 */
namespace Ufee\Amo\Api;
use Ufee\Amo\Base\Models\QueryModel;

class Query extends QueryModel
{
    /**
     * Execute request
     * @return Query
     */
    public function execute()
    {
        $last_time = 1;
        if ($last_query = $this->instance()->queries->last()) {
            $last_time = $last_query->start_time;
        }
        $current_time = microtime(true);
        $time_offset = $current_time-$last_time;
        if ($this->latency > $time_offset) {
            $sleep_time = ($this->latency-$time_offset)*1000000;
            usleep($sleep_time);
            $this->attributes['sleep_time'] = $sleep_time/1000000;
        }
        $this->attributes['start_time'] = microtime(true);
        $this->attributes['response'] = new Response(
            $this->method == 'POST' ? $this->post() : $this->get(), $this
        );
        $this->attributes['end_time'] = microtime(true);
        $this->attributes['execution_time'] = round($this->end_time - $this->start_time, 5);
        $this->attributes['memory_usage'] = memory_get_peak_usage(true)/1024/1024;
        $this->generateHash();
        if (in_array($this->response->getCode(), [200])) {
            $this->instance()->queries->pushQuery($this);
        }
        return $this;
    }

    /**
     * GET query
     * @return Query
     */
    private function get()
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
        return curl_exec($this->curl);
    }

    /**
     * POST query
     * @return Query
     */
    private function post()
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->post_data));
        return curl_exec($this->curl);
    }
}
