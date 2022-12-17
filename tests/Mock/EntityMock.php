<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Mock;

/**
 * @internal
 */
class EntityMock
{
    private readonly string $test;

    public function __construct(array $input)
    {
        if (empty($input['test'])) {
            throw new \RuntimeException('Test key not found');
        }
        $this->test = (string) $input['test'];
    }

    public function getTest(): string
    {
        return $this->test;
    }
}
