<?php
/**
 * amoCRM API client Catalogs service
 */
namespace Ufee\Amo\Services;

class Catalogs extends \Ufee\Amo\Base\Services\LimitedList
{
	protected static 
		$_require = [
			'add' => ['name'],
			'update' => ['id']
		];
	protected
		$entity_key = 'catalogs',
		$entity_model = '\Ufee\Amo\Models\Catalog',
		$entity_collection = '\Ufee\Amo\Collections\CatalogCollection',
		$cache_time = false,
		$methods = [
			'list', 'add', 'update', 'delete'
		];
	
    /**
     * Add catalogs to CRM
	 * @param mixed $models
     */
	public function add(&$models)
	{
		$create_models = $models;
		if (!is_array($models)) {
			$create_models = [$models];
		}
		$added_raws = $this->_add($create_models);
		$added = true;
		foreach ($create_models as $k=>&$model) {
			if ($added_raw = $added_raws->get($k)) {
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
     * Get full
	 * @return Collection
     */
	public function catalogs()
	{
		return $this->list->recursiveCall();
	}
}
