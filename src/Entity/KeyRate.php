<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from KeyRate method.
 *
 * @psalm-immutable
 */
final class KeyRate implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DT', $item);
        $this->rate = DataHelper::float('Rate', $item, .0);
    }

    #[\Override]
    public function getRate(): float
    {
        return $this->rate;
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
