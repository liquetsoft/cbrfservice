<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from DragMetDynamic method.
 *
 * @psalm-immutable
 */
final class PreciousMetalRate implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly PreciousMetalCode $code;

    private readonly float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DateMet', $item);
        $this->code = DataHelper::enumInt('CodMet', $item, PreciousMetalCode::class);
        $this->rate = DataHelper::float('price', $item, .0);
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getCode(): PreciousMetalCode
    {
        return $this->code;
    }

    #[\Override]
    public function getRate(): float
    {
        return $this->rate;
    }
}
