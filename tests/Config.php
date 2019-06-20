<?php
/**
 * amoCRM API client config for tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests;

abstract class Config
{
	/**
	 * amoCRM API client config for tests
	 * @return array
	 */
    public static function getAccount()
    {
		return [
			'id' => 123,
			'domain' => 'testdomain',
			'login' => 'test@login',
			'hash' => 'testhash'
		];
    }
}