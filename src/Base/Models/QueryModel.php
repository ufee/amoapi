<?php
/**
 * amoCRM Base API Query model
 */
namespace Ufee\Amo\Base\Models;
use Ufee\Amo\Amoapi,
	Ufee\Amo\Base\Services\Service;

class QueryModel
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
	 * @param Amoapi $instance
	 * @param string $service_class
     */
    public function __construct(Amoapi &$instance, $service_class = '')
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
     * Model on load
	 * @return void
     */
	protected function _boot()
	{
		$this->attributes['headers'] = [];
		$this->attributes['method'] = 'GET';
		$this->attributes['post_data'] = [];
		$this->attributes['json_data'] = [];
		$this->setCurl();
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
			CURLOPT_USERAGENT => 'Amoapi v.'.$instance::VERSION.' ('.$instance->getAuth('domain').'/'.$instance->getAuth('zone').')',
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
		]);
		if ($instance->hasSession()) {
			curl_setopt($this->curl, CURLOPT_COOKIE, 'session_id='.$instance->session['id']);
		} else {
			curl_setopt($this->curl, CURLOPT_COOKIEJAR, $instance->queries->getCookiePath());
			$this->setArgs([
				'USER_LOGIN' => $instance->getAuth('login'),
				'USER_HASH' => $instance->getAuth('hash')
			]);
		}
		return $this;
	}

    /**
     * Set query method
     * @param string $method
     */
    public function setMethod($method = 'GET')
    {
		$this->attributes['method'] = $method;
		return $this;
    }
    
    /**
     * Set query args
     * @param array $args
     */
    public function setArgs($args = [])
    {
		foreach ($args as $key=>$val) {
			$this->attributes['args'][$key] = $val;
		}
		return $this;
	}
	
    /**
     * Reset query args
     * @param array $args
     */
    public function resetArgs($args = [])
    {
		if (empty($args)) {
			$this->attributes['args'] = [];
		} else {
			foreach ($args as $key) {
				if (isset($this->attributes['args'][$key])) {
					unset($this->attributes['args'][$key]);
				}
			}
		}
		return $this;
    }

    /**
     * Set post query data
     * @param array $data
     */
    public function setPostData($data = [])
    {
		foreach ($data as $key=>$val) {
			$this->attributes['post_data'][$key] = $val;
		}
		return $this;
	}
	
    /**
     * Set post json data
     * @param array $data
     */
    public function setJsonData($data = [])
    {
		foreach ($data as $key=>$val) {
			$this->attributes['json_data'][$key] = $val;
		}
		$this->setHeader('Content-Type', 'application/json');
		return $this;
    }

    /**
     * Set url link
     * @param string $url
     */
    public function setUrl($url)
    {
		$this->attributes['url'] = $url;
		return $this;
	}
	
    /**
     * Set retry status
     * @param bool $status
     */
    public function setRetry($status)
    {
		$this->attributes['retry'] = (bool)$status;
		return $this;
	}
	
    /**
     * Set headers
     * @param string $name
	 * @param mixed $value
     */
    public function setHeader($name, $value)
    {
		$this->attributes['headers'][$name] = $value;
		return $this;
	}

    /**
     * Get headers
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->headers as $name=>$value) {
            $headers[]= $name.': '.$value;
        }
		return $headers;
	}

    /**
     * Get url link
	 * @return string
     */
    public function getUrl()
    {
        $url = $this->url;
        if ($this->args) {
            $url .= '?' . http_build_query($this->args);
        }
        return 'https://'.$this->instance()->getAuth('domain').'.amocrm.'.$this->instance()->getAuth('zone').$url;
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
			$this->instance()->getAuth('login').
			$this->instance()->getAuth('hash').
			$this->instance()->getAuth('zone').
			$this->method.
			json_encode($args).
			json_encode($this->post_data).
			json_encode($this->json_data)
		);
	}

    /**
     * Get start date
	 * @return string
     */
    public function startDate($format = 'Y-m-d H:i:s')
    {
        return date($format, $this->start_time);
	}

    /**
     * Get end date
	 * @return string
     */
    public function endDate($format = 'Y-m-d H:i:s')
    {
        return date($format, $this->end_time);
	}

    /**
     * Get query service
	 * @return Service
     */
    public function getService()
    {
		$instance = $this->instance();
		if (!$serviceClass = $this->service) {
			return null;
		}
        if (!$service = $serviceClass::getInstance(null, $instance)) {
			$service = $serviceClass::setInstance(null, $instance);
		}
		return $service;
	}

    /**
     * Get Amoapi instance
	 * @return Amoapi
     */
    public function instance()
    {
		return Amoapi::getInstance($this->account_id);
	}

    /**
     * Protect get model fields
	 * @param string $field
     */
	public function __get($field)
	{
		if (!array_key_exists($field, $this->attributes)) {
			throw new \Exception('Invalid Query field: '.$field);
		}
		return $this->attributes[$field];
	}
	
    /**
     * Protect set model fields
	 * @param string $field
	 * @param string $value
     */
	public function __set($field, $value)
	{
		if (!array_key_exists($field, $this->attributes)) {
			throw new \Exception('Invalid Query field: '.$field);
		}
		throw new \Exception('Protected Query field set fail: '.$field);
		$this->attributes[$field] = $value;
	}

    /**
     * Clear query cache
     * @return QueryModel
     */
    public function clearCache()
    {
		if (file_exists($this->cache_path.'/'.$this->hash.'.cache')) {
			@unlink($this->cache_path.'/'.$this->hash.'.cache');
		}
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = [];
		foreach ($this->attributes as $field_key=>$val) {
			if (in_array($field_key, $this->hidden)) {
				continue;
			}
			$fields[$field_key] = $val;
		}
		return $fields;
    }
	
    /**
     * Close curl
     */
    public function __destruct()
    {
        if (is_resource($this->curl)) {
			curl_close($this->curl);
		}
    }
}