Php класс, получает курсы валют
===============================

Php обертка для [сервиса цб РФ](http://www.cbr.ru/scripts/Root.asp?PrtId=DWS).


Установка
---------

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md).**

Добавьте в ваш composer.json в раздел `require`:

```javascript
"require": {
    "php": ">=5.3.3",
    "marvin255/cbrfservice": "dev-master"
}
```

И в раздел `repositories`:

```javascript
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/marvin255/cbrfservice"
    }
]
```

**Обычная**

Скачайте библиотеку и распакуйте ее в свой проект. Убедитесь, что файл `Autoloader.php` подключен в вашем скрипте.

```php
require_once 'lib/Autoloader.php';
```


Использование
-------------

```php
//инициируем новый объект сервиса
$cbrf = new \cbrfservice\CbrfDaily;
//получаем курсы валют
$currencies = $cbrf->GetCursOnDate();
//получаем список доступных валют
$enumCurrencies = $cbrf->EnumValutes();
```


Настройка
---------

Внимание! Все настройки soap должны быть заданы до первого запроса к сервису.

При инициализации:

```php
$cbrf = new \cbrfservice\CbrfDaily(array(
	'wsdl' => 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL',
));
```

После инициализации:

```php
$cbrf->config(array(
	'catchExceptions' => false,
));
```

Опции
-----

* `wsdl` - ссылка на WSDL описание, по умолчанию `'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL'`;

* `soapOptions` - настройки [SoapClient](http://php.net/manual/ru/soapclient.soapclient.php), по умолчанию `array()`;

* `catchExceptions` - если значение истинно, то все исключения будут перехвачены классом и внесены во внутренний массив ошибок, в противном случае исключения не будут обрабатываться, по умолчанию `true`;


Методы
------

* `array \cbrfservice\BaseService::getErrors( void )` - возвращает массив ошибок, полученных во время запросов к сервису.

* `bool \cbrfservice\BaseService::hasErrors( void )` - возвращает истину, если во время выполнения запроса были ошибки.

* `void \cbrfservice\BaseService::clearErrors( void )` - очищает список ошибок.

* `array \cbrfservice\CbrfDaily::GetCursOnDate( [mixed $onDate, mixed $currency] )` - возвращает массив с курсами валют за заданную дату. Если `$onDate` не задан, то возвращается список валют за текущее время. Если задан `$currency`, то возвращается значение только для этой валюты.

* `array \cbrfservice\CbrfDaily::EnumValutes( [bool $seld, mixed $currency] )` - возвращает список с описаниями валютю $seld: false — перечень ежедневных валют, true — перечень ежемесячных валют. Если задан `$currency`, то возвращается значение только для этой валюты.

* Описание остальных методов вы можете найти на [сайте банка России](http://www.cbr.ru/scripts/Root.asp?PrtId=DWS). Даты для этих методов могут быть заданы в любом формате пригодном для `strtotime()`.
