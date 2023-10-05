<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from Bliquidity method.
 *
 * @psalm-immutable
 */
final class BliquidityRate implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    private readonly float $claims;

    private readonly float $actionBasedRepoFX;

    private readonly float $actionBasedSecureLoans;

    private readonly float $standingFacilitiesRepoFX;

    private readonly float $standingFacilitiesSecureLoans;

    private readonly float $liabilities;

    private readonly float $depositAuctionBased;

    private readonly float $depositStandingFacilities;

    private readonly float $CBRbonds;

    private readonly float $netCBRclaims;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DT', $item);
        $this->rate = DataHelper::float('StrLiDef', $item, .0);
        $this->claims = DataHelper::float('claims', $item, .0);
        $this->actionBasedRepoFX = DataHelper::float('actionBasedRepoFX', $item, .0);
        $this->actionBasedSecureLoans = DataHelper::float('actionBasedSecureLoans', $item, .0);
        $this->standingFacilitiesRepoFX = DataHelper::float('standingFacilitiesRepoFX', $item, .0);
        $this->standingFacilitiesSecureLoans = DataHelper::float('standingFacilitiesSecureLoans', $item, .0);
        $this->liabilities = DataHelper::float('liabilities', $item, .0);
        $this->depositAuctionBased = DataHelper::float('depositAuctionBased', $item, .0);
        $this->depositStandingFacilities = DataHelper::float('depositStandingFacilities', $item, .0);
        $this->CBRbonds = DataHelper::float('CBRbonds', $item, .0);
        $this->netCBRclaims = DataHelper::float('netCBRclaims', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getClaims(): float
    {
        return $this->claims;
    }

    public function getActionBasedRepoFX(): float
    {
        return $this->actionBasedRepoFX;
    }

    public function getActionBasedSecureLoans(): float
    {
        return $this->actionBasedSecureLoans;
    }

    public function getStandingFacilitiesRepoFX(): float
    {
        return $this->standingFacilitiesRepoFX;
    }

    public function getStandingFacilitiesSecureLoans(): float
    {
        return $this->standingFacilitiesSecureLoans;
    }

    public function getLiabilities(): float
    {
        return $this->liabilities;
    }

    public function getDepositAuctionBased(): float
    {
        return $this->depositAuctionBased;
    }

    public function getDepositStandingFacilities(): float
    {
        return $this->depositStandingFacilities;
    }

    public function getCBRbonds(): float
    {
        return $this->CBRbonds;
    }

    public function getNetCBRclaims(): float
    {
        return $this->netCBRclaims;
    }
}
