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
		$code;
		
    /**
     * Constructor
	 * @param string $data
	 * @param Query $query
     */
    public function __construct($data, QueryModel &$query)
    {
		$this->query = $query;
		$this->data = $data;
		$this->code = curl_getinfo($query->curl, CURLINFO_HTTP_CODE);
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
}
