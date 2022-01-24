<?php
/**
 * amoCRM API client Base service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\Base\Models\Traits;
use Ufee\Amo\Base\Collections\Collection;

class LimitedList extends Cached
{
	use Traits\EntityDetector;
	
	protected $entity_collection = '\Ufee\Amo\Base\Collections\ApiModelCollection';
	protected $limit_rows_add = 50;
	protected $limit_rows_update = 50;
	protected $limit_rows = 500;
	protected $max_rows = 0;
	protected $modified_from = false;
	protected $methods = [
		'list', 'add', 'update'
	];
	
    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		$this->api_args = [
			'lang' => $this->instance->getAuth('lang')
		];
	}

    /**
     * Create new model
	 * @param mixed $create_data
	 * @returm Model
     */
	public function create($create_data = null)
	{
		$model_class = $this->entity_model;
		$model_data = [
			'request_id' => mt_rand(), 
			'account_id' => $this->instance->getAuth('id')
		];
		if (is_numeric($create_data)) {
			$create_data = ['id' => $create_data];
		}
		if (is_array($create_data)) {
			foreach ($create_data as $key=>$val) {
				$model_data[$key] = $val;
			}
		}
		$model = new $model_class($model_data, $this);
		return $model;
	}

    /**
     * Add models to CRM
	 * @param mixed $models
     */
	public function add(&$models)
	{
		$create_models = $models;
		if (!is_array($models)) {
			$create_models = [$models];
		}
		$create_parts = [];
        $p = 0;
        $i = 1;
		foreach ($create_models as $create_model) {
            $create_parts[$p][] = $create_model;
            if ($i == $this->limit_rows_add) {
                $i = 1;
                $p++;
            } else {
                $i++;
            }
		}
		$added_raws = new Collection();
		foreach ($create_parts as $part) {
			$added_part = $this->_add($part);
			$added_raws->merge($added_part);
		}
		$added = true;
		foreach ($create_models as &$model) {
			if ($added_raw = $added_raws->find('request_id', $model->request_id)->first()) {
				$model->setId($added_raw->id);
				$model->setQueryHash($added_raw->query_hash);
				$model->saved();
			} else {
				$added = false;
			}
		}
		if (!is_array($models)) {
			if (!isset($create_models[0])) {
				throw new \Exception('Error: empty created models');
			}
			$models = $create_models[0];
		} else {
			$models = $create_models;
		}
		return $added;
	}

    /**
     * Add models part to CRM
	 * @param mixed $create_part
	 * @return Collection
     */
	protected function _add($create_part)
	{
		$raws = [];
		foreach ($create_part as $model) {
			$raw = [
				'request_id' => $model->request_id,
			];
			if (!$model instanceof \Ufee\Amo\Base\Models\ApiModel) {
				throw new \Exception('Error, adding models must be ApiModel instance');
			}
			foreach (static::$_require['add'] as $rfield) {
				if (is_null($model->$rfield)) {
					throw new \Exception('Error, field "'.$rfield.'" is required in '.$model::getBasename());
				}
				if (!$model->hasChanged($rfield)) {
					$raw[$rfield] = $model->$rfield;
				}
			}
			$raws[]= array_merge($raw, $model->getChangedRawApiData());
		}
		return $this->add->add($raws);
	}

    /**
     * Update models in CRM
	 * @param mixed $models
     */
	public function update(&$models)
	{
		$update_models = $models;
		if (!is_array($models)) {
			$update_models = [$models];
		}
		$update_parts = [];
        $p = 0;
        $i = 1;
		foreach ($update_models as $update_model) {
            $update_parts[$p][] = $update_model;
            if ($i == $this->limit_rows_update) {
                $i = 1;
                $p++;
            } else {
                $i++;
            }
		}
		$updated_raws = new Collection();
		foreach ($update_parts as $part) {
			$updated_part = $this->_update($part);
			$updated_raws->merge($updated_part);
		}
		$updated = true;
		foreach ($update_models as &$model) {
			if ($updated_raw = $updated_raws->find('id', $model->id)->first()) {
				$model->setId($updated_raw->id);
				$model->setQueryHash($updated_raw->query_hash);
				$model->saved();
			} else {
				$updated = false;
			}
		}
		if (!is_array($models)) {
			if (!isset($update_models[0])) {
				throw new \Exception('Error: empty updated models');
			}
			$models = $update_models[0];
		} else {
			$models = $update_models;
		}
		return $updated;
	}

    /**
     * Update models part in CRM
	 * @param mixed $update_part
	 * @return Collection
     */
	protected function _update($update_part)
	{
		$raws = [];
		foreach ($update_part as $model) {
			$raw = [];
			if (!$model instanceof \Ufee\Amo\Base\Models\ApiModel) {
				throw new \Exception('Error, updating models must be ApiModel instance');
			}
			foreach (static::$_require['update'] as $rfield) {
				if (is_null($model->$rfield)) {
					throw new \Exception('Error, field "'.$rfield.'" is required in '.$model::getBasename());
				}
				if (!$model->hasChanged($rfield)) {
					$raw[$rfield] = $model->$rfield;
				}
			}
			$raws[]= array_merge($raw, $model->getChangedRawApiData());
		}
		return $this->update->update($raws);
	}

    /**
     * Update from raw data
	 * @param array $raw
	 * @return Collection
     */
	public function updateRaw(Array $raw)
	{
		return $this->update->update($raw);
	}

    /**
     * Delete models
	 * @param mixed $models
     */
	public function delete($models)
	{
		if (!is_array($models)) {
			$models = [$models];
		}
		$ids = [];
		foreach ($models as $model) {
			if ($trans_id = $this->getIdFrom($model)) {
				$ids[]= $trans_id;
			}
		}
		if (count($ids) == 0) {
			return null;
		}
		$deleted = $this->delete->delete($ids);
		if ($deleted->count() === count($ids)) {
			return true;
		}
		return false;
	}
	
    /**
     * Request arg set
	 * @param string $key
	 * @param mixed $value
     */
    public function where($key, $value = null)
    {
		return $this->list->where($key, $value);
	}
	
    /**
     * Set limit rows
	 * @param integer $count
	 * @return Service
     */
	public function limitRows($count)
	{
		$this->limit_rows = (int)$count;
		return $this;
	}
	
    /**
     * Set max rows
	 * @param integer $count
	 * @return Service
     */
	public function maxRows($count)
	{
		$this->max_rows = (int)$count;
		return $this;
	}
	
    /**
     * Set max rows
	 * @param mixed $val
	 * @return Service
     */
	public function modifiedFrom($val)
	{
		if (!is_bool($val) && !is_string($val) && !is_numeric($val)) {
			throw new \Exception('Modified value must be in: numeric timestamp, date string, bool false');
		}
		if ($val === false) {
			$this->modified_from = $val;
		} else if (is_numeric($val)) {
			$this->modified_from = $val;
		} else if (is_string($val)) {
			$this->modified_from = strtotime(date($val));
		}
		return $this;
	}

    /**
     * Get all models
	 * @return Collection
     */
	public function listing()
	{
		return $this->list->recursiveCall();
	}
	
    /**
     * Get models by id
	 * @param integer|array $id
	 * @return Model|Collection
     */
	public function find($id)
	{
		if (is_array($id) && count($id) === 0) {
			throw new \Exception('Model ids should not be empty');
		}
		$result = $this->list->where('limit_rows', is_array($id) ? count($id) : 1)
							 ->where('limit_offset', 0)
							 ->where('id', $id)
							 ->call();
		if (is_array($id)) {
			return $result;
		}
		if (!$model = $result->get(0)) {
			return null;
		}
		return $model;
	}

    /**
     * Get model limited list
	 * @return Collection
     */
	public function call()
	{
		return $this->list->call();
	}

    /**
     * Get model unlimited list
	 * @return Collection
     */
	public function recursiveCall()
	{
		return $this->list->recursiveCall();
	}
}
