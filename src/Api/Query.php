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
        $this->attributes['retries']++;
        $instance = $this->instance();
        $last_time = 1;
        if ($last_query = $instance->queries->last()) {
            $last_time = $last_query->start_time;
        }
        $current_time = microtime(true);
        $time_offset = $current_time-$last_time;
        $delay = $instance->queries->getDelay();
        if ($delay > $time_offset) {
            $sleep_time = ($delay-$time_offset)*1000000;
            usleep($sleep_time);
            $this->attributes['sleep_time'] = $sleep_time/1000000;
        }
		$this->attributes['start_time'] = microtime(true);
		$method = strtolower($this->method);
        $this->attributes['response'] = new Response(
        	$this->$method(), $this
        );
        curl_close($this->curl);
		$this->attributes['curl'] = null;
		$code = $this->response->getCode();
		$instance->queries->pushByCode($code, $this);
		
        if (!in_array($code, [200, 204]) && file_exists($instance->queries->getCookiePath())) {
           @unlink($instance->queries->getCookiePath());
        }
        if ($code == 401 && $this->url != $instance::AUTH_URL && $this->retries <= 3 && $instance->hasAutoAuth()) {
            $instance->authorize();
            $instance->queries->refreshSession();
            $this->setCurl();
            return $this->execute();
        }
		if ($code == 429 && $this->retries <= 24) {
			sleep(1);
			return $this->setCurl()->execute();
		}
		if (in_array($code, [502,504]) && $this->retry) {
			sleep(1);
            $this->setCurl();
			$this->setRetry(false);
            return $this->execute();
		}
        $this->attributes['end_time'] = microtime(true);
        $this->attributes['execution_time'] = round($this->end_time - $this->start_time, 5);
        $this->attributes['memory_usage'] = memory_get_peak_usage(true)/1024/1024;
        $this->generateHash();
        $instance->queries->pushQuery($this, in_array($code, [200]));

		if (!$instance->hasSession() && in_array($code, [200, 204])) {
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
        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        
        return curl_exec($this->curl);
    }

    /**
     * POST query
     * @return Query
     */
    private function post()
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($this->curl, CURLOPT_POST, true);
		
		if (!empty($this->attributes['json_data'])) {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->json_data));
		} else {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->post_data));
        }
        return curl_exec($this->curl);
    }

    /**
     * PATCH query
     * @return Query
     */
    private function patch()
    {
		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
    	curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->json_data));
        
        return curl_exec($this->curl);
    }
}
