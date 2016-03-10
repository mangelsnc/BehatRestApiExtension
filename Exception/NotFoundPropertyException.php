<?php

namespace Ulff\BehatRestApiExtension\Exception;

class NotFoundPropertyException extends \Exception
{
    public function __construct($propertyName)
    {
        $this->message = "JSON should have property: $propertyName";
    }
}