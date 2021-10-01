<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

/**
 * DTO that represents currency from currencies vocabulary.
 */
class CurrencyEnum implements Currency
{
    private string $code;

    private string $name;

    private string $engName;

    private int $nom;

    private string $commonCode;

    private int $numericCode;

    private string $charCode;

    public function __construct(array $item)
    {
        $this->code = trim($item['Vcode'] ?? '');
        $this->name = trim($item['Vname'] ?? '');
        $this->engName = trim($item['VEngname'] ?? '');
        $this->nom = (int) ($item['Vnom'] ?? 0);
        $this->commonCode = trim($item['VcommonCode'] ?? '');
        $this->numericCode = (int) ($item['VnumCode'] ?? 0);
        $this->charCode = strtoupper(trim($item['VcharCode'] ?? ''));
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEngName(): string
    {
        return $this->engName;
    }

    public function getNom(): int
    {
        return $this->nom;
    }

    public function getCommonCode(): string
    {
        return $this->commonCode;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }
}
