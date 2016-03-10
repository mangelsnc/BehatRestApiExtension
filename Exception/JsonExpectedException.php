<?php

namespace Ulff\BehatRestApiExtension\Exception;

class JsonExpectedException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Expected response with type JSON';
    }
}