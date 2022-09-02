<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface MainContact
{
    /**
     * Has linked main contact
     * @param mixed $contact
     * @return bool
     */
    public function hasMainContact($contact = null);

    /**
     * Set linked main contact
     * @param mixed $contact
     * @return bool
     */
    public function attachMainContact($contact);

}