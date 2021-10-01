<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class CurrencyRate implements Currency
{
    private string $charCode;

    private string $name;

    private int $numericCode;

    private float $rate;

    private int $nom;

    private DateTimeInterface $date;

    public function __construct(array $item, DateTimeInterface $date)
    {
        $this->charCode = strtoupper(trim($item['VchCode'] ?? ''));
        $this->name = trim($item['Vname'] ?? '');
        $this->numericCode = (int) ($item['Vcode'] ?? 0);
        $this->rate = (float) ($item['Vcurs'] ?? .0);
        $this->nom = (int) ($item['Vnom'] ?? 0);
        $this->date = $date;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getNom(): int
    {
        return $this->nom;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
