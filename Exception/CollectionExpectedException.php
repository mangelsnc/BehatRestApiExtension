<?php

namespace Ulff\BehatRestApiExtension\Exception;

class CollectionExpectedException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Expected response JSON with collection';
    }
}