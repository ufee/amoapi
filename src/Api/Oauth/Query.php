<?php
/**
 * amoCRM API oauth Query model
 */
namespace Ufee\Amo\Api\Oauth;
use Ufee\Amo\Base\Models\Oauth\QueryModel,
	Ufee\Amo\Api\Response;

class Query extends QueryModel
{
    /**
     * Execute request
     * @return Query
     */
    public function execute()
    {
        $this->retries++;
        $instance = $this->instance();
		
		$oauth = $instance->getOauth();
		if (empty($oauth['access_token'])) {
			throw new \Exception('Empty oauth access_token');
		}
		$expire_time = ($oauth['created_at']+$oauth['expires_in'])-time();
		if ($expire_time < 60) {
			$this->instance()->refreshAccessToken();
			$oauth = $instance->getOauth();
		}
		$this->setHeader(
			'Authorization', $oauth['token_type'].' '.$oauth['access_token']
		);
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

        $this->attributes['end_time'] = microtime(true);
        $this->attributes['execution_time'] = round($this->end_time - $this->start_time, 5);
        $this->attributes['memory_usage'] = memory_get_peak_usage(true)/1024/1024;
        $this->generateHash();
        $instance->queries->pushQuery($this, in_array($this->response->getCode(), [200]));
        return $this;
    }

    /**
     * GET query
     * @return Query
     */
    public function get()
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        
        return curl_exec($this->curl);
    }

    /**
     * POST query
     * @return Query
     */
    public function post()
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
    public function patch()
    {
		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl());
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->getHeaders());
    	curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->json_data));
        
        return curl_exec($this->curl);
    }
}
