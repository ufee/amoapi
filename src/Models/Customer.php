<?php
/**
 * amoCRM Customer model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Customer extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedContacts, Traits\MainContact, Traits\LinkedCompany, Traits\LinkedTasks, Traits\LinkedNotes, Traits\EntityDetector, Traits\LinkedTags;

	protected static 
		$cf_category = 'customers',
		$_type = 'customer',
		$_type_id = 12;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'updatedUser',
			'tags',
			'customFields',
			'company',
			'contacts',
			'tasks',
			'notes',
			'transactions'
		],
		$writable = [
			'name',
			'next_date',
			'next_price',
			'period_id',
			'created_at',
			'created_by',
			'responsible_user_id',
			'company_id',
			'contacts_id',
			'main_contact_id',
			'updated_at',
			'updated_by',
			'closest_task_at'
		];
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);

		$this->attributes['tags'] = [];
		if (isset($data->tags)) {
			foreach ($data->tags as $tag) {
				$this->attributes['tags'][]= $tag->name;
			}			
		}
		$this->attributes['company_id'] = null;
		if (isset($data->company->id)) {
			$this->attributes['company_id'] = $data->company->id;
		}
		$this->attributes['company'] = null;

		$this->attributes['contacts_id'] = [];
		if (isset($data->contacts->id)) {
			$this->attributes['contacts_id'] = $data->contacts->id;
		}
		$this->attributes['contacts'] = null;

		$this->attributes['main_contact_id'] = null;
		if (isset($data->main_contact->id)) {
			$this->attributes['main_contact_id'] = $data->main_contact->id;
		}
		unset(
			$this->attributes['main_contact']
		);
	}

	/**
     * Create linked transaction model
     * @return Transaction
     */
    public function createTransaction()
    {
		$transaction = $this->service->instance->transactions()->create();
		$transaction->attachCustomer($this);
		return $transaction;
	}

	/**
     * Linked transactions get method
     * @return TransactionsList
     */
    public function transactions()
    {
		return $this->service->instance->transactions()->where('customer_id', $this->id);
	}

    /**
     * Protect transactions access
	 * @param mixed $transactions attribute
	 * @return TransactionsCollection
     */
    public function transactions_access($transactions)
    {
		if (is_null($this->attributes['transactions'])) {
			$this->attributes['transactions'] = $this->service->instance->transactions()->where('customer_id', $this->id)->recursiveCall();
		}
		return $this->attributes['transactions'];
	}

    /**
     * Delete model
     * @return array
     */
    public function delete()
    {
		return $this->service->delete($this);
    }

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		return $fields;
    }
}
