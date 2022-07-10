<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use DateTimeInterface;
use Liquetsoft\CbrfService\Entity\CurrencyEnum;
use Liquetsoft\CbrfService\Entity\CurrencyRate;
use Liquetsoft\CbrfService\Entity\DepoRate;
use Liquetsoft\CbrfService\Entity\InternationalReserve;
use Liquetsoft\CbrfService\Entity\KeyRate;
use Liquetsoft\CbrfService\Entity\OstatDepoRate;
use Liquetsoft\CbrfService\Entity\OstatRate;
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\SwapRate;
use SoapClient;

/**
 * Class for a daily cb RF service.
 */
class CbrfDaily
{
    private CbrfSoapService $soapClient;

    /**
     * @param SoapClient|string $client
     */
    public function __construct($client = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL')
    {
        $this->soapClient = new CbrfSoapService($client);
    }

    /**
     * Returns list of rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return CurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getCursOnDate(DateTimeInterface $date): array
    {
        $soapResult = $this->soapClient->query(
            'GetCursOnDate',
            [
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $immutableDate = DataHelper::createImmutableDateTime($date);
        $list = DataHelper::array('ValuteData.ValuteCursOnDate', $soapResult);
        $callback = fn (array $item): CurrencyRate => new CurrencyRate($item, $immutableDate);

        return array_map($callback, $list);
    }

    /**
     * Returns rate for currency with set char code.
     *
     * @param DateTimeInterface $onDate
     * @param string            $charCode
     *
     * @return CurrencyRate|null
     *
     * @throws CbrfException
     */
    public function getCursOnDateByCharCode(DateTimeInterface $date, string $charCode): ?CurrencyRate
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
     * Returns rate for currency with set numeric code.
     *
     * @param DateTimeInterface $onDate
     * @param int               $numericCode
     *
     * @return CurrencyRate|null
     *
     * @throws CbrfException
     */
    public function getCursOnDateByNumericCode(DateTimeInterface $date, int $numericCode): ?CurrencyRate
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
     * List of all currencies that allowed on cbrf service.
     *
     * @param bool $seld
     *
     * @return CurrencyEnum[]
     *
     * @throws CbrfException
     */
    public function enumValutes(bool $seld = false): array
    {
        $soapResult = $this->soapClient->query(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        $list = DataHelper::array('ValuteData.EnumValutes', $soapResult);
        $callback = fn (array $item): CurrencyEnum => new CurrencyEnum($item);

        return array_map($callback, $list);
    }

    /**
     * Returns enum for currency with set char code.
     *
     * @param string $charCode
     * @param bool   $seld
     *
     * @return CurrencyEnum|null
     *
     * @throws CbrfException
     */
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
     * Returns enum for currency with set numeric code.
     *
     * @param int  $numericCode
     * @param bool $seld
     *
     * @return CurrencyEnum|null
     *
     * @throws CbrfException
     */
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
     * Latest per day date and time of publication.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateTime(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateTime');

        return DataHelper::dateTime('GetLatestDateTimeResult', $soapResult);
    }

    /**
     * Latest per day date and time of seld.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateTimeSeld(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateTimeSeld');

        return DataHelper::dateTime('GetLatestDateTimeSeldResult', $soapResult);
    }

    /**
     * Latest per month date and time of publication.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDate(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDate');

        return DataHelper::dateTime('GetLatestDateResult', $soapResult);
    }

    /**
     * Latest per month date and time of seld.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateSeld(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateSeld');

        return DataHelper::dateTime('GetLatestDateSeldResult', $soapResult);
    }

    /**
     * Returns rate dynamic for set currency within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param CurrencyEnum      $currency
     *
     * @return CurrencyRate[]
     */
    public function getCursDynamic(DateTimeInterface $from, DateTimeInterface $to, CurrencyEnum $currency): array
    {
        $soapResult = $this->soapClient->query(
            'GetCursDynamic',
            [
                'FromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ValutaCode' => $currency->getInternalCode(),
            ]
        );

        $result = [];
        $list = DataHelper::array('ValuteData.ValuteCursDynamic', $soapResult);
        foreach ($list as $item) {
            $date = DataHelper::dateTime('CursDate', $item);
            $item['Vname'] = $currency->getName();
            $item['VchCode'] = $currency->getCharCode();
            $item['Vcode'] = $currency->getNumericCode();
            $result[] = new CurrencyRate($item, $date);
        }

        return $result;
    }

    /**
     * Returns key rate dynamic within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return KeyRate[]
     */
    public function keyRate(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'KeyRate',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('KeyRate.KR', $soapResult);
        $callback = fn (array $item): KeyRate => new KeyRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns list of presious metals prices within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return PreciousMetalRate[]
     */
    public function dragMetDynamic(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'DragMetDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('DragMetall.DrgMet', $soapResult);
        $callback = fn (array $item): PreciousMetalRate => new PreciousMetalRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns list of swap rates within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return SwapRate[]
     */
    public function swapDynamic(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'SwapDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('SwapDynamic.Swap', $soapResult);
        $callback = fn (array $item): SwapRate => new SwapRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns list depo dynamic items within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return DepoRate[]
     */
    public function depoDynamic(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'DepoDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('DepoDynamic.Depo', $soapResult);
        $callback = fn (array $item): DepoRate => new DepoRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns the dynamic of balances of funds items within set dates.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return OstatRate[]
     */
    public function ostatDynamic(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'OstatDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('OstatDynamic.Ostat', $soapResult);
        $callback = fn (array $item): OstatRate => new OstatRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns the banks deposites at bank of Russia.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return OstatDepoRate[]
     */
    public function ostatDepo(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'OstatDepo',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('OD.odr', $soapResult);
        $callback = fn (array $item): OstatDepoRate => new OstatDepoRate($item);

        return array_map($callback, $list);
    }

    /**
     * Returns international valute reseves of Russia.
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return InternationalReserve[]
     */
    public function mrrf(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $soapResult = $this->soapClient->query(
            'mrrf',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $list = DataHelper::array('mmrf.mr', $soapResult);
        $callback = fn (array $item): InternationalReserve => new InternationalReserve($item);

        return array_map($callback, $list);
    }

    /**
     * Returns list of Reuters rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return ReutersCurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getReutersCursOnDate(DateTimeInterface $date): array
    {
        $enumSoapResults = $this->soapClient->query(
            'EnumReutersValutes',
            [
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $enumCurrencies = [];
        foreach ($enumSoapResults['ReutersValutesList']['EnumRValutes'] as $enumSoapResult) {
            $enumCurrencies[$enumSoapResult['num_code']] = $enumSoapResult;
        }

        $soapValutesResults = $this->soapClient->query(
            'GetReutersCursOnDate',
            [
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        foreach ($soapValutesResults['ReutersValutesData']['Currency'] as $soapValutesResult) {
            $enumCurrencies[$soapValutesResult['num_code']] = array_merge($enumCurrencies[$soapValutesResult['num_code']], $soapValutesResult);
        }

        $results = [];
        $immutableDate = DataHelper::createImmutableDateTime($date);

        foreach ($enumCurrencies as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }
}
