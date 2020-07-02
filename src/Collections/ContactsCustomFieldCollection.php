<?php
/**
 * amoCRM Contacts Custom field Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\CustomField;

class ContactsCustomFieldCollection extends CustomFieldCollection
{
    const SYS_FIELD_CLASSES = [
        'Телефон' => 'Ufee\Amo\Base\Models\CustomField\PhoneField',
        'Phone' => 'Ufee\Amo\Base\Models\CustomField\PhoneField',
        'Email' =>'Ufee\Amo\Base\Models\CustomField\EmailField',
        'Мгн. сообщения' =>'Ufee\Amo\Base\Models\CustomField\ImField',
        'IM' =>'Ufee\Amo\Base\Models\CustomField\ImField'
    ];
    
    /**
     * Get cf classname
	 * @param CustomField $cfield
	 * @return string
     */
    public function getClassFrom(CustomField $cfield)
    {
        if ($cfield->isSystem() && array_key_exists($cfield->name, self::SYS_FIELD_CLASSES)) {
            return self::SYS_FIELD_CLASSES[$cfield->name];
        }
        return parent::getClassFrom($cfield);
    }
}
