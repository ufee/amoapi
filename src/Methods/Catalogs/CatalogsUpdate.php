<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Catalogs;

class CatalogsUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalogs';
	
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
