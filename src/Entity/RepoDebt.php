<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from Repo_debt method.
 */
class RepoDebt implements Rate
{
    private float $rate;

    private float $debtAuc;

    private float $debtFix;

    private \DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->rate = DataHelper::float('debt', $item, .0);
        $this->debtAuc = DataHelper::float('debt_auc', $item, .0);
        $this->debtFix = DataHelper::float('debt_fix', $item, .0);
        $this->date = DataHelper::dateTime('Date', $item);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDebtAuc(): float
    {
        return $this->debtAuc;
    }

    public function getDebtFix(): float
    {
        return $this->debtFix;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
