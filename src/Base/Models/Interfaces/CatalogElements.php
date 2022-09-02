<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface CatalogElements
{
    /**
     * Create linked element model
     * @return CatalogElement
     */
    public function createElement();

    /**
     * Linked elements get method
     * @return CatalogElementsList
     */
    public function elements();

}