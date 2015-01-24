<?php

namespace cbrfservice;

use \SoapClient;
use \SoapFault;
use \Exception;

/**
 * Class for a basic soap service utilits.
 */
class BaseServiceSoap extends BaseService
{
	/**
	 * @var string
	 */
	public $wsdl = null;
	/**
	 * @var array
	 */
	public $soapOptions = array();
	/**
	 * @var bool
	 */
	public $catchExceptions = true;
	/**
	 * @var \SoapClient
	 */
	protected $_client = null;


	/**
	 * @param string $method
	 * @param array $params
	 */
	public function __call($method, $params)
	{
		$client = $this->getSoapClient();
		if ($client) {
			$functions = $client->__getFunctions();
			if (in_array($method, $functions)) {
				return $this->doSoapCall($method, $params);
			}
		}
		if ($this->catchExceptions) {
			$this->addError('Method "' . $method . '" not found.');
			return null;
		} else {
			throw new Exception('Method "' . $method . '" not found.');
		}
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	protected function doSoapCall($method, array $params = array())
	{
		$return = null;
		$client = $this->getSoapClient();
		if ($client) {
			if ($this->catchExceptions) {
				try {
					$return = $client->__soapCall($method, $params);
				} catch (SoapFault $e) {
					$this->addError($e->getMessage());
				}
			} else {
				$return = $client->__soapCall($method, $params);
			}
		}
		return $return == null ? null : $this->parseSoapResult($return, $method, $params);
	}

	/**
	 * @param mixed $result
	 * @return mixed
	 */
	protected function parseSoapResult($result, $method, $params)
	{
		return $result;
	}

	/**
	 * @return \SoapClient
	 */
	protected function getSoapClient()
	{
		if (!empty($this->wsdl) && $this->_client === null) {
			if ($this->catchExceptions) {
				try {
					$this->_client = new SoapClient($this->wsdl, $this->soapOptions);
				} catch (Exception $e) {
					$this->addError($e->getMessage());
				}
			} else {
				$this->_client = new SoapClient($this->wsdl, $this->soapOptions);
			}			
		}
		return $this->_client;
	}

	/**
	 * @param string $date
	 * @return string
	 */
	protected function getXsdDateTimeFromDate($date)
	{
		$timestamp = is_numeric($date) ? $date : strtotime($date);
		$return = date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);
		return $return;
	}
}