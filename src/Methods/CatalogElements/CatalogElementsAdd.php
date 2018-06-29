<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\CatalogElements;

class CatalogElementsAdd extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalog_elements';
	
    /**
     * Add entitys to CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function add($raws, $arg = [])
    {
		return $this->call(['add' => $raws], $arg);
	}
}
