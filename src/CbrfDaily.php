<?php

declare(strict_types=1);

namespace Marvin255\CbrfService;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin255\CbrfService\Entity\Currency;
use Marvin255\CbrfService\Entity\CurrencyRate;
use SoapClient;
use Throwable;

/**
 * Class for a daily cb RF service.
 */
class CbrfDaily
{
    private ?string $wsdl = null;

    private ?SoapClient $client = null;

    /**
     * @param SoapClient|string $client
     */
    public function __construct($client = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL')
    {
        if ($client instanceof SoapClient) {
            $this->client = $client;
        } else {
            $this->wsdl = $client;
        }
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
        $soapResult = $this->doSoapCall(
            'GetCursOnDate',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        $results = [];
        $immutableDate = new DateTimeImmutable($date->format(\DATE_ATOM));

        $currencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($currencyRateList as $item) {
            if (\is_array($item)) {
                $results[] = new CurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }

    /**
     * Returns list of rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return CurrencyRate|null
     *
     * @throws CbrfException
     */
    public function getCursOnDateByCode(DateTimeInterface $date, string $code): ?CurrencyRate
    {
        $currencyItem = null;
        $code = strtoupper(trim($code));

        $soapResult = $this->doSoapCall(
            'GetCursOnDate',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        // looks like repeating of getCurrencyRate
        // but we do not want to instantiate objects for all currencies
        $currencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($currencyRateList as $item) {
            $itemCode = strtoupper(trim($item['VchCode'] ?? ''));
            if ($code === $itemCode) {
                $immutableDate = new DateTimeImmutable($date->format(\DATE_ATOM));
                $currencyItem = new CurrencyRate($item, $immutableDate);
                break;
            }
        }

        return $currencyItem;
    }

    /**
     * List of all currencies that allowed on cbrf service.
     *
     * @param bool $seld
     *
     * @return Currency[]
     *
     * @throws CbrfException
     */
    public function enumValutes(bool $seld = false): array
    {
        $soapResult = $this->doSoapCall(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        $results = [];

        $enumValutes = $soapResult['ValuteData']['EnumValutes'] ?? [];
        foreach ($enumValutes as $item) {
            if (\is_array($item)) {
                $results[] = new Currency($item);
            }
        }

        return $results;
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
        $soapResult = $this->doSoapCall('GetLatestDateTime');

        try {
            $dateTime = new DateTimeImmutable($soapResult['GetLatestDateTimeResult'] ?? '');
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $dateTime;
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
        $soapResult = $this->doSoapCall('GetLatestDateTimeSeld');

        try {
            $dateTime = new DateTimeImmutable($soapResult['GetLatestDateTimeSeldResult'] ?? '');
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $dateTime;
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
        $soapResult = $this->doSoapCall('GetLatestDate');

        try {
            $dateTime = new DateTimeImmutable($soapResult['GetLatestDateResult'] ?? '');
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $dateTime;
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
        $soapResult = $this->doSoapCall('GetLatestDateSeld');

        try {
            $dateTime = new DateTimeImmutable($soapResult['GetLatestDateSeldResult'] ?? '');
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $dateTime;
    }

    /**
     * Makes a soap call.
     *
     * @param string $method
     * @param array  $params
     *
     * @return array
     *
     * @throws CbrfException
     */
    private function doSoapCall(string $method, array $params = []): array
    {
        $parsedResult = [];

        try {
            // need to do this because every params list are nested to parameters object
            if (!empty($params)) {
                $params = [$params];
            }
            $soapCallResult = $this->getSoapClient()->__soapCall($method, $params);

            $resName = $method . 'Result';
            if (!empty($soapCallResult->$resName->any)) {
                $xml = simplexml_load_string(
                    $soapCallResult->$resName->any,
                    'SimpleXMLElement',
                    \LIBXML_NOCDATA
                );
                $parsedResult = $this->xml2array($xml);
            } else {
                $parsedResult = (array) $soapCallResult;
            }
        } catch (Throwable $e) {
            $message = sprintf("Fail on '%s': '%s'.", $method, $e->getMessage());
            throw new CbrfException($message, 0, $e);
        }

        return $parsedResult;
    }

    /**
     * Converts SimpleXMLElement to an associative array,.
     *
     * @param mixed $xmlObject
     *
     * @return array<string, mixed>
     */
    private function xml2array($xmlObject): array
    {
        $out = [];

        $xmlArray = (array) $xmlObject;
        foreach ($xmlArray as $index => $node) {
            if (\is_object($node) || \is_array($node)) {
                $out[$index] = $this->xml2array($node);
            } else {
                $out[$index] = $node;
            }
        }

        return $out;
    }

    /**
     * Returns a SoapClient instance for soap requests.
     *
     * @return SoapClient
     *
     * @throws CbrfException
     */
    private function getSoapClient()
    {
        if ($this->client !== null) {
            return $this->client;
        }

        if ($this->wsdl === null) {
            $message = sprintf("Provided client must be string with valid url to WSDL file or a '%s' instance.", SoapClient::class);
            throw new CbrfException($message);
        }

        try {
            $this->client = new SoapClient(
                $this->wsdl,
                [
                    'exception' => true,
                ]
            );
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $this->client;
    }
}
