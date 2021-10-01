<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * @internal
 */
abstract class BaseTestCase extends TestCase
{
    protected function assertSameDate(DateTimeInterface $date1, DateTimeInterface $date2, string $message = "Date objects don't contain same dates"): void
    {
        $this->assertSame($date1->getTimestamp(), $date2->getTimestamp(), $message);
    }

    /**
     * @param string     $method
     * @param array|null $params
     * @param mixed      $result
     *
     * @return SoapClient
     */
    protected function createSoapCallMock(string $method, ?array $params, $result = null): SoapClient
    {
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($params === null) {
            $soapClient->expects($this->once())
                ->method('__soapCall')
                ->with(
                    $this->identicalTo($method)
                )
                ->willReturn($result)
            ;
        } else {
            $soapClient->expects($this->once())
                ->method('__soapCall')
                ->with(
                    $this->identicalTo($method),
                    $this->identicalTo([$params])
                )
                ->willReturn($result)
            ;
        }

        return $soapClient;
    }
}
