<?php

namespace Ulff\BehatRestApiExtension\Exception;

class IncorrectPropertyValueException extends \Exception
{
    public function __construct($propertyName, $propertyExpectedValue, $propertyActualValue)
    {
        $this->message = "JSON '$propertyName' property should have value: '$propertyExpectedValue', actual: '$propertyActualValue'";
    }
}