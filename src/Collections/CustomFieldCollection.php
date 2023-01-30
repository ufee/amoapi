<?php
/**
 * amoCRM Model Custom field Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\CustomField;

class CustomFieldCollection extends CollectionWrapper
{
    const FIELD_CLASSES = [
        1 => 'Ufee\Amo\Base\Models\CustomField\TextField',
        2 => 'Ufee\Amo\Base\Models\CustomField\NumericField',
        3 => 'Ufee\Amo\Base\Models\CustomField\CheckboxField',
        4 => 'Ufee\Amo\Base\Models\CustomField\SelectField',
        5 => 'Ufee\Amo\Base\Models\CustomField\MultiSelectField',
        6 => 'Ufee\Amo\Base\Models\CustomField\DateField',
        7 => 'Ufee\Amo\Base\Models\CustomField\UrlField',
        8 => 'Ufee\Amo\Base\Models\CustomField\MultiTextField',
        9 => 'Ufee\Amo\Base\Models\CustomField\TextareaField',
        10 => 'Ufee\Amo\Base\Models\CustomField\RadioButtonField',
        11 => 'Ufee\Amo\Base\Models\CustomField\StreetAddressField',
        13 => 'Ufee\Amo\Base\Models\CustomField\SmartAddressField',
        14 => 'Ufee\Amo\Base\Models\CustomField\BirthDayField',
        15 => 'Ufee\Amo\Base\Models\CustomField\JurField',
		16 => 'Ufee\Amo\Base\Models\CustomField\ItemsField',
        17 => 'Ufee\Amo\Base\Models\CustomField\OrgField',
        18 => 'Ufee\Amo\Base\Models\CustomField\CategoryField',
        19 => 'Ufee\Amo\Base\Models\CustomField\CalendarField',
		20 => 'Ufee\Amo\Base\Models\CustomField\NumericField',
		21 => 'Ufee\Amo\Base\Models\CustomField\TextField',
		23 => 'Ufee\Amo\Base\Models\CustomField\NumericField',
		24 => 'Ufee\Amo\Base\Models\CustomField\ChainedList',
		25 => 'Ufee\Amo\Base\Models\CustomField\FileField'
	];
	
    /**
     * Constructor
	 * @param array $elements
	 * @param Account $account
     */
    public function __construct(Array $elements, \Ufee\Amo\Models\Account &$account)
    {
		$this->collection = new \Ufee\Amo\Base\Collections\Collection($elements);
		$client_id = $account->service->instance->getAuth('id');
		$this->collection->each(function(&$item) use(&$client_id) {
			$item->client_id = $client_id;
			$item = new CustomField($item);
		});
		$this->attributes['account'] = $account;
	}

    /**
     * Get cf classname
	 * @param CustomField $cfield
	 * @return string
     */
    public function getClassFrom(CustomField $cfield)
    {
        if (!array_key_exists($cfield->field_type, self::FIELD_CLASSES)) {
            return 'Ufee\Amo\Base\Models\CustomField\EntityField';
        }
        return self::FIELD_CLASSES[$cfield->field_type];
    }
}
