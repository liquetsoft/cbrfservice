<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests;

use Liquetsoft\CbrfService\CbrfDaily;
use Liquetsoft\CbrfService\CbrfFactory;

/**
 * @internal
 */
final class CbrfFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testCreateDaily(): void
    {
        $daily = CbrfFactory::createDaily();

        $this->assertInstanceOf(CbrfDaily::class, $daily);
    }
}
