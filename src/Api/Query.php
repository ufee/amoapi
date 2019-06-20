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
        $instance = $this->instance();
        $last_time = 1;
        if ($last_query = $instance->queries->last()) {
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
        curl_close($this->curl);
        if (!in_array($this->response->getCode(), [200, 204]) && file_exists($instance->queries->getCookiePath())) {
           @unlink($instance->queries->getCookiePath());
        }
        if ($this->response->getCode() == 401 && $this->url != $instance::AUTH_URL && $instance->hasAutoAuth()) {
            $instance->authorize();
            $instance->queries->refreshSession();
            $this->setCurl();
            return $this->execute();
        }
        $this->attributes['end_time'] = microtime(true);
        $this->attributes['execution_time'] = round($this->end_time - $this->start_time, 5);
        $this->attributes['memory_usage'] = memory_get_peak_usage(true)/1024/1024;
        $this->generateHash();
        $instance->queries->pushQuery($this, in_array($this->response->getCode(), [200]));

		if (!$instance->hasSession() && in_array($this->response->getCode(), [200, 204])) {
            $instance->queries->refreshSession();
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
