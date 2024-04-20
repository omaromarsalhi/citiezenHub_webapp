<?php

namespace App\MyHelpers;


class AiVerificationMessage
{
    private  $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function getImages()
    {
        return $this->obj;
    }
}