<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class CursOnDate
{
    private string $vchCode = '';

    private string $vname = '';

    private int $vcode = 0;

    private float $vcurs = .0;

    private int $vnom = 0;

    private DateTimeInterface $date;

    public function __construct(array $item, DateTimeInterface $date)
    {
        $this->vchCode = strtoupper(trim($item['VchCode'] ?? ''));
        $this->vname = trim($item['Vname'] ?? '');
        $this->vcode = (int) ($item['Vcode'] ?? 0);
        $this->vcurs = (float) ($item['Vcurs'] ?? .0);
        $this->vnom = (int) ($item['Vnom'] ?? 0);
        $this->date = $date;
    }

    public function getVchCode(): string
    {
        return $this->vchCode;
    }

    public function getVname(): string
    {
        return $this->vname;
    }

    public function getVcode(): int
    {
        return $this->vcode;
    }

    public function getVcurs(): float
    {
        return $this->vcurs;
    }

    public function getVnom(): int
    {
        return $this->vnom;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
