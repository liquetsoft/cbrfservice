<?php

namespace marvin255\cbrfservice;

/**
 * Class for a basic service utilits.
 */
class BaseService
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @param string $error
     */
    protected function addError($error)
    {
        $this->errors[] = trim($error);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return !empty($this->errors) ? $this->errors : array();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function clearErrors()
    {
        $this->errors = array();
    }

    /**
     * @param array $oprions
     */
    public function __construct(array $options = null)
    {
        if ($options) {
            $this->config($options);
        }
    }

    /**
     * @param array $config
     */
    public function config(array $options)
    {
        $properties = array();
        //we must set only public properties
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $properties[$prop->getName()] = true;
        }
        foreach ($options as $name => $value) {
            if (isset($properties[$name])) {
                $this->$name = $value;
            }
        }
    }
}
