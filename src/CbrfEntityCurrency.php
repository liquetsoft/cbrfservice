<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

/**
 * Interface for DTO that contains currency.
 */
interface CbrfEntityCurrency
{
    public function getName(): string;

    public function getNom(): int;

    public function getNumericCode(): int;

    public function getCharCode(): string;
}
