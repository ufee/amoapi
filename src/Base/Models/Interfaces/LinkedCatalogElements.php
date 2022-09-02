<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedCatalogElements
{
    /**
     * Attach emenet
     * @param integer $element_id
     * @param integer $catalog_id
     * @param integer $count
     * @return static
     */
    public function attachElement($catalog_id, $element_id, $count = 1);

}