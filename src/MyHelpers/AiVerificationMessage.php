<?php

namespace App\MyHelpers;


use Doctrine\ORM\EntityManagerInterface;

class AiVerificationMessage
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