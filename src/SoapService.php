<?php

namespace marvin255\cbrfservice;

use SoapClient;
use InvalidArgumentException;

/**
 * Class for a basic soap service utilits.
 */
class SoapService
{
    /**
     * @var \SoapClient
     */
    private $client = null;

    /**
     * Constructor.
     *
     * @param string|\SoapClient $client soapClient instance to connect to service or string instance with wsdl url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($client = null)
    {
        if (is_string($client)) {
            try {
                $this->client = new SoapClient($client);
            } catch (\Exception $e) {
                throw new InvalidArgumentException(
                    "Can't create SoapClient by wsdl ({$client}): " . $e->getMessage()
                );
            }
        } elseif ($client instanceof SoapClient) {
            $this->client = $client;
        } else {
            throw new InvalidArgumentException(
                "Constructor's parameter must be a string or SoapClient instance"
            );
        }
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
     * @param mixed $result
     *
     * @return mixed
     */
    protected function parseSoapResult($result, $method, $params)
    {
        return $result;
    }

    /**
     * Returns a SoapClient instance for soap requests.
     *
     * @return \SoapClient
     */
    protected function getSoapClient()
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
