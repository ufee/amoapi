## AmoCRM API Client
Api клиент для работы с amoCRM

## Установка

```
composer require ufee/amoapi
```

**Запуск тестов**

Отредактировать vendor/ufee/amoapi/tests/Config.php

```
composer require --dev phpunit/phpunit ^5
vendor/bin/phpunit vendor/ufee/amoapi
```

## Работа с клиентом

```
$amo = \Ufee\Amo\Amoapi::setInstance([
	'id' => 123,
	'domain' => 'testdomain',
	'login' => 'test@login',
	'hash' => 'testhash'
]);
```

**Работа со сделками**

```
Получение всех сделок
$leads = $amo->leads;
$leads = $amo->leads()->recursiveCall();
$leads = $amo->leads()->call(); // первые 500

Получение по ID
$lead = $amo->leads()->find($id); // array|integer

Получение сделок с дополнительным условием
$lead = $amo->leads()->where('key', $val)->recursiveCall();

Связанные сущности по сделке 
$contact = $lead->contact;
$contacts = $lead->contacts;
$company = $lead->company;
$tasks = $lead->tasks;
$notes = $lead->notes;

Создание сделок
$lead1 = $amo->leads()->create();
$lead1->name = 'Amoapi v7 - 1';
$lead2 = $amo->leads()->create();
$lead2->name = 'Amoapi v7 - 2';
$amo->leads()->add([$lead1, $lead2]);

$lead = $amo->leads()->create();
$lead->name = 'Amoapi v7';
$lead->pipeline_id = $amo->account->pipelines->main();
$lead->status_id = $lead->pipeline->statuses->first();
$lead->responsible_user_id = $amo->account->currentUser->id;
$lead->sale = 100500;
$lead->cf('Число')->setValue(5);
$lead->cf('Текст')->setValue('Test');
$lead->cf('Мультисписок')->reset()->setValues(['Мужская одежда', 'Аксессуары']);
$lead->cf('День рождения')->setValue(date('Y-m-d'));
$lead->cf('Дата')->setValue(date('Y-m-d'));
$lead->cf('Переключатель')->disable();
$lead->save();

Создание сделки из контакта
$lead = $contact->createLead();
$lead->name = 'Amoapi v7';
$lead->save();
```

**Работа с контактами**

```
Получение всех контактов
$contacts = $amo->contacts;
$contacts = $amo->contacts()->recursiveCall();
$contacts = $amo->contacts()->call(); // первые 500

Получение по ID
$contact = $amo->contacts()->find($id); // array|integer

Получение контактов с дополнительным условием
$contacts = $amo->contacts()->where('key', $val)->recursiveCall();

Связанные сущности по контакту
$leads = $contact->leads;
$company = $contact->company;
$tasks = $lead->tasks;
$notes = $lead->notes;

Создание контактов
$contact1 = $amo->contacts()->create();
$contact1->name = 'Amoapi v7 - 1';
$contact2 = $amo->contacts()->create();
$contact2->name = 'Amoapi v7 - 2';
$amo->contacts()->add([$contact1, $contact2]);

$contact = $amo->contacts()->create();
$contact->name = 'Amoapi v7';
$contact->cf('Телефон')->setValue('987654321', 'Home');
$contact->cf('Телефон')->setValue('123456789');
$contact->cf('Email')->setValue('best@list.ru');
$contact->cf('Мгн. сообщения')->setValue('bestJa', 'Jabber');
$contact->cf('Короткий адрес')->setValue('РФ, ЧР, г.Чебоксары');
$contact->cf('Полный адрес')->setIndex(428000);
$contact->cf('Настрой')->setValue('Отличный');
$contact->cf('Адрес сайта')->setValue('https://cmdf5.ru/');
$contact->cf('Описание')->setValue('Рыбным текстом называется текст, служащий для временного наполнения макета в публикациях или производстве веб-сайтов, пока финальный текст еще не создан.');
$contact->cf('Юр. лицо')->setName('Команда F5');
$contact->cf('Юр. лицо')->setAddress('РФ, ЧР, г.Чебоксары');
$contact->cf('Юр. лицо')->setType(1);
$contact->cf('Юр. лицо')->setInn(123);
$contact->cf('Юр. лицо')->setKpp(456);
$contact->save();

Создание контакта из сделки
$contact = $lead->createContact();
$contact->name = 'Amoapi v7';
$contact->save();
```

**Работа с компаниями**

```
Получение всех компаний
$companies = $amo->companies;
$companies = $amo->companies()->recursiveCall();
$companies = $amo->companies()->call(); // первые 500

Получение по ID
$company = $amo->companies()->find($id); // array|integer

Получение компаний с дополнительным условием
$companies = $amo->companies()->where('key', $val)->recursiveCall();

Связанные сущности по компании
$leads = $company->leads;
$contacts = $company->contacts;
$tasks = $lead->tasks;
$notes = $lead->notes;

Создание компаний
$company1 = $amo->companies()->create();
$company1->name = 'Amoapi v7 - 1';
$company2 = $amo->companies()->create();
$company2->name = 'Amoapi v7 - 2';
$amo->companies()->add([$company1, $company2]);

$company = $amo->companies()->create();
$company->name = 'Amoapi v7';
$company->save();

Создание компании из контакта или сделки
$company = $contact->createCompany();
$company = $lead->createCompany();
$company->name = 'Amoapi v7';
$company->save();
```

**Работа с задачами**

```
Получение всех задач
$tasks = $amo->tasks;
$tasks = $amo->tasks()->recursiveCall();
$tasks = $amo->tasks()->call(); // первые 500

Получение по ID
$task = $amo->tasks()->find($id); // array|integer

Получение задач с дополнительным условием
$tasks = $amo->tasks()->where('key', $val)->recursiveCall();

Создание задач
$task1 = $amo->tasks()->create();
$task1->text = 'Amoapi v7 - 1';
$task1->element_type = 3;
$task1->element_id = 34762721;
$task2 = $amo->tasks()->create();
$task2->text = 'Amoapi v7 - 2';
$task2->element_type = 2;
$task2->element_id = 34762720;
$amo->tasks()->add([$task1, $task2]);

$task = $amo->tasks()->create();
$task->text = 'Amoapi v7';
$task->element_type = 1;
$task->element_id = 34762725;
$task->save();

Создание задачи из контакта, сделки или компании
$task = $contact->createTask($type = 1);
$task = $lead->createTask($type = 1);
$task = $company->createTask($type = 1);
$task->text = 'Amoapi v7';
$task->element_type = 1;
$task->element_id = 34762725;
$task->save();
```

**Работа с примечаниями**

```
Получение всех примечаний
$notes = $amo->notes;
$notes = $amo->notes()->recursiveCall();
$notes = $amo->notes()->call(); // первые 500

Получение по ID
$note = $amo->notes()->find($id); // array|integer

Получение примечаний с дополнительным условием
$notes = $amo->notes()->where('key', $val)->recursiveCall();

Создание примечаний
$note1 = $amo->notes()->create();
$note1->note_type = 4;
$note1->text = 'Amoapi v7 - 1';
$note1->element_type = 3;
$note1->element_id = 34762721;
$note2 = $amo->notes()->create();
$note2->note_type = 4;
$note2->text = 'Amoapi v7 - 2';
$note2->element_type = 2;
$note2->element_id = 34762720;
$amo->notes()->add([$note1, $note2]);

$note = $amo->notes()->create();
$note->note_type = 4;
$note->text = 'Amoapi v7';
$note->element_type = 1;
$note->element_id = 34762725;
$note->save();

Создание примечания из контакта, сделки или компании
$note = $contact->createNote($type = 4);
$note = $lead->createNote($type = 4);
$note = $company->createNote($type = 4);
$note->text = 'Amoapi v7';
$note->element_type = 2;
$note->element_id = 34762728;
$note->save();
```