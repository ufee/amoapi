<?php
/**
 * amoCRM CatalogElement model Collection class
 */
namespace Ufee\Amo\Collections;

class CatalogElementCollection extends \Ufee\Amo\Collections\ServiceCollection
{
    /**
     * Delete elements
     */
	public function delete()
	{
        return $this->service->delete(
            $this->all()
        );
    }
}
