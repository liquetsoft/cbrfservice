<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Transport;

use Liquetsoft\CbrfService\Exception\CbrfTransportException;
use Liquetsoft\CbrfService\Tests\BaseTestCase;
use Liquetsoft\CbrfService\Transport\CbrfSoapTransport;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
class CbrfSoapTransportTest extends BaseTestCase
{
    /**
     * @test
     *
     * @dataProvider queryProvider
     */
    public function testQueryXmlResult(string $method, ?array $rawParams, ?array $queryParams, object $responseXml, array $response): void
    {
        /** @var MockObject&\SoapClient */
        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->identicalTo($method),
                $this->identicalTo($queryParams)
            )
            ->willReturn($responseXml);

        $service = new CbrfSoapTransport($soapClient);
        $testResponse = $service->query($method, $rawParams);

        $this->assertSame($response, $testResponse);
    }

    public static function queryProvider(): array
    {
        $responseXml = new \stdClass();
        $responseXml->TestSoapAnyXmlResult = new \stdClass();
        $responseXml->TestSoapAnyXmlResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $responseXml->TestSoapAnyXmlResult->any .= '<test><nested>value</nested></test>';
        $responseXml->TestSoapAnyXmlResult->any .= '</diffgr:diffgram>';

        $resultXml = [
            'test' => [
                'nested' => 'value',
            ],
        ];

        $regularResponse = new \stdClass();
        $regularResponse->test = 'value';

        $resultRegular = [
            'test' => 'value',
        ];

        $dateTime = new \DateTimeImmutable();

        return [
            "xml response inside 'any' parameter" => [
                'TestSoapAnyXml',
                [
                    'param_name' => 'param_value',
                    'param_name_1' => 'param_value_1',
                ],
                [
                    [
                        'param_name' => 'param_value',
                        'param_name_1' => 'param_value_1',
                    ],
                ],
                $responseXml,
                $resultXml,
            ],
            'regular soap response' => [
                'TestRegularSoap',
                [
                    'param_name' => 'param_value',
                    'param_name_1' => 'param_value_1',
                ],
                [
                    [
                        'param_name' => 'param_value',
                        'param_name_1' => 'param_value_1',
                    ],
                ],
                $regularResponse,
                $resultRegular,
            ],
            'dateTime parameter conversion' => [
                'TestSoapAnyXml',
                [
                    'date_time' => $dateTime,
                ],
                [
                    [
                        'date_time' => $dateTime->format(CbrfSoapTransport::DATE_TIME_FORMAT),
                    ],
                ],
                $responseXml,
                $resultXml,
            ],
            'no params' => [
                'TestSoapAnyXml',
                null,
                [],
                $responseXml,
                $resultXml,
            ],
        ];
    }

    /**
     * @test
     */
    public function testQueryException(): void
    {
        /** @var MockObject&\SoapClient */
        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')->willThrowException(new \Exception());

        $service = new CbrfSoapTransport($soapClient);

        $this->expectException(CbrfTransportException::class);
        $service->query('test');
    }
}
