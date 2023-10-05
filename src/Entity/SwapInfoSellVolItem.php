<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from SwapInfoSellVol method.
 *
 * @psalm-immutable
 */
final class SwapInfoSellVolItem
{
    private readonly SwapInfoSellItemCurrency $currency;

    private readonly \DateTimeInterface $date;

    private readonly int $type;

    private readonly float $volumeForeignCurrency;

    private readonly float $volumeRub;

    public function __construct(array $item)
    {
        $this->currency = DataHelper::enumInt('Currency', $item, SwapInfoSellItemCurrency::class);
        $this->date = DataHelper::dateTime('DT', $item);
        $this->type = DataHelper::int('Type', $item, 0);
        $this->volumeForeignCurrency = DataHelper::float('VOL_FC', $item, .0);
        $this->volumeRub = DataHelper::float('VOL_RUB', $item, .0);
    }

    public function getCurrency(): SwapInfoSellItemCurrency
    {
        return $this->currency;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getVolumeForeignCurrency(): float
    {
        return $this->volumeForeignCurrency;
    }

    public function getVolumeRub(): float
    {
        return $this->volumeRub;
    }
}
