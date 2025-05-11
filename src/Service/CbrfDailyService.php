<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Service;

use Liquetsoft\CbrfService\CbrfDaily;
use Liquetsoft\CbrfService\CbrfEntityCurrencyInternal;
use Liquetsoft\CbrfService\CbrfTransport;
use Liquetsoft\CbrfService\Entity\BiCurBacketItem;
use Liquetsoft\CbrfService\Entity\BiCurBaseRate;
use Liquetsoft\CbrfService\Entity\BliquidityRate;
use Liquetsoft\CbrfService\Entity\CurrencyEnum;
use Liquetsoft\CbrfService\Entity\CurrencyRate;
use Liquetsoft\CbrfService\Entity\DepoRate;
use Liquetsoft\CbrfService\Entity\Dv;
use Liquetsoft\CbrfService\Entity\InternationalReserve;
use Liquetsoft\CbrfService\Entity\InternationalReserveWeek;
use Liquetsoft\CbrfService\Entity\KeyRate;
use Liquetsoft\CbrfService\Entity\Mkr;
use Liquetsoft\CbrfService\Entity\OstatDepoRate;
use Liquetsoft\CbrfService\Entity\OstatRate;
use Liquetsoft\CbrfService\Entity\OvernightRate;
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\RepoDebt;
use Liquetsoft\CbrfService\Entity\RepoDebtUSDRate;
use Liquetsoft\CbrfService\Entity\ReutersCurrency;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapDayTotalRate;
use Liquetsoft\CbrfService\Entity\SwapInfoSellItem;
use Liquetsoft\CbrfService\Entity\SwapInfoSellVolItem;
use Liquetsoft\CbrfService\Entity\SwapMonthTotalRate;
use Liquetsoft\CbrfService\Entity\SwapRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * Class for a daily cb RF service.
 *
 * @internal
 */
final class CbrfDailyService implements CbrfDaily
{
    public function __construct(private readonly CbrfTransport $transport)
    {
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getCursOnDate(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'GetCursOnDate',
            [
                'On_date' => $date,
            ]
        );

        $immutableDate = DataHelper::createImmutableDateTime($date);
        $list = array_values(DataHelper::array('ValuteData.ValuteCursOnDate', $res));
        $callback = fn (array $item): CurrencyRate => new CurrencyRate($item, $immutableDate);

        return array_map($callback, $list);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getCursOnDateByCharCode(\DateTimeInterface $date, string $charCode): ?CurrencyRate
    {
        $list = $this->getCursOnDate($date);

        $return = null;
        foreach ($list as $item) {
            if (strcasecmp($charCode, $item->getCharCode()) === 0) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getCursOnDateByNumericCode(\DateTimeInterface $date, int $numericCode): ?CurrencyRate
    {
        $list = $this->getCursOnDate($date);

        $return = null;
        foreach ($list as $item) {
            if ($item->getNumericCode() === $numericCode) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function enumValutes(bool $seld = false): array
    {
        $res = $this->transport->query(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        return DataHelper::arrayOfItems('ValuteData.EnumValutes', $res, CurrencyEnum::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function enumValuteByCharCode(string $charCode, bool $seld = false): ?CurrencyEnum
    {
        $list = $this->enumValutes($seld);

        $return = null;
        foreach ($list as $item) {
            if (strcasecmp($charCode, $item->getCharCode()) === 0) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function enumValuteByNumericCode(int $numericCode, bool $seld = false): ?CurrencyEnum
    {
        $list = $this->enumValutes($seld);

        $return = null;
        foreach ($list as $item) {
            if ($item->getNumericCode() === $numericCode) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getLatestDateTime(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateTime');

        return DataHelper::dateTime('GetLatestDateTimeResult', $res);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getLatestDateTimeSeld(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateTimeSeld');

        return DataHelper::dateTime('GetLatestDateTimeSeldResult', $res);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getLatestDate(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDate');

        return DataHelper::dateTime('GetLatestDateResult', $res);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getLatestDateSeld(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateSeld');

        return DataHelper::dateTime('GetLatestDateSeldResult', $res);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getCursDynamic(\DateTimeInterface $from, \DateTimeInterface $to, CbrfEntityCurrencyInternal $currency): array
    {
        $res = $this->transport->query(
            'GetCursDynamic',
            [
                'FromDate' => $from,
                'ToDate' => $to,
                'ValutaCode' => $currency->getInternalCode(),
            ]
        );

        $result = [];
        $list = DataHelper::array('ValuteData.ValuteCursDynamic', $res);
        foreach ($list as $item) {
            if (\is_array($item)) {
                $date = DataHelper::dateTime('CursDate', $item);
                $item['Vname'] = $currency->getName();
                $item['VchCode'] = $currency->getCharCode();
                $item['Vcode'] = $currency->getNumericCode();
                $result[] = new CurrencyRate($item, $date);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function keyRate(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'KeyRate',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('KeyRate.KR', $res, KeyRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function dragMetDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DragMetDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DragMetall.DrgMet', $res, PreciousMetalRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function swapDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapDynamic.Swap', $res, SwapRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function depoDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DepoDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DepoDynamic.Depo', $res, DepoRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function ostatDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('OstatDynamic.Ostat', $res, OstatRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function ostatDepo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDepo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('OD.odr', $res, OstatDepoRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function mrrf(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mmrf.mr', $res, InternationalReserve::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function mrrf7d(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf7D',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mmrf7d.mr', $res, InternationalReserveWeek::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function saldo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Saldo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Saldo.So', $res, Saldo::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function ruoniaSV(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'RuoniaSV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('RuoniaSV.ra', $res, RuoniaIndex::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function ruonia(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Ruonia',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Ruonia.ro', $res, RuoniaBid::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function mkr(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'MKR',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mkr_base.MKR', $res, Mkr::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function dv(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DV_base.DV', $res, Dv::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function repoDebt(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Repo_debt',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Repo_debt.RD', $res, RepoDebt::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function enumReutersValutes(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'EnumReutersValutes',
            [
                'On_date' => $date,
            ]
        );

        return DataHelper::arrayOfItems('ReutersValutesList.EnumRValutes', $res, ReutersCurrency::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getReutersCursOnDate(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'GetReutersCursOnDate',
            [
                'On_date' => $date,
            ]
        );

        $results = [];
        $immutableDate = DataHelper::createImmutableDateTime($date);
        foreach (DataHelper::array('ReutersValutesData.Currency', $res) as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function overnight(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Overnight',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Overnight.OB', $res, OvernightRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function swapDayTotal(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapDayTotal',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapDayTotal.SDT', $res, SwapDayTotalRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function swapMonthTotal(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapMonthTotal',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapMonthTotal.SMT', $res, SwapMonthTotalRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function swapInfoSell(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapInfoSell',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapInfoSell.SSU', $res, SwapInfoSellItem::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function swapInfoSellVol(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapInfoSellVol',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapInfoSellVol.SSUV', $res, SwapInfoSellVolItem::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function bLiquidity(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Bliquidity',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Bliquidity.BL', $res, BliquidityRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function biCurBase(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'BiCurBase',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('BiCurBase.BCB', $res, BiCurBaseRate::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function biCurBacket(): array
    {
        $res = $this->transport->query('BiCurBacket');

        return DataHelper::arrayOfItems('BiCurBacket.BC', $res, BiCurBacketItem::class);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function repoDebtUSD(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'RepoDebtUSD',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('RepoDebtUSD.rd', $res, RepoDebtUSDRate::class);
    }
}
