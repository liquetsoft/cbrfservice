<?php

namespace marvin255\cbrfservice;

use SoapClient;
use InvalidArgumentException;

/**
 * Class for a basic soap service utilits.
 */
abstract class SoapService
{
    /**
     * @var \SoapClient
     */
    private $client = null;

    /**
     * @param mixed $result
     *
     * @return mixed
     */
    abstract protected function parseSoapResult($result, $method, $params);

    /**
     * Constructor.
     *
     * @param \SoapClient $client soapClient instance to connect to service or string instance with wsdl url
     *
     * @throws \InvalidArgumentException
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
     * @throws \marvin255\cbrfservice\Exception
     */
    protected function doSoapCall($method, array $params = [])
    {
        try {
            $res = $this->getSoapClient()->__soapCall($method, $params);
        } catch (\Exception $e) {
            throw new Exception(
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
     * @return \SoapClient
     */
    public function getSoapClient()
    {
        return $this->client;
    }

    /**
     * Converts any date in any format to xsd DateTime format.
     *
     * @param string $date
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getXsdDateTimeFromDate($date)
    {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        if ($timestamp === false) {
            throw new InvalidArgumentException(
                "Can't parse date: $date"
            );
        }
        $return = date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);

        return $return;
    }
}
