<?php
/**
 * amoCRM Company model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Company extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedLeads, Traits\LinkedContacts, Traits\LinkedTasks, Traits\LinkedNotes, Traits\EntityDetector, Traits\LinkedTags;

	protected static 
		$cf_category = 'companies',
		$_type = 'company',
		$_type_id = 3;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'tags',
			'leads',
			'customFields',
			'contacts',
			'customers',
			'notes',
			'tasks'
		],
		$writable = [
			'name',
			'created_at',
			'created_by',
			'responsible_user_id',
			'contacts_id',
			'leads_id',
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
		$this->attributes['leads_id'] = [];
		if (isset($data->leads->id)) {
			$this->attributes['leads_id'] = $data->leads->id;
		}
		$this->attributes['leads'] = null;

		$this->attributes['contacts_id'] = [];
		if (isset($data->contacts->id)) {
			$this->attributes['contacts_id'] = $data->contacts->id;
		}
		$this->attributes['contacts'] = null;
		unset(
			$this->attributes['customers']->_links
		);
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		$fields['contacts_id'] = [];
		if (isset($this->attributes['contacts']->id)) {
			$fields['contacts_id'] = $this->contacts->id;
		}
		$fields['leads_id'] = [];
		if (isset($this->attributes['leads']->id)) {
			$fields['leads_id'] = $this->leads->id;
		}
		$fields['tags'] = $this->attributes['tags'];
		return $fields;
    }
}
