<?php
/**
 * Curl response
 */
namespace Ufee\Amo\Api;

class Response
{
	private
		$query,
		$data;
		
    /**
     * Constructor
	 * @param string $data
	 * @param Query $query
     */
    public function __construct($data, Query &$query)
    {
		$this->query = $query;
		$this->data = $data;
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
	 * @return object
     */
	public function getInfo()
	{
		return (object)curl_getinfo($this->query->curl);
	}
	
    /**
     * Get response code
	 * @return integer
     */
	public function getCode()
	{
		return curl_getinfo($this->query->curl, CURLINFO_HTTP_CODE);
	}
	
    /**
     * Get curl error
	 * @return string
     */
	public function getError()
	{
		return curl_error($this->query->curl);
	}
}
