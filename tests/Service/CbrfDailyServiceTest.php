<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Service;

use Liquetsoft\CbrfService\CbrfEntityCurrencyInternal;
use Liquetsoft\CbrfService\Entity\BiCurBacketItem;
use Liquetsoft\CbrfService\Entity\BiCurBaseRate;
use Liquetsoft\CbrfService\Entity\BliquidityRate;
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
use Liquetsoft\CbrfService\Entity\OvernightRate;
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\RepoDebt;
use Liquetsoft\CbrfService\Entity\RepoDebtUSDRate;
use Liquetsoft\CbrfService\Entity\ReutersCurrency;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapDayTotalRate;
use Liquetsoft\CbrfService\Entity\SwapInfoSellItem;
use Liquetsoft\CbrfService\Entity\SwapInfoSellVolItem;
use Liquetsoft\CbrfService\Entity\SwapMonthTotalRate;
use Liquetsoft\CbrfService\Entity\SwapRate;
use Liquetsoft\CbrfService\Service\CbrfDailyService;
use Liquetsoft\CbrfService\Tests\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
final class CbrfDailyServiceTest extends BaseTestCase
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
        'GetReutersCursOnDate' => [
            'schema' => [
                'val' => self::FIXTURE_TYPE_FLOAT,
                'dir' => self::FIXTURE_TYPE_INT,
                'num_code' => self::FIXTURE_TYPE_INT,
            ],
            'path' => 'ReutersValutesData.Currency',
        ],
        'Overnight' => [
            'schema' => [
                'stavka' => self::FIXTURE_TYPE_FLOAT,
                'date' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'Overnight.OB',
        ],
        'SwapDayTotal' => [
            'schema' => [
                'Swap' => self::FIXTURE_TYPE_FLOAT,
                'DT' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'SwapDayTotal.SDT',
        ],
        'SwapMonthTotal' => [
            'schema' => [
                'RUB' => self::FIXTURE_TYPE_FLOAT,
                'EUR' => self::FIXTURE_TYPE_FLOAT,
                'USD' => self::FIXTURE_TYPE_FLOAT,
                'D0' => self::FIXTURE_TYPE_DATE,
            ],
            'path' => 'SwapMonthTotal.SMT',
        ],
        'SwapInfoSell' => [
            'schema' => [
                'Currency' => [0, 1, 2],
                'DateBuy' => self::FIXTURE_TYPE_DATE,
                'DateSell' => self::FIXTURE_TYPE_DATE,
                'DateSPOT' => self::FIXTURE_TYPE_DATE,
                'Type' => [0],
                'BaseRate' => self::FIXTURE_TYPE_FLOAT,
                'SD' => self::FIXTURE_TYPE_FLOAT,
                'TIR' => self::FIXTURE_TYPE_FLOAT,
                'Stavka' => self::FIXTURE_TYPE_FLOAT,
                'limit' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'SwapInfoSell.SSU',
        ],
        'SwapInfoSellVol' => [
            'schema' => [
                'Currency' => [0, 1, 2],
                'DT' => self::FIXTURE_TYPE_DATE,
                'Type' => [0],
                'VOL_FC' => self::FIXTURE_TYPE_FLOAT,
                'VOL_RUB' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'SwapInfoSellVol.SSUV',
        ],
        'Bliquidity' => [
            'schema' => [
                'DT' => self::FIXTURE_TYPE_DATE,
                'StrLiDef' => self::FIXTURE_TYPE_FLOAT,
                'claims' => self::FIXTURE_TYPE_FLOAT,
                'actionBasedRepoFX' => self::FIXTURE_TYPE_FLOAT,
                'actionBasedSecureLoans' => self::FIXTURE_TYPE_FLOAT,
                'standingFacilitiesRepoFX' => self::FIXTURE_TYPE_FLOAT,
                'standingFacilitiesSecureLoans' => self::FIXTURE_TYPE_FLOAT,
                'liabilities' => self::FIXTURE_TYPE_FLOAT,
                'depositAuctionBased' => self::FIXTURE_TYPE_FLOAT,
                'depositStandingFacilities' => self::FIXTURE_TYPE_FLOAT,
                'CBRbonds' => self::FIXTURE_TYPE_FLOAT,
                'netCBRclaims' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'Bliquidity.BL',
        ],
        'BiCurBase' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'VAL' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'BiCurBase.BCB',
        ],
        'BiCurBacket' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'USD' => self::FIXTURE_TYPE_FLOAT,
                'EUR' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'BiCurBacket.BC',
        ],
        'RepoDebtUSD' => [
            'schema' => [
                'D0' => self::FIXTURE_TYPE_DATE,
                'TP' => self::FIXTURE_TYPE_FLOAT,
            ],
            'path' => 'RepoDebtUSD.rd',
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

        $service = new CbrfDailyService($soapClient);
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

    /**
     * @test
     */
    public function testGetReutersCursOnDate(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['GetReutersCursOnDate']);
        $onDate = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'GetReutersCursOnDate',
            [
                'On_date' => $onDate,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->getReutersCursOnDate($onDate);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(ReutersCurrencyRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['val'], $list[$key]->getRate());
            $this->assertSame($rate['dir'], $list[$key]->getDir());
            $this->assertSame($rate['num_code'], $list[$key]->getNumericCode());
            $this->assertSameDate($onDate, $list[$key]->getDate());
        }
    }

    /**
     * @test
     */
    public function testOvernight(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['Overnight']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'Overnight',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->overnight($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(OvernightRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['date'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['stavka'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testSwapDayTotal(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['SwapDayTotal']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'SwapDayTotal',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->swapDayTotal($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(SwapDayTotalRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['DT'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['Swap'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testSwapMonthTotal(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['SwapMonthTotal']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'SwapMonthTotal',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->swapMonthTotal($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(SwapMonthTotalRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['D0'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['RUB'], $list[$key]->getRate());
            $this->assertSame($rate['EUR'], $list[$key]->getEUR());
            $this->assertSame($rate['USD'], $list[$key]->getUSD());
        }
    }

    /**
     * @test
     */
    public function testSwapInfoSell(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['SwapInfoSell']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'SwapInfoSell',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->swapInfoSell($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(SwapInfoSellItem::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['Currency'], $list[$key]->getCurrency()->value);
            $this->assertSame($rate['DateBuy'], $list[$key]->getDateBuy()->format('Y-m-d'));
            $this->assertSame($rate['DateSell'], $list[$key]->getDateSell()->format('Y-m-d'));
            $this->assertSame($rate['DateSPOT'], $list[$key]->getDateSPOT()->format('Y-m-d'));
            $this->assertSame($rate['Type'], $list[$key]->getType());
            $this->assertSame($rate['BaseRate'], $list[$key]->getBaseRate());
            $this->assertSame($rate['SD'], $list[$key]->getSD());
            $this->assertSame($rate['TIR'], $list[$key]->getTIR());
            $this->assertSame($rate['Stavka'], $list[$key]->getRate());
            $this->assertSame($rate['limit'], $list[$key]->getLimit());
        }
    }

    /**
     * @test
     */
    public function testSwapInfoSellVol(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['SwapInfoSellVol']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'SwapInfoSellVol',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->swapInfoSellVol($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(SwapInfoSellVolItem::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['Currency'], $list[$key]->getCurrency()->value);
            $this->assertSame($rate['DT'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['Type'], $list[$key]->getType());
            $this->assertSame($rate['VOL_FC'], $list[$key]->getVolumeForeignCurrency());
            $this->assertSame($rate['VOL_RUB'], $list[$key]->getVolumeRub());
        }
    }

    /**
     * @test
     */
    public function testBLiquidity(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['Bliquidity']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'Bliquidity',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->bLiquidity($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(BliquidityRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['DT'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['StrLiDef'], $list[$key]->getRate());
            $this->assertSame($rate['claims'], $list[$key]->getClaims());
            $this->assertSame($rate['actionBasedRepoFX'], $list[$key]->getActionBasedRepoFX());
            $this->assertSame($rate['actionBasedSecureLoans'], $list[$key]->getActionBasedSecureLoans());
            $this->assertSame($rate['standingFacilitiesRepoFX'], $list[$key]->getStandingFacilitiesRepoFX());
            $this->assertSame($rate['standingFacilitiesSecureLoans'], $list[$key]->getStandingFacilitiesSecureLoans());
            $this->assertSame($rate['liabilities'], $list[$key]->getLiabilities());
            $this->assertSame($rate['depositAuctionBased'], $list[$key]->getDepositAuctionBased());
            $this->assertSame($rate['depositStandingFacilities'], $list[$key]->getDepositStandingFacilities());
            $this->assertSame($rate['CBRbonds'], $list[$key]->getCBRbonds());
            $this->assertSame($rate['netCBRclaims'], $list[$key]->getNetCBRclaims());
        }
    }

    /**
     * @test
     */
    public function testBiCurBase(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['BiCurBase']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'BiCurBase',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->biCurBase($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(BiCurBaseRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['D0'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['VAL'], $list[$key]->getRate());
        }
    }

    /**
     * @test
     */
    public function testBiCurBacket(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['BiCurBacket']);

        $soapClient = $this->createTransportMock(
            'BiCurBacket',
            null,
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->biCurBacket();

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(BiCurBacketItem::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['D0'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['USD'], $list[$key]->getUSD());
            $this->assertSame($rate['EUR'], $list[$key]->getEUR());
        }
    }

    /**
     * @test
     */
    public function testRepoDebtUSD(): void
    {
        [$rates, $response] = $this->createFixture(self::FIXTURES['RepoDebtUSD']);
        $from = new \DateTimeImmutable('-1 month');
        $to = new \DateTimeImmutable();

        $soapClient = $this->createTransportMock(
            'RepoDebtUSD',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ],
            $response
        );

        $service = new CbrfDailyService($soapClient);
        $list = $service->repoDebtUSD($from, $to);

        $this->assertCount(\count($rates), $list);
        $this->assertContainsOnlyInstancesOf(RepoDebtUSDRate::class, $list);
        foreach ($rates as $key => $rate) {
            $this->assertSame($rate['D0'], $list[$key]->getDate()->format('Y-m-d'));
            $this->assertSame($rate['TP'], $list[$key]->getRate());
        }
    }
}
