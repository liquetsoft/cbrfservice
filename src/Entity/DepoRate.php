<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from DepoDynamic method.
 *
 * @psalm-immutable
 */
final class DepoRate implements CbrfEntityRate
{
    private readonly float $rate;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->rate = DataHelper::float('Overnight', $item, .0);
        $this->date = DataHelper::dateTime('DateDepo', $item);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
