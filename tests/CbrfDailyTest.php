<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTime;
use Exception;
use Marvin255\CbrfService\CbrfDaily;
use Marvin255\CbrfService\CbrfException;
use Marvin255\CbrfService\Entity\Currency;
use Marvin255\CbrfService\Entity\CurrencyRate;
use SoapClient;
use stdClass;

class CbrfDailyTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testGetCursOnDate(): void
    {
        $dateFormat = 'Y-m-d\TH:i:s';
        [$courses, $response] = $this->getCoursesFixture();
        $onDate = new DateTime();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('GetCursOnDate'),
                $this->equalTo(
                    [
                        [
                            'On_date' => $onDate->format($dateFormat),
                        ],
                    ]
                )
            )
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $list = $service->getCursOnDate($onDate);

        $this->assertCount(4, $list);
        $this->assertContainsOnlyInstancesOf(CurrencyRate::class, $list);
        foreach ($courses as $key => $course) {
            $this->assertSame(strtoupper($course['VchCode']), $list[$key]->getChCode());
            $this->assertSame($course['Vname'], $list[$key]->getName());
            $this->assertSame($course['Vcode'], $list[$key]->getCode());
            $this->assertSame($course['Vcurs'], $list[$key]->getCurs());
            $this->assertSame($course['Vnom'], $list[$key]->getNom());
            $this->assertSame($onDate->format($dateFormat), $list[$key]->getDate()->format($dateFormat));
        }
    }

    /**
     * @test
     */
    public function testGetCursOnDateByCode(): void
    {
        $dateFormat = 'Y-m-d\TH:i:s';
        [$courses, $response] = $this->getCoursesFixture();
        $code = $courses[0]['VchCode'] ?? null;
        $onDate = new DateTime();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('GetCursOnDate'),
                $this->equalTo(
                    [
                        [
                            'On_date' => $onDate->format($dateFormat),
                        ],
                    ]
                )
            )
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $item = $service->getCursOnDateByCode($onDate, $code);

        $this->assertInstanceOf(CurrencyRate::class, $item);
        $this->assertSame(strtoupper($code), $item->getChCode());
    }

    /**
     * @test
     */
    public function testGetCursOnDateException()
    {
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetCursOnDate'))
            ->will($this->throwException(new Exception()));

        $service = new CbrfDaily($soapClient);

        $this->expectException(CbrfException::class);
        $service->getCursOnDate(new DateTime());
    }

    /**
     * @test
     */
    public function testEnumValutes(): void
    {
        $seld = false;
        [$currencies, $response] = $this->getEnumValutesFixture();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('EnumValutes'),
                $this->equalTo(
                    [
                        [
                            'Seld' => $seld,
                        ],
                    ]
                )
            )
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $list = $service->enumValutes($seld);

        $this->assertCount(4, $list);
        $this->assertContainsOnlyInstancesOf(Currency::class, $list);
        foreach ($currencies as $key => $currency) {
            $this->assertSame(strtoupper($currency['VcharCode']), $list[$key]->getCharCode());
            $this->assertSame($currency['Vname'], $list[$key]->getName());
            $this->assertSame($currency['Vcode'], $list[$key]->getCode());
            $this->assertSame($currency['VEngname'], $list[$key]->getEngName());
            $this->assertSame($currency['Vnom'], $list[$key]->getNom());
            $this->assertSame($currency['VnumCode'], $list[$key]->getNumCode());
            $this->assertSame($currency['VcommonCode'], $list[$key]->getCommonCode());
        }
    }

    /**
     * @test
     */
    public function testEnumValutesException(): void
    {
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('EnumValutes'))
            ->will($this->throwException(new Exception()));

        $service = new CbrfDaily($soapClient);

        $this->expectException(CbrfException::class);
        $service->enumValutes();
    }

    // /**
    //  * @test
    //  */
    // public function testGetLatestDateTime()
    // {
    //     $response = new stdClass();
    //     $response->GetLatestDateTimeResult = '2018-01-19T00:00:00';
    //
    //     $soapClient = $this->getMockBuilder(SoapClient::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $soapClient->method('__soapCall')
    //         ->with($this->equalTo('GetLatestDateTime'))
    //         ->will($this->returnValue($response));
    //
    //     $service = new CbrfDaily($soapClient);
    //
    //     $this->assertSame(
    //         '19.01.2018 00:00:00',
    //         $service->GetLatestDateTime(),
    //         'default format'
    //     );
    //     $this->assertSame(
    //         '01/19/2018',
    //         $service->GetLatestDateTime('m/d/Y'),
    //         'any format'
    //     );
    //     $this->assertSame(
    //         strtotime($response->GetLatestDateTimeResult),
    //         $service->GetLatestDateTime(null),
    //         'timestamp'
    //     );
    // }
    //
    // /**
    //  * @test
    //  */
    // public function testGetLatestDateTimeSeld()
    // {
    //     $response = new stdClass();
    //     $response->GetLatestDateTimeSeldResult = '2018-01-19T00:00:00';
    //
    //     $soapClient = $this->getMockBuilder(SoapClient::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $soapClient->method('__soapCall')
    //         ->with($this->equalTo('GetLatestDateTimeSeld'))
    //         ->will($this->returnValue($response));
    //
    //     $service = new CbrfDaily($soapClient);
    //
    //     $this->assertSame(
    //         '19.01.2018 00:00:00',
    //         $service->GetLatestDateTimeSeld(),
    //         'default format'
    //     );
    //     $this->assertSame(
    //         '01/19/2018',
    //         $service->GetLatestDateTimeSeld('m/d/Y'),
    //         'any format'
    //     );
    //     $this->assertSame(
    //         strtotime($response->GetLatestDateTimeSeldResult),
    //         $service->GetLatestDateTimeSeld(null),
    //         'timestamp'
    //     );
    // }
    //
    // /**
    //  * @test
    //  */
    // public function testGetLatestDate()
    // {
    //     $response = new stdClass();
    //     $response->GetLatestDateResult = '20180119';
    //
    //     $soapClient = $this->getMockBuilder(SoapClient::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $soapClient->method('__soapCall')
    //         ->with($this->equalTo('GetLatestDate'))
    //         ->will($this->returnValue($response));
    //
    //     $service = new CbrfDaily($soapClient);
    //
    //     $this->assertSame(
    //         '20180119',
    //         $service->GetLatestDate(),
    //         'default format'
    //     );
    //     $this->assertSame(
    //         '01/19/2018',
    //         $service->GetLatestDate('m/d/Y'),
    //         'any format'
    //     );
    //     $this->assertSame(
    //         strtotime('2018-01-19'),
    //         $service->GetLatestDate(null),
    //         'timestamp'
    //     );
    // }
    //
    // /**
    //  * @test
    //  */
    // public function testGetLatestDateSeld()
    // {
    //     $response = new stdClass();
    //     $response->GetLatestDateSeldResult = '20180119';
    //
    //     $soapClient = $this->getMockBuilder(SoapClient::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $soapClient->method('__soapCall')
    //         ->with($this->equalTo('GetLatestDateSeld'))
    //         ->will($this->returnValue($response));
    //
    //     $service = new CbrfDaily($soapClient);
    //
    //     $this->assertSame(
    //         '20180119',
    //         $service->GetLatestDateSeld(),
    //         'default format'
    //     );
    //     $this->assertSame(
    //         '01/19/2018',
    //         $service->GetLatestDateSeld('m/d/Y'),
    //         'any format'
    //     );
    //     $this->assertSame(
    //         strtotime('2018-01-19'),
    //         $service->GetLatestDateSeld(null),
    //         'timestamp'
    //     );
    // }

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
                'Vcurs' => floatval(mt_rand()),
                'Vnom' => mt_rand(),
            ];
        }

        $soapResponse = new stdClass();
        $soapResponse->GetCursOnDateResult = new stdClass();

        $soapResponse->GetCursOnDateResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $soapResponse->GetCursOnDateResult->any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $soapResponse->GetCursOnDateResult->any .= '<ValuteCursOnDate xmlns="">';
            foreach ($course as $key => $value) {
                $soapResponse->GetCursOnDateResult->any .= "<{$key}>{$value}</{$key}>";
            }
            $soapResponse->GetCursOnDateResult->any .= '</ValuteCursOnDate>';
        }
        $soapResponse->GetCursOnDateResult->any .= '</ValuteData>';
        $soapResponse->GetCursOnDateResult->any .= '</diffgr:diffgram>';

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

        $soapResponse = new stdClass();
        $soapResponse->EnumValutesResult = new stdClass();

        $soapResponse->EnumValutesResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $soapResponse->EnumValutesResult->any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $soapResponse->EnumValutesResult->any .= '<EnumValutes xmlns="">';
            foreach ($course as $key => $value) {
                $soapResponse->EnumValutesResult->any .= "<{$key}>{$value}</{$key}>";
            }
            $soapResponse->EnumValutesResult->any .= '</EnumValutes>';
        }
        $soapResponse->EnumValutesResult->any .= '</ValuteData>';
        $soapResponse->EnumValutesResult->any .= '</diffgr:diffgram>';

        return [$courses, $soapResponse];
    }
}
