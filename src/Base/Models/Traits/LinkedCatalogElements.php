<?php
/**
 * amoCRM Base trait - linked catalog elements
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedCatalogElements
{
    /**
     * Attach emenet
	 * @param integer $element_id
     * @param integer $catalog_id
     * @param integer $count
     * @return static
     */
    public function attachElement($catalog_id, $element_id, $count = 1)
    {
        $values = $this->catalog_elements_id;
        foreach ($values as $k=>$v) {
            if (!is_array($v)) {
                unset($values[$k]);
            }
        }
		if (!array_key_exists($catalog_id, $values)) {
            $values[$catalog_id] = [];
        }
        $values[$catalog_id][$element_id] = $count;
        $this->catalog_elements_id = $values;
		return $this;
    }
}
