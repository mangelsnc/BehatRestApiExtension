<?php

namespace Ulff\BehatRestApiExtension\Exception;

class SingleObjectExpectedException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Expected response JSON with single object';
    }
}