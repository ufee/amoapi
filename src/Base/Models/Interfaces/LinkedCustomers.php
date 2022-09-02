<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedCustomers
{
    /**
     * Attach customers
     * @param array $customers
     * @return static
     */
    public function attachCustomers($customers);

    /**
     * Attach customer
     * @param mixed $customer
     * @return static
     */
    public function attachCustomer($customer);

    /**
     * Has linked customers
     * @param mixed $customers
     * @return bool
     */
    public function hasCustomers($customers = null);

    /**
     * Linked customers get method
     * @return CustomersList
     */
    public function customers();

}