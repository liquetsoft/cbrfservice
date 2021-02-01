Php класс, получает курсы валют
===============================

[![Latest Stable Version](https://poser.pugx.org/marvin255/cbrfservice/v/stable.png)](https://packagist.org/packages/marvin255/cbrfservice)
[![Total Downloads](https://poser.pugx.org/marvin255/cbrfservice/downloads.png)](https://packagist.org/packages/marvin255/cbrfservice)
[![License](https://poser.pugx.org/marvin255/cbrfservice/license.svg)](https://packagist.org/packages/marvin255/cbrfservice)
[![Build Status](https://github.com/marvin255/cbrfservice/workflows/cbrf_service/badge.svg)](https://github.com/marvin255/cbrfservice/actions?query=workflow%3A%22cbrf_service%22)

Php обертка для [сервиса Центробанка РФ](https://www.cbr.ru/development/DWS/).


Установка
---------

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md).**

Добавьте библиотеку с помощью composer:

```bash
composer req marvin255/cbrfservice
```


Использование
-------------

```php
//инициируем новый объект сервиса
$cbrf = new \Marvin255\CbrfService\CbrfDaily();
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
$cbrf = new \Marvin255\CbrfService\CbrfDaily($client);
//получаем курсы валют
$currencies = $cbrf->GetCursOnDate();
//получаем список доступных валют
$enumCurrencies = $cbrf->EnumValutes();
```



Обработка ошибок
----------------

Все ошибки, которые будут перехвачены при запросах, будут выброшены как исключение `\Marvin255\CbrfService\Exception`. Если `\SoapClient` будет сконфигурирован без исключений, то обработка ошибок остается на стороне клиентского скрипта.



Методы
------

* `array \Marvin255\CbrfService\CbrfDaily::GetCursOnDate( [mixed $onDate, mixed $currency] )` - возвращает массив с курсами валют за заданную дату. Если `$onDate` не задан, то возвращается список валют за текущее время. Если задан `$currency`, то возвращается значение только для этой валюты.

* `array \Marvin255\CbrfService\CbrfDaily::EnumValutes( [bool $seld, mixed $currency] )` - возвращает список с описаниями валютю $seld: false — перечень ежедневных валют, true — перечень ежемесячных валют. Если задан `$currency`, то возвращается значение только для этой валюты.

* Описание остальных методов вы можете найти на [сайте банка России](https://www.cbr.ru/development/DWS/). Даты для этих методов могут быть заданы в любом формате пригодном для `strtotime()`.
