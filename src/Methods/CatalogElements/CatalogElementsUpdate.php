<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\CatalogElements;

class CatalogElementsUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalog_elements';
	
    /**
     * Update entitys in CRM
	 * @param array $raws
	 * @param array $arg
     */
    public function update($raws, $arg = [])
    {
		return $this->call(['update' => $raws], $arg);
	}
}
