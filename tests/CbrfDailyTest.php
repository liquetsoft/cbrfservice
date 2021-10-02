<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTimeImmutable;
use Marvin255\CbrfService\CbrfDaily;
use Marvin255\CbrfService\CbrfSoapService;
use Marvin255\CbrfService\Entity\CurrencyEnum;
use Marvin255\CbrfService\Entity\CurrencyRate;
use stdClass;

/**
 * @internal
 */
class CbrfDailyTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testGetCursOnDate(): void
    {
        [$courses, $response] = $this->getCoursesFixture();
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

        $this->assertCount(4, $list);
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
        [$courses, $response] = $this->getCoursesFixture();
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
        [$courses, $response] = $this->getCoursesFixture();
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
        [$currencies, $response] = $this->getEnumValutesFixture();
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

        $this->assertCount(4, $list);
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
        [$courses, $response] = $this->getEnumValutesFixture();
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
        [$courses, $response] = $this->getEnumValutesFixture();
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
        [$currencies, $response] = $this->getGetCursDynamicFixture();
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

        $this->assertCount(4, $list);
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
     * Returns fixture for courses checking.
     *
     * @return array
     */
    private function getCoursesFixture(): array
    {
        $courses = [];
        for ($i = 0; $i <= 3; ++$i) {
            $courses[] = [
                'VchCode' => "VchCode_{$i}",
                'Vname' => "Vname_{$i}",
                'Vcode' => mt_rand(),
                'Vcurs' => (float) (mt_rand()),
                'Vnom' => mt_rand(),
            ];
        }

        $any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $any .= '<ValuteCursOnDate xmlns="">';
            foreach ($course as $key => $value) {
                $any .= "<{$key}>{$value}</{$key}>";
            }
            $any .= '</ValuteCursOnDate>';
        }
        $any .= '</ValuteData>';
        $any .= '</diffgr:diffgram>';

        $soapResponse = new stdClass();
        $soapResponse->GetCursOnDateResult = new stdClass();
        $soapResponse->GetCursOnDateResult->any = $any;

        return [$courses, $soapResponse];
    }

    /**
     * Returns fixture for currencies checking.
     *
     * @return array
     */
    private function getEnumValutesFixture(): array
    {
        $courses = [];
        for ($i = 0; $i <= 3; ++$i) {
            $courses[] = [
                'Vcode' => "Vcode_{$i}",
                'Vname' => "Vname_{$i}",
                'VEngname' => "VEngname_{$i}",
                'Vnom' => $i,
                'VcommonCode' => "VcommonCode_{$i}",
                'VnumCode' => $i,
                'VcharCode' => "VcharCode_{$i}",
            ];
        }

        $any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $any .= '<EnumValutes xmlns="">';
            foreach ($course as $key => $value) {
                $any .= "<{$key}>{$value}</{$key}>";
            }
            $any .= '</EnumValutes>';
        }
        $any .= '</ValuteData>';
        $any .= '</diffgr:diffgram>';

        $soapResponse = new stdClass();
        $soapResponse->EnumValutesResult = new stdClass();
        $soapResponse->EnumValutesResult->any = $any;

        return [$courses, $soapResponse];
    }

    /**
     * Returns fixture for rates dynamic.
     *
     * @return array
     */
    private function getGetCursDynamicFixture(): array
    {
        $courses = [];
        for ($i = 0; $i <= 3; ++$i) {
            $courses[] = [
                'CursDate' => "2010-10-1{$i}",
                'Vcode' => "Vcode_{$i}",
                'Vnom' => $i,
                'Vcurs' => (float) (mt_rand()),
            ];
        }

        $any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $any .= '<ValuteCursDynamic xmlns="">';
            foreach ($course as $key => $value) {
                $any .= "<{$key}>{$value}</{$key}>";
            }
            $any .= '</ValuteCursDynamic>';
        }
        $any .= '</ValuteData>';
        $any .= '</diffgr:diffgram>';

        $soapResponse = new stdClass();
        $soapResponse->GetCursDynamicResult = new stdClass();
        $soapResponse->GetCursDynamicResult->any = $any;

        return [$courses, $soapResponse];
    }
}
