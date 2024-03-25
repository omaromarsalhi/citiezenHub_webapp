<?php
namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;
use Vich\UploaderBundle\Storage\StorageInterface;




class MyTools
{
    private $storage;
    private $namer;


    public function __construct(SmartUniqueNamer $namer)
    {
        $this->namer = $namer;
    }


    public function uploadImage(UploadedFile $imageFile): string
    {    $dossier='/src' ;
        $fileName = $this->namer->name();
        $this->storage->upload($imageFile,$fileName);
        return $fileName;
    }

}
