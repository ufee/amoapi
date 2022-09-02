<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface EntityDetector
{
    /**
     * Get entity id from another
     * @param mixed $entity
     * @return integer|null
     */
    public function getIdFrom($entity = null);

}