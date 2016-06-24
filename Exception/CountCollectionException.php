<?php

namespace Ulff\BehatRestApiExtension\Exception;

class CountCollectionException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Collection has the wrong number of items.';
    }
}
