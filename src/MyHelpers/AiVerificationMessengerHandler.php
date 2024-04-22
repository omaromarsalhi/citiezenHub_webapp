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

    private $entityManager=null;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AiVerificationMessage $message): void
    {
        $obj = $message->getObj();
        $aiVerification= new AiVerification();
        $aiDataHolder=$aiVerification->run($obj);
        var_dump($aiDataHolder);


        $aiResult = new AiResult();
//        $aiResultServes=new AiResultServes();
//
//        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
//        $serializedData = $serializer->serialize($aiDataHolder, 'json');

        $aiResult->setBody('eeee');
        $aiResult->setIdProduct($obj['product']);



        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->getDoctrine()->resetManager();
        }

        $this->entityManager->persist($aiResult);
        $this->entityManager->flush();
//        $aiResultServes->addAiResult($aiResult,$this->entityManager);

////        $aiResultController=new AiResultController();
//        $aiResult = new AiResult();
//
//        $this->aiDataHolder=$aiVerification->run($obj);
//
//        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
//        $serializedData = $serializer->serialize($this->aiDataHolder, 'json');
//
//        $aiResult->setBody($serializedData);
//        $aiResult->setIdProduct($obj['product']);
//
////        $aiResultController->new($aiResult,$this->entityManager);
//        $this->entityManager->persist($aiResult);
//        $this->entityManager->flush();

    }
}