<?php
/**
 * amoCRM API client Transactions service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Base\Collections\Collection;

class Transactions extends \Ufee\Amo\Base\Services\LimitedList
{
	protected static 
		$_require = [
			'add' => ['customer_id', 'price'],
			'update' => ['comment']
		];
	protected
		$entity_key = 'transactions',
		$entity_model = '\Ufee\Amo\Models\Transaction',
		$entity_collection = '\Ufee\Amo\Collections\TransactionsCollection',
		$cache_time = false,
		$methods = [
			'list', 'add', 'update', 'delete'
		];

    /**
     * Add transactions to CRM
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
	 * @return TransactionCollection
     */
	public function transactions()
	{
		return $this->list->recursiveCall();
	}
}
