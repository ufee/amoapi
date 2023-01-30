## AmoCRM API Client
Api клиент для работы с amoCRM  
Поддержка oAuth авторизации начиная с версии 0.9.0.0

## Установка

```
composer require ufee/amoapi
```

## Запуск тестов
Отредактировать vendor/ufee/amoapi/tests/Config.php

```
composer require --dev phpunit/phpunit ^5
vendor/bin/phpunit vendor/ufee/amoapi
```
## Инициализация клиента по oAuth
Хранение oauth токена возможно в нескольких вариантах
#### Файловое хранилище
Используется по умолчанию (/vendor/ufee/amoapi/src/Cache/), можно задать свой путь  
Создается поддиректория: {path}/{domain}/{client_id}.json  
Настоятельно рекомендуется использовать cвой путь для кеширования, в противном случае данные будут УДАЛЕНЫ composer'ом при обновлении на новую версию.
```php
\Ufee\Amo\Oauthapi::setOauthStorage(
	new \Ufee\Amo\Base\Storage\Oauth\FileStorage(['path' => '/full/oauth/path'])
);
```
#### Redis
Поддерживается библиотека [phpredis](https://github.com/phpredis/phpredis)   
Формат ключа: {domain}_{client_id}
```php
$redis = new \Redis();
$redis->connect('/var/run/redis/redis.sock');
$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
$redis->select(5); // switch to specific db

\Ufee\Amo\Oauthapi::setOauthStorage(
	new \Ufee\Amo\Base\Storage\Oauth\RedisStorage(['connection' => $redis])
);
```
#### MongoDB
Поддерживается библиотека [mongodb](https://github.com/mongodb/mongo-php-library)   
```php
$mongo = new \MongoDB\Client('mongodb://host');
$collection = $mongo->selectCollection('database', 'some_collection');

\Ufee\Amo\Oauthapi::setOauthStorage(
	new \Ufee\Amo\Base\Storage\Oauth\MongoDbStorage(['collection' => $collection])
);
```
#### Наследование хранилища
Реализуется класс, наследующий \Ufee\Amo\Base\Storage\Oauth\AbstractStorage
```php
class CustomOauthStorage extends \Ufee\Amo\Base\Storage\Oauth\AbstractStorage
{
	protected function initClient(Oauthapi $client) {
		parent::initClient($client);
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		if ($data = getClientOauthData($key)) {
			static::$_oauth[$key] = $data;
		}
	}
	public function setOauthData(Oauthapi $client, array $oauth) {
		parent::setOauthData($client, $oauth);
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		return setClientOauthData($key, $oauth);
	}
}
\Ufee\Amo\Oauthapi::setOauthStorage(
	new CustomOauthStorage()
);
```
#### Стороннее хранение
Кеширование oauth данных библиотекой не производится
```php
\Ufee\Amo\Oauthapi::setOauthStorage(
	new \Ufee\Amo\Base\Storage\Oauth\AbstractStorage()
);
$amo = \Ufee\Amo\Oauthapi::setInstance([...]);
$first_token = $amo->fetchAccessToken($code);
saveToCustomStorage($first_token);
$last_saved_token = getFromCustomStorage();
$amo->setOauth($last_saved_token);
$amo->onAccessTokenRefresh(function($oauth) {
	saveToCustomStorage($oauth);
});
```
#### Deprecated
Задать свой путь для кеширования oauth данных (устаревший метод)
```php
$amo->setOauthPath('path_to/oauth');
// equal to new method
\Ufee\Amo\Oauthapi::setOauthStorage(
    new \Ufee\Amo\Base\Storage\Oauth\FileStorage(['path' => 'path_to/oauth'])
);
```
#### Получение объекта для работы с конкретным аккаунтом
```php
$amo = \Ufee\Amo\Oauthapi::setInstance([
    'domain' => 'testdomain',
    'client_id' => 'b6cf0658-b19...', // id приложения
    'client_secret' => 'D546t4yRlOprfZ...',
    'redirect_uri' => 'https://site.ru/amocrm/oauth/redirect',
    'zone' => 'ru', // or com
    'timezone' => 'Europe/Moscow',
    'lang' => 'ru' // or en
]);
```
Получение ранее инициализированного объекта по id приложения
```php
$amo = \Ufee\Amo\Oauthapi::getInstance('b6cf0658-b19...');
```
Получение URL авторизации в приложении amoCRM  
Необходимо для извлечения кода авторизации
```php
$first_auth_url = $amo->getOauthUrl($arg = ['mode' => 'popup', 'state' => 'amoapi']);
```
Получение oauth данных - access_token, refresh_token производится единоразово, по коду авторизации  
Полученные данные oauth кешируются в соответствии с выбранным хранилищем  
Применяются при API запросах автоматически  
```php
$oauth = $amo->fetchAccessToken($code);
```
При необходимости можно задать oauth данные принудительно, вручную  
Данные также будут кешированы автоматически в соответствии с выбранным хранилищем 
```php
$amo->setOauth([
	'token_type' => 'Bearer',
	'expires_in' => 86400,
	'access_token' => 'bKSuyc4u6oi...',
	'refresh_token' => 'a89iHvS9uR4...',
	'created_at' => 1597678161
]);
```
Токен доступа обновляется автоматически, если срок действия refresh_token не истек  
При необходимости можно обновить oauth данные по refresh_token принудительно, вручную  
Новые oauth данные также будут кешированы автоматически
```php
$oauth = $amo->refreshAccessToken($refresh_token = null); // при передаче null используются кешированные oauth данные
```
Вызов callback функции при автоматическом обновлении токена доступа
```php
$amo->onAccessTokenRefresh(function($oauth, $query, $response) {
	echo $query->startDate().' - ['.$query->method.'] '.$query->getUrl()."\n";
	print_r($query->post_data);
	echo $query->endDate().' - ['.$response->getCode().'] '. $response->getData(). "\n\n";
	echo "\nRefreshed oauth: \n";
	print_r($oauth);
});
$amo->onAccessTokenRefreshError(function($exc, $query, $response) {
	echo $query->startDate().' - ['.$query->method.'] '.$query->getUrl()."\n";
	print_r($query->post_data);
	echo $query->endDate().' - ['.$response->getCode().'] '. $response->getData(). "\n\n";
	exit('Error refresh token: '.$exc->getMessage().', code: '.$exc->getCode());
});
```
После первичного выполнения метода fetchAccessToken(), можно пользоваться клиентом в обычном режиме  
Повторное выполнение метода fetchAccessToken() или setOauth() необходимо только в случаях, если:
1) Изменились ключи доступа в приложении
2) Изменился поддомен amoCRM аккаунта
3) Истек срок действия refresh_token
4) Получена ошибка авторизации

Рекомендуется убедиться в отсутствии публичного доступа к папке с кешем - /vendor/ufee/amoapi/src/Cache/  
(если используется директория по умолчанию)

Обмен API ключа на код авторизации oAuth
```php
$resp_code = $amo->ajax()->exchangeApiKey(
	$crm_login, 
	$api_hash, 
	$client_id, 
	$client_secret
);
if ($resp_code === 202) {
	// Запрос принят, код авторизации будет отправлен на redirect_uri
}
```
## Инициализация клиента по API-hash
Получение объекта для работы с конкретным аккаунтом
```php
$amo = \Ufee\Amo\Amoapi::setInstance([
    'id' => 123,
    'domain' => 'testdomain',
    'login' => 'test@login',
    'hash' => 'testhash',
    'zone' => 'com', // default: ru
    'timezone' => 'Europe/London', // default: Europe/Moscow
    'lang' => 'en' // default: ru
]);
```
Включение/выключение автоматической авторизации при ошибке 401  
Сессия (cookie) кешируется в файлах
```php
$amo->autoAuth(true); // true/false, рекомендуется true
```

## Работа с клиентом
Включение логирования заросов (Logs/m-Y/domain.log)
```php
$amo->queries->logs(true); // to default path
```
или
```php
$amo->queries->logs('path_to_log/queries'); // to custom path
```
Не более 1 запроса за заданное время, в секундах
```php
$amo->queries->setDelay(0.5); // default: 0.15 sec
```
Запрос /api/v2/account кешируется в файлах, время указывается в секундах
```php
\Ufee\Amo\Services\Account::setCacheTime(1800); // default: 600 sec
```
Пользовательская отладка запросов (обновлено с вводом oAuth)
```php
$amo->queries->onResponseCode(429, function(\Ufee\Amo\Base\Models\QueryModel $query) {
	echo 'Resp code 429, retry '.$query->retries."\n";
});
$amo->queries->listen(function(\Ufee\Amo\Base\Models\QueryModel $query) {
    $code = $query->response->getCode();
    echo $query->startDate().' - ['.$query->method.'] '.$query->getUrl()."\n";
    print_r($query->headers);
    if ($code === 0) {
        echo $query->response->getError()."\n\n";
    } else {
        print_r(count($query->json_data) ? $query->json_data : $query->post_data);
        echo $query->endDate().' - ['.$query->response->getCode().'] '.$query->response->getData()."\n\n";
    }
});
```
## Кеширование запросов
Хранение кеша возможно в нескольких вариантах
#### Файловое хранилище
Используется по умолчанию (/vendor/ufee/amoapi/src/Cache/), можно задать свой путь  
Создаются кеш-файлы: {path}/{domain}_{hash}.cache  
Настоятельно рекомендуется использовать cвой путь для кеширования, в противном случае данные будут УДАЛЕНЫ composer'ом при обновлении на новую версию.
```php
$apiClient->queries->setCacheStorage(
	new \Ufee\Amo\Base\Storage\Query\FileStorage($apiClient, ['path' => 'pull_path_to/cache'])
);
```
#### Redis
Поддерживается библиотека [phpredis](https://github.com/phpredis/phpredis)   
Формат ключа: {domain}-cache:{hash}
```php
$redis = new \Redis();
$redis->connect('/var/run/redis/redis.sock');
$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP); // \Redis::SERIALIZER_IGBINARY recommended
$redis->select(5); // switch to specific db

$apiClient->queries->setCacheStorage(
	new \Ufee\Amo\Base\Storage\Query\RedisStorage($apiClient, ['connection' => $redis])
);
```
## Поиск сущностей
Поиск по дополнительному полю
```php
$leads = $amo->leads()->searchByCustomField('Москва', 'Город', 300); // by CF name, max 300
$leads = $amo->leads()->searchByCustomField('Москва', 623425); // by CF id
$companies = $amo->companies()->searchByName('ООО Шарики за Ролики', 100); // by name, max 100
$contacts = $amo->contacts()->searchByEmail('Test@Mail.Ru', 0); // by email, no max limit
$contacts = $amo->contacts()->searchByPhone('89271002030');
```
## Работа с дополнительными полями
Убрать значение
```php
$entity->cf('Имя поля')->reset();
$entity->cf('Организация')->removeBy('name', 'ИП Петров А.А.');
```
Получить значение
```php
$entity->cf('Имя поля')->getValue();
$entity->cf('Имя поля')->getValues();
$entity->cf('Имя поля')->getEnums();
$entity->cf('Дата')->format('Y-m-d');
$entity->cf('Дата')->getTimestamp();
$entity->cf('Организация')->getValues();
```
Задать значение
```php
$entity->cf('Имя поля')->setEnum($enum);
$entity->cf('Имя поля')->setEnums($enums);
$entity->cf('Число')->setValue(5);
$entity->cf('Текст')->setValue('Test');
$entity->cf('Мультисписок')->reset()->setValues(['Мужская одежда', 'Аксессуары']);
$entity->cf('День рождения')->setDate('Y-m-d');
$entity->cf('Дата')->setTimestamp(14867456357);
$entity->cf('Дата')->setDate('Y-m-d');
$entity->cf('Переключатель')->enable();
$entity->cf('Переключатель')->disable();
$entity->cf('Полный адрес')->setCountry('Россия');
$entity->cf('Полный адрес')->setRegion('Чувашская республика');
$entity->cf('Полный адрес')->setCity('Чебоксары');
$entity->cf('Полный адрес')->setIndex(428000);
$entity->cf('Телефон')->setValue('987654321', 'Home');
$entity->cf('Телефон')->setValue('123456789');
$entity->cf('Email')->setValue('best@list.ru');
$entity->cf('Мгн. сообщения')->setValue('bestJa', 'Jabber');
$entity->cf('Юр. лицо')->setName('Команда F5');
$entity->cf('Юр. лицо')->setAddress('РФ, ЧР, г.Чебоксары');
$entity->cf('Юр. лицо')->setType(1);
$entity->cf('Юр. лицо')->setInn(123);
$entity->cf('Юр. лицо')->setKpp(456);
$entity->cf('Организация')->addValue([
    'name' => 'ИП Петров А.А.',
    'city' => 'Москва',
    '...' => '...'
]);
```
## Работа с коллекциями
Перебор, поиск и фильтрация
```php
foreach ($amo->leads as $lead) { ... }
$amo->leads->each(function(&$lead) { ... });
$leadsByCf = $amo->leads->find('name', 'Трубы гофрированные');
$leadsBySale = $amo->leads->filter(function($lead) {
    return $lead->sale > 0;
});
$firstLead = $lead->first();
$lastLead = $lead->last();
```
Сортировка
```php
$leads->sortBy('sale', 'DESC');
$leads->usort(function($a, $b) {});
$leads->uasort(function($a, $b) {});
```
Дополнительно
```php
$has_contains =  $leads->contains('name', 'Test');
$sale_sum = $leads->sum('sale');
$leads = $leads->transform(function($lead) {
    return [
        'id' => $lead->id,
        'name' => $lead->name
    ];
});
$leads_array = $leads->toArray();
```
## Работа со сделками
Получение всех сделок
```php
$leads = $amo->leads;
$leads = $amo->leads()->recursiveCall();
$leads = $amo->leads()->call(); // первые 500
```
Получение по дате последнего изменения
```php
$leads = $amo->leads()
             ->modifiedFrom('Y-m-5 09:20:00') // по дате, с 5 числа текущего месяца, с 9:20 утра
             ->modifiedFrom(1528188143) // или по timestamp
             ->maxRows(1000)
             ->listing();
```
Получение по ID
```php
$lead = $amo->leads()->find($id); // array|integer
```
Получение сделок с дополнительным условием
```php
$lead = $amo->leads()->where('key', $val)->recursiveCall();
```
Связанные сущности по сделке 
```php
$contact = $lead->contact;
$contacts = $lead->contacts;
$company = $lead->company;
$tasks = $lead->tasks;
$notes = $lead->notes;
```
Создание сделок
```php
$leads = [
    $amo->leads()->create(),
    $amo->leads()->create()
];
$leads[0]->name = 'Amoapi v7 - 1';
$leads[1]->name = 'Amoapi v7 - 2';
$amo->leads()->add($leads);

$lead = $amo->leads()->create();
$lead->name = 'Amoapi v7';
$lead->attachTag('Amoapi');
$lead->pipeline_id = $amo->account->pipelines->main();
$lead->status_id = $lead->pipeline->statuses->first();
$lead->responsible_user_id = $amo->account->currentUser->id;
$lead->sale = 100500;
$lead->cf('Число')->setValue(5);
$lead->cf('Текст')->setValue('Test');
$lead->cf('Мультисписок')->reset()->setValues(['Мужская одежда', 'Аксессуары']);
$lead->cf('Дата')->setValue(date('Y-m-d'));
$lead->cf('Переключатель')->disable();
$lead->save();
```
Создание сделки из контакта
```php
$lead = $contact->createLead();
$lead->name = 'Amoapi v7';
$lead->save();
```
Копирование сделки
```php
$copy = clone $lead;
$copy->name = 'New lead';
$copy->save();
```

## Работа с контактами
Получение всех контактов
```php
$contacts = $amo->contacts;
$contacts = $amo->contacts()->recursiveCall();
$contacts = $amo->contacts()->call(); // первые 500
```
Получение по ID
```php
$contact = $amo->contacts()->find($id); // array|integer
```
Получение контактов с дополнительным условием
```php
$contacts = $amo->contacts()->where('key', $val)->recursiveCall();
```
Связанные сущности по контакту
```php
$leads = $contact->leads;
$company = $contact->company;
$tasks = $lead->tasks;
$notes = $lead->notes;
```
Создание контактов
```php
$contacts = [
    $amo->contacts()->create(),
    $amo->contacts()->create()
];
$contacts[0]->name = 'Amoapi v7 - 1';
$contacts[1]->name = 'Amoapi v7 - 2';
$amo->contacts()->add($contacts);

$contact = $amo->contacts()->create();
$contact->name = 'Amoapi v7';
$contact->attachTags(['Amoapi', 'Test']);
$contact->cf('Телефон')->setValue('987654321', 'Home');
$contact->cf('Телефон')->setValue('123456789');
$contact->cf('Email')->setValue('best@list.ru');
$contact->save();
```
Создание контакта из сделки
```php
$contact = $lead->createContact();
$contact->name = 'Amoapi v7';
$contact->save();
```
Копирование контакта
```php
$copy = clone $contact;
$copy->name = 'New contact';
$copy->save();
```

## Работа с компаниями
Получение всех компаний
```php
$companies = $amo->companies;
$companies = $amo->companies()->recursiveCall();
$companies = $amo->companies()->call(); // первые 500
```
Получение по ID
```php
$company = $amo->companies()->find($id); // array|integer
```
Получение компаний с дополнительным условием
```php
$companies = $amo->companies()->where('key', $val)->recursiveCall();
```
Связанные сущности по компании
```php
$leads = $company->leads;
$contacts = $company->contacts;
$tasks = $lead->tasks;
$notes = $lead->notes;
```
Создание компаний
```php
$companys = [
    $amo->companies()->create(),
    $amo->companies()->create()
];
$companys[0]->name = 'Amoapi v7 - 1';
$companys[1]->name = 'Amoapi v7 - 2';
$amo->companies()->add($companys);

$company = $amo->companies()->create();
$company->name = 'Amoapi v7';
$company->save();
```
Создание компании из контакта или сделки
```php
$company = $contact->createCompany();
$company = $lead->createCompany();
$company->name = 'Amoapi v7';
$company->save();
```
Копирование компании
```php
$copy = clone $company;
$copy->name = 'New company';
$copy->save();
```

## Работа с задачами
Получение всех задач
```php
$tasks = $amo->tasks;
$tasks = $amo->tasks()->recursiveCall();
$tasks = $amo->tasks()->call(); // первые 500
```
Получение по ID
```php
$task = $amo->tasks()->find($id); // array|integer
```
Получение задач с дополнительным условием
```php
$tasks = $amo->tasks()->where('key', $val)->recursiveCall();
```
Создание задач
```php
$tasks = [
    $amo->tasks()->create(),
    $amo->tasks()->create()
];
$tasks[0]->text = 'Amoapi v7 - 1';
$tasks[0]->element_type = 3;
$tasks[0]->element_id = 34762721;
$tasks[1]->text = 'Amoapi v7 - 2';
$tasks[1]->element_type = 2;
$tasks[1]->element_id = 34762720;
$amo->tasks()->add($tasks);

$task = $amo->tasks()->create();
$task->text = 'Amoapi v7';
$task->element_type = 1;
$task->element_id = 34762725;
$task->save();
```
Создание задачи из контакта, сделки или компании
```php
$task = $contact->createTask($type = 1);
$task = $lead->createTask($type = 1);
$task = $company->createTask($type = 1);
$task->text = 'Amoapi v7';
$task->element_type = 1;
$task->element_id = 34762725;
$task->save();
```
Получение родительского контакта, сделки или компании
```php
$contact = $task->linkedContact;
$lead = $task->linkedLead;
$comapny = $task->linkedCompany;
```

## Работа с примечаниями
Получение всех примечаний
```php
$notes = $amo->notes;
$notes = $amo->notes()->where('type', 'contact')->recursiveCall();
$notes = $amo->notes()->where('type', 'lead')->call(); // первые 500
```
Получение примечаний по ID и типу сущности
```php
$note = $amo->notes()->find($id, 'lead');
```
Получение примечаний с дополнительным условием
```php
$notes = $amo->notes()->where('key', $val)->recursiveCall();
```
Создание примечаний
```php
$notes = [
    $amo->notes()->create(), 
    $amo->notes()->create()
];
$notes[0]->note_type = 4;
$notes[0]->text = 'Amoapi v7 - 1';
$notes[0]->element_type = 3;
$notes[0]->element_id = 34762721;
$notes[1]->note_type = 4;
$notes[1]->text = 'Amoapi v7 - 2';
$notes[1]->element_type = 2;
$notes[1]->element_id = 34762720;
$amo->notes()->add($notes);

$note = $amo->notes()->create();
$note->note_type = 4;
$note->text = 'Amoapi v7';
$note->element_type = 1;
$note->element_id = 34762725;
$note->save();
```
Создание примечания из контакта, сделки или компании
```php
$note = $contact->createNote($type = 4);
$note = $lead->createNote($type = 4);
$note = $company->createNote($type = 4);
$note->text = 'Amoapi v7';
$note->element_type = 2;
$note->element_id = 34762728;
$note->save();
```
Закрепление/открепление примечаний (note type 4)
```php
$note->setPinned(true); // true/false
```
Получение содержимого файла (note type 5)
```php
$contents = $note->getAttachment();
```
Получение родительского контакта, сделки или компании
```php
$contact = $note->linkedContact;
$lead = $note->linkedLead;
$comapny = $note->linkedCompany;
```

## Работа со списками
Получение всех списков (каталогов)
```php
$catalogs = $amo->catalogs;
```
Получение по ID
```php
$catalog = $amo->catalogs()->find($id); // array|integer
```
Получение списков с дополнительным условием
```php
$catalogs = $amo->catalogs()->where('key', $val)->call();
```
Связанные сущности по списку
```php
$elements = $catalog->elements;
```
Создание списков
```php
$catalogs = [
    $amo->catalogs()->create(),
    $amo->catalogs()->create()
];
$catalogs[0]->name = 'Amoapi v7 - 1';
$catalogs[1]->name = 'Amoapi v7 - 2';
$amo->catalogs()->add($catalogs);

$catalog = $amo->catalogs()->create();
$catalog->name = 'Amoapi v7';
$catalog->save();
```
Удаление списков
```php
$amo->catalogs()->delete($catalogs); // array|integer
$catalog->delete();
```

## Работа с элементами каталога (товарами)
Получение товаров
```php
$element = $amo->catalogElements()->find($id);
$elements = $amo->catalogElements()->where('catalog_id', 1234)->call();
$elements = $catalog->elements;
```
Добавление товаров
```php
$element = $amo->catalogElements()->create();
$element->catalog_id = 1234;
```
или
```php
$element = $catalog->createElement();
$element->name = 'Холодильник LG';
$element->cf('Артикул')->setValue('ML-4675');
$element->cf('Количество')->setValue(100);
$element->cf('Цена')->setValue(38500);
$element->save();
```
Обновление товаров
```php
$element->cf('Скидка')->setValue(5);
$element->save();
```
Связанные сущности по товару
```php
$catalog = $element->catalog;
$leads = $element->leads;
```
Удаление товаров
```php
$amo->elements()->delete($elements); // array|integer
$catalog->elements->delete(); // удаление всех товаров каталога
$element->delete();
```
Прикрепление товаров к сделке
```php
$lead->attachElement($catalog->id, $element->id, $count = 1);
```

## Работа с покупателями
Получение всех покупателей
```php
$customers = $amo->customers;
$customers = $amo->customers()->recursiveCall();
$customers = $amo->customers()->call(); // первые 500
```
Получение по ID
```php
$customer = $amo->customers()->find($id); // array|integer
```
Получение покупателей с дополнительным условием
```php
$customer = $amo->customers()->where('key', $val)->recursiveCall();
```
Связанные сущности покупателя
```php
$contact = $customer->contact;
$contacts = $customer->contacts;
$company = $customer->company;
$tasks = $customer->tasks;
$notes = $customer->notes;
$transactions = $customer->transactions;
```
Создание покупателей
```php
$customer = $amo->customers()->create();
$customer->name = 'Amoapi v7';
$customer->next_date = time();
$customer->next_price = 100;
$customer->responsible_user_id = $amo->account->currentUser->id;
$customer->cf('Число')->setValue(5);
$customer->cf('Текст')->setValue('Test');
$customer->cf('Мультисписок')->reset()->setValues(['Мужская одежда', 'Аксессуары']);
$customer->cf('Дата')->setValue(date('Y-m-d'));
$customer->cf('Переключатель')->disable();
$customer->save();
```
Создание покупателя из контакта
```php
$customer = $contact->createCustomer();
$customer->name = 'Amoapi v7';
$customer->next_date = time();
$customer->save();
```
Удаление покупателей
```php
$amo->customers()->delete($customers); // array|integer
$customer->delete();
```

## Работа с покупками
Получение транзакций (покупок)
```php
$transactions = $amo->transactions;
$transactions = $customer->transactions;
```
Добавление транзакций
```php
$transaction = $amo->transactions()->create();
$transaction->customer_id = 1234;
```
или
```php
$transaction = $customer->createTransaction();
$transaction->price = 1500;
$transaction->save();
```
Обновление комментариев транзакций покупателя
```php
$transaction->comment = 'Тест';
$transaction->save();
```
Удаление транзакций покупателей
```php
$amo->transactions()->delete($transactions); // array|integer
$customer->transactions->delete(); // удаление всех покупок покупателя
$transaction->delete(); // удаление покупки
```

## Работа с веб-хуками
Получение вебхуков (webhooks)
```php
$webhooks = $amo->webhooks;
```
Добавление вебхуков
```php
$result = $amo->webhooks()->subscribe('http://site.ru/handler/', ['add_lead', 'update_contact', 'responsible_lead']);
```
Удаление вебхуков
```php
$result = $amo->webhooks()->unsubscribe('http://site.ru/handler/', ['update_contact', 'responsible_lead']);
```

## Работа с frontend методами
Скачивание файла из примечания
```php
$contents = $amo->ajax()->getAttachment('AbCd_attach_name.zip');
```
Выполнение произвольных запросов
```php
$amo->ajax()->get($url = '/ajax/example', $args = []);
$amo->ajax()->post($url = '/ajax/example', $data = [], $args = [], $post_type = 'raw OR json');
$amo->ajax()->postJson($url = '/ajax/example', $data = [], $args = []);
$amo->ajax()->patch($url = '/ajax/example', $data = [], $args = []);
```
