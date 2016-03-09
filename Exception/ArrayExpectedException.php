<?php

namespace Ulff\BehatRestApiExtension\Exception;

class ArrayExpectedException extends \Exception
{
    public function __construct($propertyName, $propertyExpectedValue, $propertyActualValue)
    {
        $this->message = "JSON '$propertyName' property should have array value: '$propertyExpectedValue', actual: '".var_export($propertyActualValue, true)."'";
    }
}