<?php

namespace Ulff\BehatRestApiExtension\Exception;


class DuplicatedDocumentException extends \Exception
{
    public function __construct($documentName)
    {
        $this->message = "Document '$documentName' with given data exists yet";
    }
}
