<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests;

use Liquetsoft\CbrfService\CbrfDaily;
use Liquetsoft\CbrfService\CbrfEntityCurrencyInternal;
use Liquetsoft\CbrfService\Entity\CurrencyEnum;
use Liquetsoft\CbrfService\Entity\CurrencyRate;
use Liquetsoft\CbrfService\Entity\DepoRate;
use Liquetsoft\CbrfService\Entity\Dv;
use Liquetsoft\CbrfService\Entity\InternationalReserve;
use Liquetsoft\CbrfService\Entity\InternationalReserveWeek;
use Liquetsoft\CbrfService\Entity\KeyRate;
use Liquetsoft\CbrfService\Entity\Mkr;
use Liquetsoft\CbrfService\Entity\OstatDepoRate;
use Liquetsoft\CbrfService\Entity\OstatRate;
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\RepoDebt;
use Liquetsoft\CbrfService\Entity\ReutersCurrency;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapRate;
use PHPUnit\Framework\MockObject\MockObject;

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
            'path' => 'ValuteData.ValuteCursOnDate',
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
            'path' => 'ValuteData.EnumValutes',
        ],
        'CursDynamic' => [
            'schema' => [
                'CursDate' => self::FIXTURE_TYPE_DATE,
                'Vcurs' => self::FIXTURE_TYPE_FLOAT,
                'Vcode' => self::FIXTURE_TYPE_STRING,
                'Vnom' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'ValuteData.ValuteCursDynamic',
        ],
        'KeyRate' => [
            'schema' => [
                'DT' => self::FIXTURE_TYPE_DATE,
                'Rate' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'KeyRate.KR',
        ],
        'DragMetDynamic' => [
            'schema' => [
                'DateMet' => self::FIXTURE_TYPE_DATE,
                'price' => self::FIXTURE_TYPE_FLOAT,
                'CodMet' => [1, 2, 3, 4],
            ],
            'path' => 'DragMetall.DrgMet',
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
            'path' => 'SwapDynamic.Swap',
        ],
        'DepoDynamic' => [
            'schema' => [
                'Overnight' => self::FIXTURE_TYPE_FLOAT,
                'DateDepo' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'DepoDynamic.Depo',
        ],
        'OstatDynamic' => [
            'schema' => [
                'DateOst' => self::FIXTURE_TYPE_DATE,
                'InMoscow' => self::FIXTURE_TYPE_FLOAT,
                'InRuss' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'OstatDynamic.Ostat',
        ],
        'OstatDepo' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'D1_7' => self::FIXTURE_TYPE_FLOAT,
                'total' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'OD.odr',
        ],
        'Mrrf' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'p1' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'mmrf.mr',
        ],
        'Mrrf7D' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'val' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'mmrf7d.mr',
        ],
        'Saldo' => [
            'schema' => [
                'Dt' => self::FIXTURE_TYPE_DATE,
                'DEADLINEBS' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'Saldo.So',
        ],
        'RuoniaSV' => [
            'schema' => [
                'DT' => self::FIXTURE_TYPE_DATE,
                'RUONIA_Index' => self::FIXTURE_TYPE_FLOAT,
                'RUONIA_AVG_1M' => self::FIXTURE_TYPE_FLOAT,
                'RUONIA_AVG_3M' => self::FIXTURE_TYPE_FLOAT,
                'RUONIA_AVG_6M' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'RuoniaSV.ra',
        ],
        'Ruonia' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'ruo' => self::FIXTURE_TYPE_FLOAT,
                'vol' => self::FIXTURE_TYPE_FLOAT,
                'DateUpdate' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'Ruonia.ro',
        ],
        'MKR' => [
            'schema' => [
                'CDate' => self::FIXTURE_TYPE_DATE,
                'p1' => self::FIXTURE_TYPE_INT,
                'd1' => self::FIXTURE_TYPE_FLOAT,
                'd7' => self::FIXTURE_TYPE_FLOAT,
                'd30' => self::FIXTURE_TYPE_FLOAT,
                'd90' => self::FIXTURE_TYPE_FLOAT,
                'd180' => self::FIXTURE_TYPE_FLOAT,
                'd360' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'mkr_base.MKR',
        ],
        'DV' => [
            'schema' => [
                'Date' => self::FIXTURE_TYPE_DATE,
                'VIDate' => self::FIXTURE_TYPE_DATE,
                'VOvern' => self::FIXTURE_TYPE_FLOAT,
                'VLomb' => self::FIXTURE_TYPE_FLOAT,
                'VIDay' => self::FIXTURE_TYPE_FLOAT,
                'VOther' => self::FIXTURE_TYPE_FLOAT,
                'Vol_Gold' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'DV_base.DV',
        ],
        'RepoDebt' => [
            'schema' => [
                'Date' => self::FIXTURE_TYPE_DATE,
                'debt' => self::FIXTURE_TYPE_FLOAT,
                'debt_auc' => self::FIXTURE_TYPE_FLOAT,
                'debt_fix' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'Repo_debt.RD',
        ],
        'EnumReutersValutes' => [
            'schema' => [
                'char_code' => self::FIXTURE_TYPE_STRING,
                'Title_ru' => self::FIXTURE_TYPE_STRING,
                'Title_en' => self::FIXTURE_TYPE_STRING,
                'num_code' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'ReutersValutesList.EnumRValutes',
        ],
    ];

    /**
     * @test
     */
    public function testGetCursOnDate(): void
    {
        [$courses, $response] = $this->createFixture(self::FIXTURES['CurrencyRate']);
        $onDate = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->getCursOnDate($onDate);

        $this->assertCount(\count($courses), $list);
        $this->assertContainsOnlyInstancesOf(CurrencyRate::class, $list);
        foreach ($courses as $key => $course) {
            $this->assertSame(strtoupper((string) $course['VchCode']), $list[$key]->getCharCode());
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
        $charCode = (string) ($courses[0]['VchCode'] ?? '');
        $onDate = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate,
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
        $numericCode = (int) ($courses[0]['Vcode'] ?? 0);
        $onDate = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'GetCursOnDate',
            [
                'On_date' => $onDate,
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

        $soapClient = $this->createTransportMock(
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
            $this->assertSame(strtoupper((string) $currency['VcharCode']), $list[$key]->getCharCode());
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
        $charCode = (string) ($courses[0]['VcharCode'] ?? '');
        $seld = false;

        $soapClient = $this->createTransportMock(
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
        $numericCode = (int) ($courses[0]['VnumCode'] ?? 0);
        $seld = false;

        $soapClient = $this->createTransportMock(
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
        $date = new \DateTimeImmutable();
        $response = [
            'GetLatestDateTimeResult' => $date->format(\DateTimeInterface::ATOM),
        ];

        $soapClient = $this->createTransportMock(
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
        $date = new \DateTimeImmutable();
        $response = [
            'GetLatestDateTimeSeldResult' => $date->format(\DateTimeInterface::ATOM),
        ];

        $soapClient = $this->createTransportMock(
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
        $date = new \DateTimeImmutable();
        $response = [
            'GetLatestDateResult' => $date->format(\DateTimeInterface::ATOM),
        ];

        $soapClient = $this->createTransportMock(
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
        $date = new \DateTimeImmutable();
        $response = [
            'GetLatestDateSeldResult' => $date->format(\DateTimeInterface::ATOM),
        ];

        $soapClient = $this->createTransportMock(
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
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();
        $charCode = 'EUR';
        $numericCode = 978;
        $name = 'Euro';
        $internalCode = 'test01';

        /** @var MockObject&CbrfEntityCurrencyInternal */
        $currencyEnum = $this->getMockBuilder(CbrfEntityCurrencyInternal::class)->getMock();
        $currencyEnum->method('getInternalCode')->willReturn($internalCode);
        $currencyEnum->method('getName')->willReturn($name);
        $currencyEnum->method('getCharCode')->willReturn($charCode);
        $currencyEnum->method('getNumericCode')->willReturn($numericCode);

        $soapClient = $this->createTransportMock(
            'GetCursDynamic',
            [
                'FromDate' => $from,
                'ToDate' => $to,
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
            $this->assertSameDate($currency['CursDate'], $list[$key]->getDate());
        }
    }

    /**
     * @test
     */
    public function testKeyRate(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['KeyRate']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'KeyRate',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->keyRate($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(KeyRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSameDate($rate['DT'], $list[$key]->getDate());
            $this->assertSame($rate['Rate'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testDragMetDynamic(): void
    {
        [$metals, $response] = $this->createFixture(self::FIXTURES['DragMetDynamic']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'DragMetDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->dragMetDynamic($from, $to);

        $this->assertCount(\count($metals), $list);
        $this->assertContainsOnlyInstancesOf(PreciousMetalRate::class, $list);
        foreach ($metals as $key => $metal) {
            $this->assertSameDate($metal['DateMet'], $list[$key]->getDate());
            $this->assertSame($metal['CodMet'], $list[$key]->getCode()->value);
            $this->assertSame($metal['price'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testSwapDynamic(): void
    {
        [$swaps, $response] = $this->createFixture(self::FIXTURES['SwapDynamic']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'SwapDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->swapDynamic($from, $to);

        $this->assertCount(\count($swaps), $list);
        $this->assertContainsOnlyInstancesOf(SwapRate::class, $list);
        foreach ($swaps as $key => $swap) {
            $this->assertSameDate($swap['DateBuy'], $list[$key]->getDateBuy());
            $this->assertSameDate($swap['DateSell'], $list[$key]->getDateSell());
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
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'DepoDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->depoDynamic($from, $to);

        $this->assertCount(\count($depos), $list);
        $this->assertContainsOnlyInstancesOf(DepoRate::class, $list);
        foreach ($depos as $key => $depo) {
            $this->assertSameDate($depo['DateDepo'], $list[$key]->getDate());
            $this->assertSame($depo['Overnight'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testOstatDynamic(): void
    {
        [$depos, $response] = $this->createFixture(self::FIXTURES['OstatDynamic']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'OstatDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->ostatDynamic($from, $to);

        $this->assertCount(\count($depos), $list);
        $this->assertContainsOnlyInstancesOf(OstatRate::class, $list);
        foreach ($depos as $key => $ostat) {
            $this->assertSameDate($ostat['DateOst'], $list[$key]->getDate());
            $this->assertSame($ostat['InMoscow'], $list[$key]->getMoscow());
            $this->assertSame($ostat['InRuss'], $list[$key]->getRussia());
        }
    }

    /**
     * @test
     */
    public function testOstatDepo(): void
    {
        [$depos, $response] = $this->createFixture(self::FIXTURES['OstatDepo']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'OstatDepo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->ostatDepo($from, $to);

        $this->assertCount(\count($depos), $list);
        $this->assertContainsOnlyInstancesOf(OstatDepoRate::class, $list);
        foreach ($depos as $key => $ostat) {
            $this->assertSameDate($ostat['D0'], $list[$key]->getDate());
            $this->assertSame($ostat['D1_7'], $list[$key]->getDays1to7());
            $this->assertSame($ostat['total'], $list[$key]->getTotal());
        }
    }

    /**
     * @test
     */
    public function testMrrf(): void
    {
        [$mrrfs, $response] = $this->createFixture(self::FIXTURES['Mrrf']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'mrrf',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->mrrf($from, $to);

        $this->assertCount(\count($mrrfs), $list);
        $this->assertContainsOnlyInstancesOf(InternationalReserve::class, $list);
        foreach ($mrrfs as $key => $mrrf) {
            $this->assertSameDate($mrrf['D0'], $list[$key]->getDate());
            $this->assertSame($mrrf['p1'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testMrrf7d(): void
    {
        [$mrrfs, $response] = $this->createFixture(self::FIXTURES['Mrrf7D']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'mrrf7D',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->mrrf7d($from, $to);

        $this->assertCount(\count($mrrfs), $list);
        $this->assertContainsOnlyInstancesOf(InternationalReserveWeek::class, $list);
        foreach ($mrrfs as $key => $mrrf) {
            $this->assertSameDate($mrrf['D0'], $list[$key]->getDate());
            $this->assertSame($mrrf['val'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testSaldo(): void
    {
        [$saldos, $response] = $this->createFixture(self::FIXTURES['Saldo']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'Saldo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->saldo($from, $to);

        $this->assertCount(\count($saldos), $list);
        $this->assertContainsOnlyInstancesOf(Saldo::class, $list);
        foreach ($saldos as $key => $saldo) {
            $this->assertSameDate($saldo['Dt'], $list[$key]->getDate());
            $this->assertSame($saldo['DEADLINEBS'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testRuoniaSV(): void
    {
        [$ruoniaIndexes, $response] = $this->createFixture(self::FIXTURES['RuoniaSV']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'RuoniaSV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->ruoniaSV($from, $to);

        $this->assertCount(\count($ruoniaIndexes), $list);
        $this->assertContainsOnlyInstancesOf(RuoniaIndex::class, $list);
        foreach ($ruoniaIndexes as $key => $ruoniaIndex) {
            $this->assertSameDate($ruoniaIndex['DT'], $list[$key]->getDate());
            $this->assertSame($ruoniaIndex['RUONIA_Index'], $list[$key]->getIndex());
            $this->assertSame($ruoniaIndex['RUONIA_AVG_1M'], $list[$key]->getAverage1Month());
            $this->assertSame($ruoniaIndex['RUONIA_AVG_3M'], $list[$key]->getAverage3Month());
            $this->assertSame($ruoniaIndex['RUONIA_AVG_6M'], $list[$key]->getAverage6Month());
        }
    }

    /**
     * @test
     */
    public function testRuonia(): void
    {
        [$ruoniaBids, $response] = $this->createFixture(self::FIXTURES['Ruonia']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'Ruonia',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->ruonia($from, $to);

        $this->assertCount(\count($ruoniaBids), $list);
        $this->assertContainsOnlyInstancesOf(RuoniaBid::class, $list);
        foreach ($ruoniaBids as $key => $ruoniaBid) {
            $this->assertSameDate($ruoniaBid['D0'], $list[$key]->getDate());
            $this->assertSame($ruoniaBid['ruo'], $list[$key]->getRate());
            $this->assertSame($ruoniaBid['vol'], $list[$key]->getDealsVolume());
            $this->assertSameDate($ruoniaBid['DateUpdate'], $list[$key]->getDateUpdate());
        }
    }

    /**
     * @test
     */
    public function testMKR(): void
    {
        [$mkrs, $response] = $this->createFixture(self::FIXTURES['MKR']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'MKR',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->mkr($from, $to);

        $this->assertCount(\count($mkrs), $list);
        $this->assertContainsOnlyInstancesOf(Mkr::class, $list);
        foreach ($mkrs as $key => $mkr) {
            $this->assertSameDate($mkr['CDate'], $list[$key]->getDate());
            $this->assertSame($mkr['p1'], $list[$key]->getP1());
            $this->assertSame($mkr['d1'], $list[$key]->getD1());
            $this->assertSame($mkr['d7'], $list[$key]->getD7());
            $this->assertSame($mkr['d30'], $list[$key]->getD30());
            $this->assertSame($mkr['d90'], $list[$key]->getD90());
            $this->assertSame($mkr['d180'], $list[$key]->getD180());
            $this->assertSame($mkr['d360'], $list[$key]->getD360());
        }
    }

    /**
     * @test
     */
    public function testDV(): void
    {
        [$dvs, $response] = $this->createFixture(self::FIXTURES['DV']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'DV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->dv($from, $to);

        $this->assertCount(\count($dvs), $list);
        $this->assertContainsOnlyInstancesOf(Dv::class, $list);
        foreach ($dvs as $key => $dv) {
            $this->assertSameDate($dv['Date'], $list[$key]->getDate());
            $this->assertSameDate($dv['VIDate'], $list[$key]->getVIDate());
            $this->assertSame($dv['VOvern'], $list[$key]->getVOvern());
            $this->assertSame($dv['VLomb'], $list[$key]->getVLomb());
            $this->assertSame($dv['VIDay'], $list[$key]->getVIDay());
            $this->assertSame($dv['VOther'], $list[$key]->getVOther());
            $this->assertSame($dv['Vol_Gold'], $list[$key]->getVGold());
        }
    }

    /**
     * @test
     */
    public function testRepoDebt(): void
    {
        [$debts, $response] = $this->createFixture(self::FIXTURES['RepoDebt']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'Repo_debt',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->repoDebt($from, $to);

        $this->assertCount(\count($debts), $list);
        $this->assertContainsOnlyInstancesOf(RepoDebt::class, $list);
        foreach ($debts as $key => $debt) {
            $this->assertSameDate($debt['Date'], $list[$key]->getDate());
            $this->assertSame($debt['debt'], $list[$key]->getRate());
            $this->assertSame($debt['debt_auc'], $list[$key]->getDebtAuc());
            $this->assertSame($debt['debt_fix'], $list[$key]->getDebtFix());
        }
    }

    /**
     * @test
     */
    public function testEnumReutersValutes(): void
    {
        [$currencies, $response] = $this->createFixture(self::FIXTURES['EnumReutersValutes']);
        $onDate = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'EnumReutersValutes',
            [
                'On_date' => $onDate,
            ],
            $response
        );

        $service = new CbrfDaily($soapClient);
        $list = $service->enumReutersValutes($onDate);

        $this->assertCount(\count($currencies), $list);
        $this->assertContainsOnlyInstancesOf(ReutersCurrency::class, $list);
        foreach ($currencies as $key => $currency) {
            $this->assertSame(strtoupper((string) $currency['char_code']), $list[$key]->getCharCode());
            $this->assertSame($currency['Title_ru'], $list[$key]->getName());
            $this->assertSame($currency['Title_en'], $list[$key]->getNameEn());
            $this->assertSame($currency['num_code'], $list[$key]->getNumericCode());
            $this->assertSame(1, $list[$key]->getNom());
        }
    }
}
