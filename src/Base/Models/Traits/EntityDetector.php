<?php
/**
 * amoCRM Base trait - detect entity
 */
namespace Ufee\Amo\Base\Models\Traits;

trait EntityDetector
{
    /**
     * Get entity id from another
	 * @param mixed $entity
     * @return integer|null
     */
    public function getIdFrom($entity = null)
    {
		if (is_numeric($entity)) {
			return $entity;
		}
		if (is_object($entity)) {
			return $entity->id;
		}
		if (is_array($entity)) {
			return $entity['id'];
		}
		return null;
	}
}
