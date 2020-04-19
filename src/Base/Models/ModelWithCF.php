<?php
/**
 * amoCRM Base model with Custom fiels
 */
namespace Ufee\Amo\Base\Models;
use Ufee\Amo\Models\EntityCustomFields,
	Ufee\Amo\Base\Collections\Collection;

class ModelWithCF extends ApiModel
{
	protected static 
		$_type = '',
		$cf_category = '';
	protected 
		$hidden = [
			'service',
			'custom_fields',
			'customFields'
		];

    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		$this->attributes['customFields'] = null;
		$this->attributes['custom_fields'] = null;
		$cfs = [];
		
		if (isset($data->custom_fields)) {
			foreach($data->custom_fields as $cf) {
				$cfs[$cf->id] = $cf;
			}
			$this->attributes['custom_fields'] = gzencode(serialize($cfs), 7);
		}
		$data = null;
	}
	
    /**
     * Get custom field
	 * @param string $cf_name
	 * @return EntityCustomField
     */
    public function cf($cf_name = null)
    {
		if (is_null($cf_name)) {
			return $this->customFields;
		}
		return $this->customFields->byName($cf_name);
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
			
			if ($this->attributes['custom_fields']) {
				$this->attributes['custom_fields'] = unserialize(gzdecode($this->attributes['custom_fields']));
			} else {
				$this->attributes['custom_fields'] = [];
			}
			foreach ($account_cfields->all() as $cfield) {
				$cf_data = [
					'id' => $cfield->id,
					'account_id' => $this->service->account->id,
					'name' => $cfield->name,
					'code' => isset($this->attributes['custom_fields'][$cfield->id]->code) ? $this->attributes['custom_fields'][$cfield->id]->code : null,
					'values' => isset($this->attributes['custom_fields'][$cfield->id]) ? $this->attributes['custom_fields'][$cfield->id]->values : [],
					'field' => $cfield
				];
				$cf_class = $account_cfields->getClassFrom($cfield);
				$model_cfields->push(new $cf_class($cf_data));
			}
			$this->attributes['customFields'] = new EntityCustomFields($model_cfields);
			$account_cfields = null;
		}
		return $this->attributes['customFields'];
	}

    /**
     * Get changed raw model api data
	 * @return array
     */
    public function getChangedRawApiData()
	{
		$data = parent::getChangedRawApiData();
		$raw = [];
		if (!is_null($this->attributes['customFields']) && $cf_raws = $this->customFields->getChangedApiRaw()) {
			$raw['custom_fields'] = $cf_raws;
			foreach ($cf_raws as $cf_raw) {
				$this->attributes['custom_fields'][$cf_raw['id']] = (object)$this->customFields->byId($cf_raw['id'])->getRaw();
			}
		}
		return array_merge($data, $raw);
	}

    /**
     * Set raw custom_fields
	 * @param array $custom_fields raw
	 * @return ModelWithCF
     */
    public function setCustomFields(array $custom_fields)
	{
		$this->attributes['customFields'] = null;
		$this->attributes['custom_fields'] = [];
		foreach($custom_fields as $cf) {
			$this->attributes['custom_fields'][$cf->id] = $cf;
		}
		$this->customFields->each(function(&$cfield) {
			$cfield->setChanged('values');
		});
		$this->setChanged('custom_fields');
		return $this;
	}

    /**
     * Resfresh changed data of custom fields collection
	 * @return ModelWithCF
     */
    public function refreshCustomFieldChanges()
	{
		if (!is_null($this->attributes['customFields']) && $cf_raws = $this->customFields->getChangedApiRaw()) {
			foreach ($cf_raws as $cf_raw) {
				$this->attributes['custom_fields'][$cf_raw['id']] = (object)$this->customFields->byId($cf_raw['id'])->getRaw();
			}
		}
		return $this;
	}

	/**
     * Saved data trigger
	 * @return void
     */
    public function saved()
    {
		parent::saved();
		if (!is_null($this->attributes['customFields'])) {
			$this->customFields->each(function(&$cfield) {
				$cfield->saved();
			});
		}
	}

    /**
     * Get Model type
     * @return array
     */
    public static function getType()
    {
		return static::$_type;
	}

    /**
     * Get hash from model fields
	 * @return object
     */
    public function getHash()
    {
		$fields = $this->toArray();
		$fields['custom_fields'] = [];
		$this->customFields->each(function(&$cfield) use(&$fields) {
			$fields['custom_fields'][$cfield->id] = $cfield->getValues();
		});
		return md5(
			json_encode($fields)
		);
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$this->refreshCustomFieldChanges();
		$fields = parent::toArray();
		return $fields;
    }
}
