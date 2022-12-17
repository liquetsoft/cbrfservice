<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests;

use Liquetsoft\CbrfService\CbrfSoapService;
use Liquetsoft\CbrfService\Exception\CbrfException;

/**
 * @internal
 */
class CbrfSoapServiceTest extends BaseTestCase
{
    /**
     * @test
     *
     * @dataProvider queryProvider
     */
    public function testQueryXmlResult(string $method, array $params, object $responseXml, array $response): void
    {
        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->identicalTo($method),
                $this->identicalTo([$params])
            )
            ->willReturn($responseXml);

        $service = new CbrfSoapService($soapClient);
        $testResponse = $service->query($method, $params);

        $this->assertSame($response, $testResponse);
    }

    public function queryProvider(): array
    {
        $responseXml = new \stdClass();
        $responseXml->TestSoapAnyXmlResult = new \stdClass();
        $responseXml->TestSoapAnyXmlResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $responseXml->TestSoapAnyXmlResult->any .= '<test><nested>value</nested></test>';
        $responseXml->TestSoapAnyXmlResult->any .= '</diffgr:diffgram>';

        $regularResponse = new \stdClass();
        $regularResponse->test = 'value';

        return [
            "xml response inside 'any' parameter" => [
                'TestSoapAnyXml',
                [
                    'param_name' => 'param_value',
                    'param_name_1' => 'param_value_1',
                ],
                $responseXml,
                [
                    'test' => ['nested' => 'value'],
                ],
            ],
            'regular soap response' => [
                'TestRegularSoap',
                [
                    'param_name' => 'param_value',
                    'param_name_1' => 'param_value_1',
                ],
                $regularResponse,
                [
                    'test' => 'value',
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function testQueryException(): void
    {
        $soapClient = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('EnumValutes'))
            ->will($this->throwException(new \Exception()));

        $service = new CbrfSoapService($soapClient);

        $this->expectException(CbrfException::class);
        $service->query('test');
    }
}
