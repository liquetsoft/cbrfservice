<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

/**
 * Enum with precious metal codes.
 */
enum SwapInfoSellItemCurrency: int
{
    case USD = 0;
    case EUR = 1;
    case CNY = 2;
}
