<?php
/**
 * amoCRM Transaction model Collection class
 */
namespace Ufee\Amo\Collections;

class TransactionsCollection extends \Ufee\Amo\Collections\ServiceCollection
{
    /**
     * Delete transactions
     */
	public function delete()
	{
        return $this->service->delete(
            $this->all()
        );
    }
}
