<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedParents
{
    /**
     * Has linked contact
     * @param mixed $contact
     * @return bool
     */
    public function hasLinkedContact($contact = null);

    /**
     * Has linked lead
     * @param mixed $lead
     * @return bool
     */
    public function hasLinkedLead($lead = null);

    /**
     * Has linked company
     * @param mixed $company
     * @return bool
     */
    public function hasLinkedCompany($company = null);

}