Php курсы валют
===============

[![Latest Stable Version](https://poser.pugx.org/marvin255/cbrfservice/v/stable.png)](https://packagist.org/packages/marvin255/cbrfservice)
[![Total Downloads](https://poser.pugx.org/marvin255/cbrfservice/downloads.png)](https://packagist.org/packages/marvin255/cbrfservice)
[![License](https://poser.pugx.org/marvin255/cbrfservice/license.svg)](https://packagist.org/packages/marvin255/cbrfservice)
[![Build Status](https://github.com/marvin255/cbrfservice/workflows/cbrf_service/badge.svg)](https://github.com/marvin255/cbrfservice/actions?query=workflow%3A%22cbrf_service%22)

Php обертка для [сервиса Центробанка РФ](https://www.cbr.ru/development/DWS/).



Установка
---------

Добавьте библиотеку в проект с помощью [Composer](https://getcomposer.org/doc/00-intro.md):

```bash
composer req marvin255/cbrfservice
```



Использование
-------------

```php
//инициируем новый объект сервиса
$cbrf = new \Marvin255\CbrfService\CbrfDaily();
```

```php
//получаем курсы всех валют
$rates = $cbrf->getCursOnDate(new \DateTimeImmutable());

//получаем курс валюты по ее буквенному коду
$rateEur = $cbrf->getCursOnDateByCharCode(new \DateTimeImmutable(), 'EUR');

//получаем курс валюты по ее цифровому коду
$rate978 = $cbrf->getCursOnDateByNumericCode(new \DateTimeImmutable(), 978);
```

```php
//получаем словарь всех доступных валют
$currencies = $cbrf->enumValutes();

//получаем описание валюты из словаря по буквенному коду
$enumEur = $cbrf->enumValuteByCharCode('EUR');

//получаем описание валюты из словаря по цифровому коду
$enum978 = $cbrf->enumValuteByNumericCode(978);

//получаем динамику курса для указанной валюты за последний месяц
$dynamic = $cbrf->getCursDynamic(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable(),
    $enumEur
);
```

```php
//получаем динамику ключевой ставки за последний месяц
$keyRate = $cbrf->keyRate(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем динамику цен на драгоценные металлы за последний месяц
$metalsPrices = $cbrf->dragMetDynamic(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем валютный своп за последний месяц
$swap = $cbrf->swapDynamic(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем динамику ставок привлечения средств по депозитным операциям за последний месяц
$depo = $cbrf->depoDynamic(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем динамику сведений об остатках средств на корреспондентских счетах кредитных организаций
$leftovers = $cbrf->ostatDynamic(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
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
```



Обработка ошибок
----------------

Все ошибки, которые будут перехвачены при запросах, будут выброшены как исключение `\Marvin255\CbrfService\CbrfException`. Если `\SoapClient` будет сконфигурирован с отключенными исключениями, то обработка ошибок остается на стороне клиентского скрипта.



Методы
------

Описание методов вы можете найти на [сайте банка России](https://www.cbr.ru/development/DWS/).
