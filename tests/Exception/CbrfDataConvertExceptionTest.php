<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Exception;

use Liquetsoft\CbrfService\Exception\CbrfDataConvertException;
use Liquetsoft\CbrfService\Tests\BaseTestCase;

/**
 * @internal
 */
final class CbrfDataConvertExceptionTest extends BaseTestCase
{
    public function testGetMessageWithoutPreviuos(): void
    {
        $from = 'from_test';
        $to = 'to_test';

        $exception = new CbrfDataConvertException($from, $to);

        $this->assertStringContainsString($from, $exception->getMessage());
        $this->assertStringContainsString($to, $exception->getMessage());
    }

    public function testGetMessageWithPreviuos(): void
    {
        $from = 'from_test';
        $to = 'to_test';
        $previousMessage = 'previous';

        $exception = new CbrfDataConvertException($from, $to, new \RuntimeException($previousMessage));

        $this->assertStringContainsString($from, $exception->getMessage());
        $this->assertStringContainsString($to, $exception->getMessage());
        $this->assertStringContainsString($previousMessage, $exception->getMessage());
    }
}
