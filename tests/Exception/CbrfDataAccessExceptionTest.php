<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Exception;

use Liquetsoft\CbrfService\Exception\CbrfDataAccessException;
use Liquetsoft\CbrfService\Tests\BaseTestCase;

/**
 * @internal
 */
class CbrfDataAccessExceptionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testGetMessageWithPathAndType(): void
    {
        $path = 'path';
        $type = 'type';

        $exception = new CbrfDataAccessException($path, $type);

        $this->assertStringContainsString($path, $exception->getMessage());
        $this->assertStringContainsString($type, $exception->getMessage());
    }

    /**
     * @test
     */
    public function testGetMessageWithPath(): void
    {
        $path = 'path';

        $exception = new CbrfDataAccessException($path);

        $this->assertStringContainsString($path, $exception->getMessage());
    }

    /**
     * @test
     */
    public function testGetMessageWithoutAll(): void
    {
        $exception = new CbrfDataAccessException();

        $this->assertNotEmpty($exception->getMessage());
    }
}
