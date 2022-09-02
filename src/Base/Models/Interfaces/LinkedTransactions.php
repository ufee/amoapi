<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedTransactions
{
    /**
     * Attach leads
     * @param array $leads
     * @return static
     */
    public function attachLeads($leads);
    /**
     * Attach lead
     * @param mixed $lead
     * @return static
     */
    public function attachLead($lead);

    /**
     * Has linked leads
     * @param mixed $leads
     * @return bool
     */
    public function hasLeads($leads = null);

    /**
     * Linked transactions get method
     * @return LeadsList
     */
    public function transactions();

}