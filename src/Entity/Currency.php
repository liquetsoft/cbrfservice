<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

/**
 * DTO that represents currency from currencies vocabulary.
 */
class Currency
{
    private string $code = '';

    private string $name = '';

    private string $engName = '';

    private int $nom = 0;

    private string $commonCode = '';

    private int $numCode = 0;

    private string $charCode = '';

    public function __construct(array $item)
    {
        $this->code = trim($item['Vcode'] ?? '');
        $this->name = trim($item['Vname'] ?? '');
        $this->engName = trim($item['VEngname'] ?? '');
        $this->nom = (int) ($item['Vnom'] ?? 0);
        $this->commonCode = trim($item['VcommonCode'] ?? '');
        $this->numCode = (int) ($item['VnumCode'] ?? 0);
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

    public function getNumCode(): int
    {
        return $this->numCode;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }
}
