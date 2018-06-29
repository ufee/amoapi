<?php
/**
 * amoCRM Base trait - catalog elements
 */
namespace Ufee\Amo\Base\Models\Traits;

trait CatalogElements
{
	/**
     * Create linked element model
     * @return CatalogElement
     */
    public function createElement()
    {
		$element = $this->service->instance->catalogElements()->create();
		$element->catalog_id = $this->id;
		return $element;
	}
	
	/**
     * Linked elements get method
     * @return CatalogElementsList
     */
    public function elements()
    {
		$service = $this->service->instance->catalogElements()->where('catalog_id', $this->id);
		return $service;
	}
	
    /**
     * Protect elements access
	 * @param mixed $elements attribute
	 * @return CatalogElementsCollection
     */
    protected function elements_access($elements)
    {
		if (is_null($this->attributes['elements'])) {
			$this->attributes['elements'] = $this->elements()->recursiveCall();
		}
		return $this->attributes['elements'];
	}
}
