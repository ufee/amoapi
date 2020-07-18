<?php
/**
 * amoCRM Catalog element model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Collections\Collection,
	Ufee\Amo\Base\Models\Traits;

class CatalogElement extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedLeads, Traits\EntityDetector;

	protected static 
		$cf_category = 'catalogs',
		$_type = 'catalog_element',
		$_type_id = -1;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'updatedUser',
			'is_deleted',
			'customFields',
			'leads',
			'catalog',
			'customers'
		],
		$writable = [
			'name',
			'created_at',
			'created_by',
			'catalog_id',
			'leads_id',
			'updated_at',
			'updated_by'
		];
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);

		$this->attributes['leads_id'] = [];
		if (isset($data->leads->id)) {
			$this->attributes['leads_id'] = $data->leads->id;
		}
		$this->attributes['leads'] = null;
		unset(
			$this->attributes['customers']->_links
		);
	}

    /**
     * Protect customFields access
	 * @param mixed $customFields attribute
	 * @return EntityCustomFields
     */
    protected function customFields_access($customFields)
    {
		if (is_null($this->attributes['customFields'])) {
			
			$model_cfields = new Collection([]);
			$account_cfields = $this->service->account->customFields->{static::$cf_category};
			$curr_custom_fields = $this->custom_fields;
			
			if (!$catalogFields = $account_cfields->get($this->catalog_id)) {
				throw new \Exception('Error init custom fields from catalog: '.$this->catalog_id);
			}
			foreach ($catalogFields->all() as $cfield) {
				$cf_data = [
					'id' => $cfield->id,
					'account_id' => $this->service->account->id,
					'name' => $cfield->name,
					'values' => isset($curr_custom_fields[$cfield->id]) ? $curr_custom_fields[$cfield->id]->values : [],
					'field' => $cfield
				];
				$cf_class = $catalogFields->getClassFrom($cfield);
				$model_cfields->push(new $cf_class($cf_data));
			}
			$this->attributes['customFields'] = new EntityCustomFields($model_cfields);
		}
		return $this->attributes['customFields'];
	}

    /**
     * Protect catalog access
	 * @param mixed $catalog attribute
	 * @return Catalog
     */
    public function catalog_access($catalog)
    {
		if (is_null($this->attributes['catalog'])) {
			$this->attributes['catalog'] = $this->service->instance->catalogs()->find($this->catalog_id);
		}
		return $this->attributes['catalog'];
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
