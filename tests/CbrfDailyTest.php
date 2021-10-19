<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTimeImmutable;
use Marvin255\CbrfService\CbrfDaily;
use Marvin255\CbrfService\CbrfSoapService;
use Marvin255\CbrfService\Entity\CurrencyEnum;
use Marvin255\CbrfService\Entity\CurrencyRate;
use Marvin255\CbrfService\Entity\DepoRate;
use Marvin255\CbrfService\Entity\KeyRate;
use Marvin255\CbrfService\Entity\OstatRate;
use Marvin255\CbrfService\Entity\PreciousMetalRate;
use Marvin255\CbrfService\Entity\SwapRate;
use stdClass;

/**
 * @internal
 */
class CbrfDailyTest extends BaseTestCase
{
    public const FIXTURES = [
        'CurrencyRate' => [
            'schema' => [
                'VchCode' => self::FIXTURE_TYPE_STRING,
                'Vname' => self::FIXTURE_TYPE_STRING,
                'Vcode' => self::FIXTURE_TYPE_INT,
                'Vcurs' => self::FIXTURE_TYPE_FLOAT,
                'Vnom' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'GetCursOnDateResult.any.ValuteData.ValuteCursOnDate',
        ],
        'EnumValutes' => [
            'schema' => [
                'Vcode' => self::FIXTURE_TYPE_STRING,
                'Vname' => self::FIXTURE_TYPE_STRING,
                'VEngname' => self::FIXTURE_TYPE_STRING,
                'Vnom' => self::FIXTURE_TYPE_INT,
                'VcommonCode' => self::FIXTURE_TYPE_STRING,
                'VnumCode' => self::FIXTURE_TYPE_INT,
                'VcharCode' => self::FIXTURE_TYPE_STRING,
            ],
            'path' => 'EnumValutesResult.any.ValuteData.EnumValutes',
        ],
        'CursDynamic' => [
            'schema' => [
                'CursDate' => self::FIXTURE_TYPE_DATE,
                'Vcurs' => self::FIXTURE_TYPE_FLOAT,
                'Vcode' => self::FIXTURE_TYPE_STRING,
                'Vnom' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'GetCursDynamicResult.any.ValuteData.ValuteCursDynamic',
        ],
        'KeyRate' => [
            'schema' => [
                'DT' => self::FIXTURE_TYPE_DATE,
                'Rate' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'KeyRateResult.any.KeyRate.KR',
        ],
        'DragMetDynamic' => [
            'schema' => [
                'DateMet' => self::FIXTURE_TYPE_DATE,
                'price' => self::FIXTURE_TYPE_FLOAT,
                'CodMet' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'DragMetDynamicResult.any.DragMetall.DrgMet',
        ],
        'SwapDynamic' => [
            'schema' => [
                'DateBuy' => self::FIXTURE_TYPE_DATE,
                'DateSell' => self::FIXTURE_TYPE_DATE,
                'BaseRate' => self::FIXTURE_TYPE_FLOAT,
                'TIR' => self::FIXTURE_TYPE_FLOAT,
                'Stavka' => self::FIXTURE_TYPE_FLOAT,
                'Currency' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'SwapDynamicResult.any.SwapDynamic.Swap',
        ],
        'DepoDynamic' => [
            'schema' => [
                'Overnight' => self::FIXTURE_TYPE_FLOAT,
                'DateDepo' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'DepoDynamicResult.any.DepoDynamic.Depo',
        ],
        'OstatDynamic' => [
            'schema' => [
                'DateOst' => self::FIXTURE_TYPE_DATE,
                'InMoscow' => self::FIXTURE_TYPE_FLOAT,
                'InRuss' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'OstatDynamicResult.any.OstatDynamic.Ostat',
        ],
    ];

    /**
     * @test
     */
    public function testGetCursOnDate(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['CurrencyRate']);
        $onDate = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->getCursOnDate($onDate);

        $this->assertCount(\count($courses), $list);
        $this->assertContainsOnlyInstancesOf(CurrencyRate::class, $list);
        foreach ($courses as $key => $course) {
            $this->assertSame(strtoupper($course['VchCode']), $list[$key]->getCharCode());
            $this->assertSame($course['Vname'], $list[$key]->getName());
            $this->assertSame($course['Vcode'], $list[$key]->getNumericCode());
            $this->assertSame($course['Vcurs'], $list[$key]->getRate());
            $this->assertSame($course['Vnom'], $list[$key]->getNom());
            $this->assertSameDate($onDate, $list[$key]->getDate());
        }
    }

    /**
     * @test
     */
    public function testGetCursOnDateByCharCode(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['CurrencyRate']);
        $charCode = $courses[0]['VchCode'] ?? '';
        $onDate = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $item = $service->getCursOnDateByCharCode($onDate, $charCode);

        $this->assertInstanceOf(CurrencyRate::class, $item);
        $this->assertSame(strtoupper($charCode), $item->getCharCode());
    }

    /**
     * @test
     */
    public function testGetCursOnDateByNumericCode(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['CurrencyRate']);
        $numericCode = $courses[0]['Vcode'] ?? 0;
        $onDate = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $item = $service->getCursOnDateByNumericCode($onDate, $numericCode);

        $this->assertInstanceOf(CurrencyRate::class, $item);
        $this->assertSame($numericCode, $item->getNumericCode());
    }

    /**
     * @test
     */
    public function testEnumValutes(): void
    {
        [$currencies, $response] = $this->createFixture(self::FIXTURES['EnumValutes']);
        $seld = false;

        $soapClient = $this->createSoapCallMock(
            'EnumValutes',
            [
                'Seld' => $seld,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->enumValutes($seld);

        $this->assertCount(\count($currencies), $list);
        $this->assertContainsOnlyInstancesOf(CurrencyEnum::class, $list);
        foreach ($currencies as $key => $currency) {
            $this->assertSame(strtoupper($currency['VcharCode']), $list[$key]->getCharCode());
            $this->assertSame($currency['Vname'], $list[$key]->getName());
            $this->assertSame($currency['Vcode'], $list[$key]->getInternalCode());
            $this->assertSame($currency['VEngname'], $list[$key]->getEngName());
            $this->assertSame($currency['Vnom'], $list[$key]->getNom());
            $this->assertSame($currency['VnumCode'], $list[$key]->getNumericCode());
            $this->assertSame($currency['VcommonCode'], $list[$key]->getCommonCode());
        }
    }

    /**
     * @test
     */
    public function testEnumValuteByCharCode(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['EnumValutes']);
        $charCode = $courses[0]['VcharCode'] ?? '';
        $seld = false;

        $soapClient = $this->createSoapCallMock(
            'EnumValutes',
            [
                'Seld' => $seld,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $item = $service->enumValuteByCharCode($charCode, $seld);

        $this->assertInstanceOf(CurrencyEnum::class, $item);
        $this->assertSame(strtoupper($charCode), $item->getCharCode());
    }

    /**
     * @test
     */
    public function testEnumValuteByNumericCode(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['EnumValutes']);
        $numericCode = $courses[0]['VnumCode'] ?? 0;
        $seld = false;

        $soapClient = $this->createSoapCallMock(
            'EnumValutes',
            [
                'Seld' => $seld,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $item = $service->enumValuteByNumericCode($numericCode, $seld);

        $this->assertInstanceOf(CurrencyEnum::class, $item);
        $this->assertSame($numericCode, $item->getNumericCode());
    }

    /**
     * @test
     */
    public function testGetLatestDateTime(): void
    {
        $date = new DateTimeImmutable();
        $response = new stdClass();
        $response->GetLatestDateTimeResult = $date->format(CbrfSoapService::DATE_TIME_FORMAT);

        $soapClient = $this->createSoapCallMock(
            'GetLatestDateTime',
            null,
            $response
        );

        $service = new CbrfDaily($soapClient);
        $testDate = $service->getLatestDateTime();

        $this->assertSameDate($date, $testDate);
    }

    /**
     * @test
     */
    public function testGetLatestDateTimeSeld(): void
    {
        $date = new DateTimeImmutable();
        $response = new stdClass();
        $response->GetLatestDateTimeSeldResult = $date->format(CbrfSoapService::DATE_TIME_FORMAT);

        $soapClient = $this->createSoapCallMock(
            'GetLatestDateTimeSeld',
            null,
            $response
        );

        $service = new CbrfDaily($soapClient);
        $testDate = $service->getLatestDateTimeSeld();

        $this->assertSameDate($date, $testDate);
    }

    /**
     * @test
     */
    public function testGetLatestDate(): void
    {
        $date = new DateTimeImmutable();
        $response = new stdClass();
        $response->GetLatestDateResult = $date->format(CbrfSoapService::DATE_TIME_FORMAT);

        $soapClient = $this->createSoapCallMock(
            'GetLatestDate',
            null,
            $response
        );

        $service = new CbrfDaily($soapClient);
        $testDate = $service->getLatestDate();

        $this->assertSameDate($date, $testDate);
    }

    /**
     * @test
     */
    public function testGetLatestDateSeld(): void
    {
        $date = new DateTimeImmutable();
        $response = new stdClass();
        $response->GetLatestDateSeldResult = $date->format(CbrfSoapService::DATE_TIME_FORMAT);

        $soapClient = $this->createSoapCallMock(
            'GetLatestDateSeld',
            null,
            $response
        );

        $service = new CbrfDaily($soapClient);
        $testDate = $service->getLatestDateSeld();

        $this->assertSameDate($date, $testDate);
    }

    /**
     * @test
     */
    public function testGetCursDynamic(): void
    {
        [$currencies, $response] = $this->createFixture(self::FIXTURES['CursDynamic']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();
        $charCode = 'EUR';
        $numericCode = 978;
        $name = 'Euro';
        $internalCode = 'test01';

        $currencyEnum = $this->getMockBuilder(CurrencyEnum::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currencyEnum->method('getInternalCode')->willReturn($internalCode);
        $currencyEnum->method('getName')->willReturn($name);
        $currencyEnum->method('getCharCode')->willReturn($charCode);
        $currencyEnum->method('getNumericCode')->willReturn($numericCode);

        $soapClient = $this->createSoapCallMock(
            'GetCursDynamic',
            [
                'FromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ValutaCode' => $internalCode,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->getCursDynamic($from, $to, $currencyEnum);

        $this->assertCount(\count($currencies), $list);
        $this->assertContainsOnlyInstancesOf(CurrencyRate::class, $list);
        foreach ($currencies as $key => $currency) {
            $this->assertSame($charCode, $list[$key]->getCharCode());
            $this->assertSame($name, $list[$key]->getName());
            $this->assertSame($numericCode, $list[$key]->getNumericCode());
            $this->assertSame($currency['Vcurs'], $list[$key]->getRate());
            $this->assertSame($currency['Vnom'], $list[$key]->getNom());
            $this->assertSameDate(new DateTimeImmutable($currency['CursDate']), $list[$key]->getDate());
        }
    }

    /**
     * @test
     */
    public function testKeyRate(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['KeyRate']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'KeyRate',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->keyRate($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(KeyRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSameDate(new DateTimeImmutable($rate['DT']), $list[$key]->getDate());
            $this->assertSame($rate['Rate'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testDragMetDynamic(): void
    {
        [$metals, $response] = $this->createFixture(self::FIXTURES['DragMetDynamic']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'DragMetDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->dragMetDynamic($from, $to);

        $this->assertCount(\count($metals), $list);
        $this->assertContainsOnlyInstancesOf(PreciousMetalRate::class, $list);
        foreach ($metals as $key => $metal) {
            $this->assertSameDate(new DateTimeImmutable($metal['DateMet']), $list[$key]->getDate());
            $this->assertSame($metal['CodMet'], $list[$key]->getCode());
            $this->assertSame($metal['price'], $list[$key]->getPrice());
        }
    }

    /**
     * @test
     */
    public function testSwapDynamic(): void
    {
        [$swaps, $response] = $this->createFixture(self::FIXTURES['SwapDynamic']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'SwapDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->swapDynamic($from, $to);

        $this->assertCount(\count($swaps), $list);
        $this->assertContainsOnlyInstancesOf(SwapRate::class, $list);
        foreach ($swaps as $key => $swap) {
            $this->assertSameDate(new DateTimeImmutable($swap['DateBuy']), $list[$key]->getDateBuy());
            $this->assertSameDate(new DateTimeImmutable($swap['DateSell']), $list[$key]->getDateSell());
            $this->assertSame($swap['BaseRate'], $list[$key]->getBaseRate());
            $this->assertSame($swap['TIR'], $list[$key]->getTIR());
            $this->assertSame($swap['Stavka'], $list[$key]->getRate());
            $this->assertSame($swap['Currency'], $list[$key]->getCurrency());
        }
    }

    /**
     * @test
     */
    public function testDepoDynamic(): void
    {
        [$depos, $response] = $this->createFixture(self::FIXTURES['DepoDynamic']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'DepoDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->depoDynamic($from, $to);

        $this->assertCount(\count($depos), $list);
        $this->assertContainsOnlyInstancesOf(DepoRate::class, $list);
        foreach ($depos as $key => $depo) {
            $this->assertSameDate(new DateTimeImmutable($depo['DateDepo']), $list[$key]->getDate());
            $this->assertSame($depo['Overnight'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testOstatDynamic(): void
    {
        [$depos, $response] = $this->createFixture(self::FIXTURES['OstatDynamic']);
        $from = new DateTimeImmutable('-1 month');
        $to = new DateTimeImmutable();

        $soapClient = $this->createSoapCallMock(
            'OstatDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->ostatDynamic($from, $to);

        $this->assertCount(\count($depos), $list);
        $this->assertContainsOnlyInstancesOf(OstatRate::class, $list);
        foreach ($depos as $key => $ostat) {
            $this->assertSameDate(new DateTimeImmutable($ostat['DateOst']), $list[$key]->getDate());
            $this->assertSame($ostat['InMoscow'], $list[$key]->getMoscow());
            $this->assertSame($ostat['InRuss'], $list[$key]->getRussia());
        }
    }
}
