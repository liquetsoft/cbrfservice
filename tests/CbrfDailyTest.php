<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use Marvin255\CbrfService\CbrfDaily;
use Marvin255\CbrfService\Entity\Currency;
use Marvin255\CbrfService\Entity\CurrencyRate;
use SoapClient;
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
        $dateFormat = 'Y-m-d\TH:i:s';
        [$courses, $response] = $this->getCoursesFixture();
        $onDate = new \DateTimeImmutable();

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
        $code = $courses[0]['VchCode'] ?? '';
        $onDate = new \DateTimeImmutable();

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
    public function testGetLatestDateTime(): void
    {
        $response = new stdClass();
        $response->GetLatestDateTimeResult = '2018-01-19T00:00:00';

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetLatestDateTime'))
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $date = $service->getLatestDateTime();

        $this->assertSame(
            $response->GetLatestDateTimeResult,
            $date->format('Y-m-d\TH:i:s')
        );
    }

    /**
     * @test
     */
    public function testGetLatestDateTimeSeld(): void
    {
        $response = new stdClass();
        $response->GetLatestDateTimeSeldResult = '2019-01-19T00:00:00';

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetLatestDateTimeSeld'))
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $date = $service->getLatestDateTimeSeld();

        $this->assertSame(
            $response->GetLatestDateTimeSeldResult,
            $date->format('Y-m-d\TH:i:s')
        );
    }

    /**
     * @test
     */
    public function testGetLatestDate(): void
    {
        $response = new stdClass();
        $response->GetLatestDateResult = '20190119';

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetLatestDate'))
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $date = $service->getLatestDate();

        $this->assertSame(
            '2019-01-19T00:00:00',
            $date->format('Y-m-d\TH:i:s')
        );
    }

    /**
     * @test
     */
    public function testGetLatestDateSeld(): void
    {
        $response = new stdClass();
        $response->GetLatestDateSeldResult = '20190119';

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetLatestDateSeld'))
            ->willReturn($response);

        $service = new CbrfDaily($soapClient);
        $date = $service->getLatestDateSeld();

        $this->assertSame(
            '2019-01-19T00:00:00',
            $date->format('Y-m-d\TH:i:s')
        );
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
