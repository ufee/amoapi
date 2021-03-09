<?php
/**
 * Curl response
 */
namespace Ufee\Amo\Api;
use \Ufee\Amo\Base\Models\QueryModel;

class Response
{
	private
		$query,
		$data,
		$code,
		$info,
		$error;
		
    /**
     * Constructor
	 * @param string $data
	 * @param Query $query
     */
    public function __construct($data, QueryModel &$query)
    {
		$this->query = $query;
		$this->data = $data;
		$this->info = (object)curl_getinfo($query->curl);
		$this->code = $this->info->http_code;
		
		if ($this->code === 0) {
			$this->error = curl_error($query->curl);
		}
    }
	
    /**
     * Get response content
	 * @return string
     */
	public function getData()
	{
		return $this->data;
	}
	
    /**
     * Get json decoded content
	 * @param bool $arr
	 * @return mixed|null
     */
	public function parseJson($arr = false)
	{
		return json_decode($this->data, $arr);
	}
	
    /**
     * Get response code
	 * @return integer
     */
	public function getCode()
	{
		return $this->code;
	}
	
    /**
     * Get response info
	 * @return object
     */
	public function getInfo()
	{
		return $this->info;
	}
	
    /**
     * Get response error
	 * @return string|null
     */
	public function getError()
	{
		return $this->error;
	}
}
