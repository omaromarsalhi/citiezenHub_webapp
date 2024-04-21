<?php

namespace App\MyHelpers;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AiVerificationMessengerHandler
{
    public function __invoke(AiVerificationMessage $message): void
    {
        $images = $message->getImages();
        $aiVerification= new AiVerification();
        $res=$aiVerification->run($images);
//        var_dump($res);
    }
}