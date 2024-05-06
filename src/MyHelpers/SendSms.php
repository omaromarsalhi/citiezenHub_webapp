<?php

namespace App\MyHelpers;


use Twilio\Rest\Client;

class SendSms
{
    public static function send(): void
    {

        $twilio = new Client($accountSid, $authToken);

        $message = $twilio->messages->create(
            '+21629624921', // To phone number
            [
                'from' => '+16097704463', // From phone number (your Twilio number)
                'body' => 'the product has been verified!',
            ]
        );
    }

}