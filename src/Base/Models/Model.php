<?php
/**
 * amoCRM Base model
 */
namespace Ufee\Amo\Base\Models;

class Model
{
	protected 
		$system = ['id'],
		$hidden = [],
		$writable = [],
		$attributes = [],
		$changed = [];
		
    /**
     * Constructor
	 * @param mixed $data
     */
    public function __construct($data)
    {
		if (!is_array($data) && !is_object($data)) {
			throw new \Exception(static::getBasename().' model data must be array or object');
		}
        foreach ($this->system as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->hidden as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->writable as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($data as $field_key=>$val) {
			if (array_key_exists($field_key, $this->attributes)) {
				$this->attributes[$field_key] = $val;
			}
		}
		$this->_boot($data);
    }
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		// code...
	}

	/**
     * Has attr field
	 * @param string $field
	 * @return bool
     */
    public function hasAttribute($field)
    {
		return array_key_exists($field, $this->attributes);
	}

	/**
     * Saved data trigger
	 * @param integer $id
	 * @return void
     */
    public function saved()
    {
		$this->changed = [];
	}

	/**
     * Get model changed fields
	 * @return array
     */
    public function changedFields()
    {
		return $this->changed;
	}

	/**
     * Set changed field
	 * @param string $field
	 * @return Model
     */
    public function setChanged($field)
    {
		if (!in_array($field, $this->changed)) {
			$this->changed[]= $field;
		}
		return $this;
	}

	/**
     * Has changed field
	 * @param string $field
	 * @return bool
     */
    public function hasChanged($field)
    {
		return in_array($field, $this->changed);
	}
	
    /**
     * Get model writable fields
	 * @return array
     */
	public function writableFields()
	{
		return array_merge($this->writable, $this->hidden);
	}

    /**
     * Get changed model data
	 * @return array
     */
    public function getChangedData()
    {
		$data = [];
		$changed_fields = $this->changedFields();
		foreach ($changed_fields as $field) {
			$data[$field] = $this->{$field};
		}
		return $data;
	}
	
    /**
     * Get write fields data
     * @return array
     */
    protected function _getWriteAttributes()
    {
		$fields = [];
		$writable = $this->writableFields();
        foreach ($this->attributes as $key=>$val) {
			if (!in_array($key, $writable)) {
				continue;
			}
			$fields[$key] = $val;
		}
		return $fields;
    }
	
    /**
     * Protect get model fields
	 * @param string $field
     */
	public function __get($field)
	{
		if (!array_key_exists($field, $this->attributes)) {
			if (method_exists($this, $field)) {
				return $this->$field();
			}
			throw new \Exception('Invalid '.static::getBasename().' field: '.$field);
		}
		$access_method_name = $field.'_access';
		if (method_exists($this, $access_method_name)) {
			return $this->$access_method_name($this->attributes[$field]);
		}
		return $this->attributes[$field];
	}
	
    /**
     * Protect set model fields
	 * @param string $property
	 * @param string $value
     */
	public function __set($field, $value)
	{
		if (!array_key_exists($field, $this->attributes)) {
			throw new \Exception('Invalid '.static::getBasename().' field: '.$field);
		}
		if (!in_array($field, $this->writable)) {
			throw new \Exception('Protected '.static::getBasename().' field set fail: '.$field);
		}
		if ($this->attributes[$field] !== $value && !in_array($field, $this->changed)) {
			$this->changed[]= $field;
		}
		$protect_method_name = $field.'_protect';
		if (method_exists($this, $protect_method_name)) {
			if ($this->$protect_method_name($value) === false) {
				return false;
			}
		}
		$this->attributes[$field] = $value;
	}
	
    /**
     * Get class basename
	 * @return string
     */
    public static function getBasename()
    {
        return substr(static::class, strrpos(static::class, '\\') + 1);
	}
	
    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = [];
		foreach ($this->attributes as $field_key=>$val) {
			if (in_array($field_key, $this->hidden)) {
				continue;
			}
			$fields[$field_key] = $val;
		}
		return $fields;
    }
}
