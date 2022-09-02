<?php
/**
 * amoCRM Catalog model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\CatalogElements;
use Ufee\Amo\Base\Models\Traits;

class Catalog extends \Ufee\Amo\Base\Models\ApiModel implements CatalogElements
{
	use Traits\CatalogElements;

	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'sdk_widget_code',
			'sort',
			'widgets',
			'elements'
		],
		$writable = [
			'name',
			'type',
			'can_add_elements',
			'can_link_multiple',
			'can_show_in_cards',
			'created_at',
			'created_by'
		];

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
		$fields['sort'] = $this->attributes['sort'];
		$fields['sdk_widget_code'] = $this->attributes['sdk_widget_code'];
		return $fields;
    }
}
