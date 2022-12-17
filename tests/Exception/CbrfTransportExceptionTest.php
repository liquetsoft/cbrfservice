<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Exception;

use Liquetsoft\CbrfService\Exception\CbrfTransportException;
use Liquetsoft\CbrfService\Tests\BaseTestCase;

/**
 * @internal
 */
class CbrfTransportExceptionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testGetMethod(): void
    {
        $method = 'test';

        $exception = new CbrfTransportException($method);

        $this->assertSame($method, $exception->getMethod());
    }

    /**
     * @test
     */
    public function testGetParams(): void
    {
        $method = 'test';
        $params = ['test key' => 'test value'];

        $exception = new CbrfTransportException($method, $params);

        $this->assertSame($params, $exception->getParams());
    }

    /**
     * @test
     */
    public function testGetMessageWithoutPreviuos(): void
    {
        $method = 'test';

        $exception = new CbrfTransportException($method);

        $this->assertStringContainsString($method, $exception->getMessage());
    }

    /**
     * @test
     */
    public function testGetMessageWithPreviuos(): void
    {
        $method = 'test';
        $previousMessage = 'previous';

        $exception = new CbrfTransportException($method, [], new \RuntimeException($previousMessage));

        $this->assertStringContainsString($method, $exception->getMessage());
        $this->assertStringContainsString($previousMessage, $exception->getMessage());
    }
}
