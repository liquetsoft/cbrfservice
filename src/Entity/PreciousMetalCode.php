<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

/**
 * Enum with precious metal codes.
 */
enum PreciousMetalCode: int
{
    case GOLD = 1;
    case SILVER = 2;
    case PLATINUM = 3;
    case PALLADIUM = 4;
}
