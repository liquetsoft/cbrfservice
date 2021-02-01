<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class CurrencyRate
{
    private string $chCode = '';

    private string $name = '';

    private int $code = 0;

    private float $curs = .0;

    private int $nom = 0;

    private DateTimeInterface $date;

    public function __construct(array $item, DateTimeInterface $date)
    {
        $this->chCode = strtoupper(trim($item['VchCode'] ?? ''));
        $this->name = trim($item['Vname'] ?? '');
        $this->code = (int) ($item['Vcode'] ?? 0);
        $this->curs = (float) ($item['Vcurs'] ?? .0);
        $this->nom = (int) ($item['Vnom'] ?? 0);
        $this->date = $date;
    }

    public function getChCode(): string
    {
        return $this->chCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getCurs(): float
    {
        return $this->curs;
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
