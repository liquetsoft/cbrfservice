<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

/**
 * Interface for DTO that contains currency.
 */
interface Currency
{
    public function getName(): string;

    public function getNom(): int;

    public function getNumericCode(): int;

    public function getCharCode(): string;
}
