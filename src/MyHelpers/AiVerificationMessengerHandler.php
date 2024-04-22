<?php

namespace App\MyHelpers;

use App\Entity\AiResult;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class AiVerificationMessengerHandler
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(AiVerificationMessage $message): void
    {
        $obj = $message->getObj();

        $aiVerification= new AiVerification();
        $aiDataHolder=$aiVerification->run($obj);

        $aiResult = new AiResult();
        $aiResultServes=new AiResultServes();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $serializedData = $serializer->serialize($aiDataHolder, 'json');

        $aiResult->setBody($serializedData);
        $aiResult->setProduct($obj['product']);

        $aiResultServes->addAiResult($aiResult,$this->entityManager);

    }
}