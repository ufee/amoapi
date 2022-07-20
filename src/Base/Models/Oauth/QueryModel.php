<?php
/**
 * amoCRM Base API oauth Query model
 */
namespace Ufee\Amo\Base\Models\Oauth;
use Ufee\Amo\Oauthapi,
	Ufee\Amo\Base\Services\Service;

class QueryModel extends \Ufee\Amo\Base\Models\QueryModel
{
	protected 
		$system = [
			'url',
			'method',
			'args',
			'post_data',
			'json_data',
			'retry',
			'start_time',
			'end_time',
			'execution_time',
			'sleep_time',
			'hash',
			'memory_usage',
			'headers'
		],
		$hidden = [
			'account_id',
			'service',
			'curl',
			'cookie_file',
			'response'
		],
		$attributes = [],
		$retries = 0,
		$cookie = [];
		
    /**
     * Constructor
	 * @param Oauthapi $instance
	 * @param string $service_class
     */
    public function __construct(Oauthapi &$instance, $service_class = '')
    {
        foreach ($this->system as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->hidden as $field_key) {
			$this->attributes[$field_key] = null;
		}
		$this->attributes['account_id'] = $instance->getAuth('id');
		$this->attributes['service'] = $service_class;
		$this->attributes['retry'] = true;
		$this->_boot();
	}

    /**
     * Set query curl
	 * @return static
     */
	public function setCurl()
	{
		$instance = $this->instance();
		$this->attributes['curl'] = \curl_init();
		curl_setopt_array($this->curl, [
			CURLOPT_AUTOREFERER => true,
			CURLOPT_USERAGENT => 'Amoapi v.'.$instance::VERSION.' ('.$instance->getAuth('lang').'/'.$instance->getAuth('zone').')',
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HEADER => false,
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
		]);
		return $this;
	}

    /**
     * DEBUG curl query
	 * @param resource $fp - fopen file
     * @return Query
     */
    public function verbose($fp)
    {
		curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
		curl_setopt($this->curl, CURLOPT_STDERR, $fp);
		return $this;
	}

    /**
     * Generate query hash
	 * @return string
     */
    public function generateHash()
    {
		$args = $this->args;
		unset($args['USER_LOGIN'], $args['USER_HASH']);
        return $this->attributes['hash'] = md5(
			$this->instance()->getAuth('domain').
			$this->instance()->getAuth('client_id').
			$this->instance()->getAuth('zone').
			$this->method.
			json_encode($args).
			json_encode($this->post_data).
			json_encode($this->json_data)
		);
	}

    /**
     * Clear query cache
     * @return QueryModel
     */
    public function clearCache()
    {
		if (file_exists($this->cache_path.'/'.$this->hash.'.Oauthapi.cache')) {
			@unlink($this->cache_path.'/'.$this->hash.'.Oauthapi.cache');
		}
	}

    /**
     * Get Oauthapi instance
	 * @return Oauthapi
     */
    public function instance()
    {
		return Oauthapi::getInstance($this->account_id);
	}
}