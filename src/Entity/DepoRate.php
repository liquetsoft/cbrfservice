<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

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
