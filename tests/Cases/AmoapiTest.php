<?php
/**
 * amoCRM API client tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Amoapi,
	Ufee\Amo\Services,
	Ufee\Amo\Models;
	
require_once __DIR__ . '/../TestCase.php';

class AmoapiTest extends \Tests\TestCase
{
    public function testGetAccountService()
    {
		Assert::assertInstanceOf(
			Services\Account::class,
			$this->amo->account()
		);
    }
	
    public function testGetAccountCurrentMoodel()
    {
		Assert::assertInstanceOf(
			Models\Account::class,
			$this->amo->account
		);
    }
}
