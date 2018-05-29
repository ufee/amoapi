<?php
/**
 * amoCRM Customer model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Customer extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedContacts, Traits\MainContact, Traits\LinkedCompany, Traits\LinkedTasks, Traits\LinkedNotes, Traits\EntityDetector;

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
			'tags',
			'customFields',
			'company',
			'contacts',
			'tasks',
			'notes'
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
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		$fields['tags'] = $this->attributes['tags'];
		return $fields;
    }
}
