<?php

declare(strict_types=1);

namespace Marvin255\CbrfService;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin255\CbrfService\Entity\CurrencyEnum;
use Marvin255\CbrfService\Entity\CurrencyRate;
use Marvin255\CbrfService\Entity\ReutersCurrencyRate;
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

        $immutableDate = DateTimeImmutable::createFromInterface($date);
        $list = DataAccessor::array('ValuteData.ValuteCursOnDate', $soapResult);
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

        $list = DataAccessor::array('ValuteData.EnumValutes', $soapResult);
        $callback = fn (array $item): CurrencyEnum => new CurrencyEnum($item);

        return array_map($callback, $list);
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

        return DataAccessor::dateTime('GetLatestDateTimeResult', $soapResult);
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

        return DataAccessor::dateTime('GetLatestDateTimeSeldResult', $soapResult);
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

        return DataAccessor::dateTime('GetLatestDateResult', $soapResult);
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

        return DataAccessor::dateTime('GetLatestDateSeldResult', $soapResult);
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
        $immutableDate = DateTimeImmutable::createFromInterface($date);

        foreach ($enumCurrencies as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }
}
