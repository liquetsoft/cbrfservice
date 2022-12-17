<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityDate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from OstatDynamic method.
 *
 * @psalm-immutable
 */
final class OstatRate implements CbrfEntityDate
{
    private readonly float $moscow;

    private readonly float $russia;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DateOst', $item);
        $this->moscow = DataHelper::float('InMoscow', $item, .0);
        $this->russia = DataHelper::float('InRuss', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getMoscow(): float
    {
        return $this->moscow;
    }

    public function getRussia(): float
    {
        return $this->russia;
    }
}
