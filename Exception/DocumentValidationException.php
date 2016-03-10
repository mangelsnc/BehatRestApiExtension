<?php

namespace Ulff\BehatRestApiExtension\Exception;

class DocumentValidationException extends \Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}