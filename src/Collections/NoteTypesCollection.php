<?php
/**
 * amoCRM Note types model Collection class
 */
namespace Ufee\Amo\Collections;

class NoteTypesCollection extends \Ufee\Amo\Base\Collections\Collection
{
    /**
     * Constructor
	   * @param array $elements
	   * @param Account $account
     */
    public function __construct(Array $elements = [], \Ufee\Amo\Models\Account &$account)
    {
      $this->collection = new \Ufee\Amo\Base\Collections\Collection($elements);
      $this->attributes['account'] = $account;
    }
}
