<?php

namespace marvin255\cbrfservice\tests;

use marvin255\cbrfservice\CbrfDaily;

class CbrfDailyTest extends BaseTestCase
{
    // public function testConstructSoapClientException()
    // {
    //     $wsdl = 'wsdl_' . mt_rand();
    //     $this->setExpectedException('\InvalidArgumentException', $wsdl);
    //     $service = new CbrfDaily($wsdl);
    // }

    public function testConstructWrongClientType()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $service = new CbrfDaily(123);
    }

    public function testGetCursOnDate()
    {
        list($courses, $response) = $this->getCoursesFixture();
        $time = time();

        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->expects($this->once())
            ->method('__soapCall')
            ->with(
                $this->equalTo('GetCursOnDate'),
                $this->equalTo([['On_date' => date('Y-m-d\TH:i:s', $time)]])
            )
            ->will($this->returnValue($response));

        $service = new CbrfDaily($soapClient);

        $this->assertSame($courses, $service->GetCursOnDate($time));
    }

    public function testGetCursOnDateCurrency()
    {
        list($courses, $response) = $this->getCoursesFixture();
        $time = time();

        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('GetCursOnDate'),
                $this->equalTo([['On_date' => date('Y-m-d\TH:i:s', $time)]])
            )
            ->will($this->returnValue($response));

        $service = new CbrfDaily($soapClient);

        $this->assertSame(
            $courses[2],
            $service->GetCursOnDate(date('d.m.Y H:i:s', $time), 'VchCode_2'),
            'by VchCode'
        );
        $this->assertSame(
            $courses[1],
            $service->GetCursOnDate(date('Y-m-d\TH:i:sP', $time), 'Vname_1'),
            'by Vname'
        );
        $this->assertSame(
            $courses[0],
            $service->GetCursOnDate($time, 'Vcode_0'),
            'by Vcode'
        );
    }

    protected function getCoursesFixture()
    {
        $courses = [
            [
                'VchCode' => 'VchCode_0',
                'Vname' => 'Vname_0',
                'Vcode' => 'Vcode_0',
                'Vcurs' => floatval(mt_rand()),
                'Vnom' => floatval(mt_rand()),
            ],
            [
                'VchCode' => 'VchCode_1',
                'Vname' => 'Vname_1',
                'Vcode' => 'Vcode_1',
                'Vcurs' => floatval(mt_rand()),
                'Vnom' => floatval(mt_rand()),
            ],
            [
                'VchCode' => 'VchCode_2',
                'Vname' => 'Vname_2',
                'Vcode' => 'Vcode_2',
                'Vcurs' => floatval(mt_rand()),
                'Vnom' => floatval(mt_rand()),
            ],
        ];

        $soapResponse = new \stdClass;
        $soapResponse->GetCursOnDateResult = new \stdClass;

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
}
