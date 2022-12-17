<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

/**
 * Interface for DTO that contains currency with internal cbrf value.
 */
interface CbrfEntityCurrencyInternal extends CbrfEntityCurrency
{
    public function getInternalCode(): string;
}
