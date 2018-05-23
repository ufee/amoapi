<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\Leads;

class LeadsUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/leads';
	
    /**
     * Update entitys in CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function update($raws, $arg = [])
    {
		return $this->call(['update' => $raws], $arg);
	}
}
