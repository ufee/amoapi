<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedContacts
{
    /**
     * Attach contacts
     * @param array $contacts
     * @return static
     */
    public function attachContacts($contacts);

    /**
     * Attach contact
     * @param mixed $contact
     * @return static
     */
    public function attachContact($contact);

    /**
     * Has linked contacts
     * @param mixed $contacts
     * @return bool
     */
    public function hasContacts($contacts = null);

    /**
     * Linked contacts get method
     * @return ContactsList
     */
    public function contacts();

}