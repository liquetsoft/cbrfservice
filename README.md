Php курсы валют
===============

[![Latest Stable Version](https://poser.pugx.org/liquetsoft/cbrfservice/v/stable.png)](https://packagist.org/packages/liquetsoft/cbrfservice)
[![Total Downloads](https://poser.pugx.org/liquetsoft/cbrfservice/downloads.png)](https://packagist.org/packages/liquetsoft/cbrfservice)
[![License](https://poser.pugx.org/liquetsoft/cbrfservice/license.svg)](https://packagist.org/packages/liquetsoft/cbrfservice)
[![Build Status](https://github.com/liquetsoft/cbrfservice/workflows/cbrf_service/badge.svg)](https://github.com/liquetsoft/cbrfservice/actions?query=workflow%3A%22cbrf_service%22)

Php обертка для [сервиса Центробанка РФ](https://www.cbr.ru/development/DWS/).



Установка
---------

Добавьте библиотеку в проект с помощью [Composer](https://getcomposer.org/doc/00-intro.md):

```bash
composer req liquetsoft/cbrfservice
```



Использование
-------------

```php
//инициируем новый объект сервиса
$cbrf = new \Liquetsoft\CbrfService\CbrfDaily();
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

```php
//получаем депозиты банков в Банке России
$leftovers = $cbrf->ostatDepo(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем международные резервы Российской Федерации, ежемесячные значения
$mrrf = $cbrf->mrrf(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем международные резервы Российской Федерации, еженедельные значения
$mrrf = $cbrf->mrrf7d(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем сальдо операций ЦБ РФ по предоставлению/абсорбированию ликвидности
$saldo = $cbrf->saldo(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем индекс и срочную версию RUONIA
$saldo = $cbrf->ruoniaSV(
    new \DateTimeImmutable('-1 month'),
    new \DateTimeImmutable()
);
```

```php
//получаем ставку RUONIA
$saldo = $cbrf->ruonia(
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
$cbrf = new \Liquetsoft\CbrfService\CbrfDaily($client);
```



Обработка ошибок
----------------

Все ошибки, которые будут перехвачены при запросах, будут выброшены как исключение `\Liquetsoft\CbrfService\CbrfException`. Если `\SoapClient` будет сконфигурирован с отключенными исключениями, то обработка ошибок остается на стороне клиентского скрипта.



Методы
------

Описание методов вы можете найти на [сайте банка России](https://www.cbr.ru/development/DWS/).
