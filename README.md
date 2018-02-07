Php класс, получает курсы валют
===============================

[![Latest Stable Version](https://poser.pugx.org/marvin255/cbrfservice/v/stable.png)](https://packagist.org/packages/marvin255/cbrfservice)
[![License](https://poser.pugx.org/marvin255/cbrfservice/license.svg)](https://packagist.org/packages/marvin255/cbrfservice)
[![Build Status](https://travis-ci.org/marvin255/cbrfservice.svg?branch=master)](https://travis-ci.org/marvin255/cbrfservice)

Php обертка для [сервиса Центробанка РФ](http://www.cbr.ru/scripts/Root.asp?PrtId=DWS).


Установка
---------

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md).**

Добавьте в ваш composer.json в раздел `require`:

```javascript
"require": {
    "marvin255/cbrfservice": "*"
}
```

**Обычная**

Скачайте библиотеку и распакуйте ее в свой проект. Убедитесь, что файл `Autoloader.php` подключен в вашем скрипте.

```php
require_once 'src/Autoloader.php';
```


Использование
-------------

```php
//инициируем новый объект сервиса
$cbrf = new \marvin255\cbrfservice\CbrfDaily;
//получаем курсы валют
$currencies = $cbrf->GetCursOnDate();
//получаем список доступных валют
$enumCurrencies = $cbrf->EnumValutes();
```

В случае, если необходимо передать сконфигурированный заранее транспорт, например для использования proxy:

```php
//инициируем новый объект SoapClient
$client = new SoapClient(
    'some.wsdl',
    [
        'proxy_host' => 'localhost',
        'proxy_port' => 8080
    ]
);
//инициируем новый объект сервиса
$cbrf = new \marvin255\cbrfservice\CbrfDaily($client);
//получаем курсы валют
$currencies = $cbrf->GetCursOnDate();
//получаем список доступных валют
$enumCurrencies = $cbrf->EnumValutes();
```



Обработка ошибок
----------------

все ошибк, которые будут перехвачены при запросах, будут выброшены как исключение `\marvin255\cbrfservice\Exception`. Если `\SoapClient` будет сконфигурирован без исключений, то обработка ошибок остается на стороне клиентского скрипта.



Методы
------

* `array \cbrfservice\CbrfDaily::GetCursOnDate( [mixed $onDate, mixed $currency] )` - возвращает массив с курсами валют за заданную дату. Если `$onDate` не задан, то возвращается список валют за текущее время. Если задан `$currency`, то возвращается значение только для этой валюты.

* `array \cbrfservice\CbrfDaily::EnumValutes( [bool $seld, mixed $currency] )` - возвращает список с описаниями валютю $seld: false — перечень ежедневных валют, true — перечень ежемесячных валют. Если задан `$currency`, то возвращается значение только для этой валюты.

* Описание остальных методов вы можете найти на [сайте банка России](http://www.cbr.ru/scripts/Root.asp?PrtId=DWS). Даты для этих методов могут быть заданы в любом формате пригодном для `strtotime()`.
