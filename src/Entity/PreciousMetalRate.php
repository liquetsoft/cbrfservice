<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from DragMetDynamic method.
 *
 * @psalm-immutable
 */
class PreciousMetalRate implements Rate
{
    public const CODE_GOLD = 1;
    public const CODE_SILVER = 2;
    public const CODE_PLATINUM = 3;
    public const CODE_PALLADIUM = 4;

    private \DateTimeInterface $date;

    private int $code;

    private float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DateMet', $item);
        $this->code = DataHelper::int('CodMet', $item, 0);
        $this->rate = DataHelper::float('price', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
