<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Tasks;

class TasksUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/tasks';
	
    /**
     * Update entitys in CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function update($raws, $arg = [])
    {
		return $this->call(['update' => $raws], $arg);
	}
}
