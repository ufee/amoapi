<?php
/**
 * amoCRM API client Base service method
 */
namespace Ufee\Amo\Base\Methods;

class Method
{
	protected
		$method,
		$service,
		$args = [];
		
    /**
     * Constructor
	 * @param $service
     */
    public function __construct(\Ufee\Amo\Base\Services\Service &$service)
    {
        $this->service = $service;
    }
}
