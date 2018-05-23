<?php
/**
 * amoCRM API client default test class
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests;

use Ufee\Amo\Amoapi;

require_once __DIR__ . '/Config.php';

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
    * @var Amoapi $amo
    */
	protected $amo;
	
    public function setUp()
    {
		$this->amo = Amoapi::setInstance(
			Config::getAccount()
		);
    }
}
