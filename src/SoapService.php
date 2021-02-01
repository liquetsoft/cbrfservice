<?php

namespace Marvin255\CbrfService;

use InvalidArgumentException;
use SoapClient;

/**
 * Class for a basic soap service utilits.
 */
abstract class SoapService
{
    /**
     * @var SoapClient|null
     */
    private $client = null;

    /**
     * @param mixed  $result
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    abstract protected function parseSoapResult($result, $method, $params);

    /**
     * Constructor.
     *
     * @param SoapClient $client soapClient instance to connect to service or string instance with wsdl url
     */
    public function __construct(SoapClient $client = null)
    {
        $this->client = $client;
    }

    /**
     * Do a soap call.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     *
     * @throws CbrfException
     */
    protected function doSoapCall($method, array $params = [])
    {
        try {
            $res = $this->getSoapClient()->__soapCall($method, $params);
        } catch (\Exception $e) {
            throw new CbrfException(
                "Fail while request {$method}: " . $e->getMessage(),
                0,
                $e
            );
        }

        return $res === null ?: $this->parseSoapResult($res, $method, $params);
    }

    /**
     * Returns a SoapClient instance for soap requests.
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if ($this->client === null) {
            throw new InvalidArgumentException('Client not found.');
        }

        return $this->client;
    }

    /**
     * Converts any date in any format to xsd DateTime format.
     *
     * @param string|int $date
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getXsdDateTimeFromDate($date)
    {
        $timestamp = is_numeric($date) ? (int) $date : strtotime($date);
        if ($timestamp === false) {
            throw new InvalidArgumentException(
                "Can't parse date: $date"
            );
        }
        $return = date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);

        return $return;
    }
}
