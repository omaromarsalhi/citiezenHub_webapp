<?php

namespace App\MyHelpers;

class SendPdfMessage
{
    private  $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function getObj()
    {
        return $this->obj;
    }
}