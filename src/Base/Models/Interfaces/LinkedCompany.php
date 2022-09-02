<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedCompany
{
    /**
     * Set linked company
     * @param mixed $company
     * @return bool
     */
    public function attachCompany($company);

    /**
     * Has linked company
     * @param mixed $company
     * @return bool
     */
    public function hasCompany($company = null);

    /**
     * Linked company get method
     * @return CompaniesList
     */
    public function company();

}