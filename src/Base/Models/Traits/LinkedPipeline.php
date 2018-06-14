<?php
/**
 * amoCRM Base trait - linked pipeline
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedPipeline
{
	/**
	 * Protect access pipeline
	 * @return Pipeline|null
	 */
	protected function pipeline_access()
	{
		return $this->service->account->pipelines->find('id', $this->attributes['pipeline_id'])->first();
	}
	
	/**
	 * Protect access pipeline status
	 * @return Pipeline|null
	 */
	protected function status_access()
	{
		return $this->pipeline->statuses->find('id', $this->attributes['status_id'])->first();
	}
}
