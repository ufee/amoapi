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
			'id' => 8838928,
			'domain' => 'dlatestov',
			'login' => 'mery131@yandex.ru',
			'hash' => 'ee8d12668c60e1a6bbda4130352093c1'
		];
    }
}
