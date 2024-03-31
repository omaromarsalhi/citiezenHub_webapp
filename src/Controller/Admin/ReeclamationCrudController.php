<?php

namespace App\Controller\Admin;

use App\Entity\Reeclamation;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReeclamationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reeclamation::class;
    }
    public function configureFields(string $pageName): iterable
    {
        /*$formattedCreatedAt = function ($reclamation) {
            // Your formatting logic here (e.g., substr to extract specific part)
            return substr($reclamation->getCreatedAt(), 0, 10); // Example: extract first 10 characters (assuming YYYY-MM-DD format)
        };*/

        return [
            IdField::new('privateKey'),
            TextField::new('subject'),
            DateTimeField::new('createdAt'),
            TextField::new('description'),
        ];
        
    }
    
}
