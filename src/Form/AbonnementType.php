<?php
//
//namespace App\Form;
//
//use App\Entity\Abonnement;
//use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolver;
//
//class AbonnementType extends AbstractType
//{
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//        $builder
//            ->add('nom')
//            ->add('prenom')
//          ->add('dateDebut')
//          ->add('dateFin')
//            ->add('TypeAbonnement')
//            ->add('image')
//
//            ->add('ajouter',SubmitType::class);
//
//        ;
//    }
//
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => Abonnement::class,
//        ]);
//    }
//}


namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('TypeAbonnement', ChoiceType::class, [
                'choices' => [
                    'Annuel' => 'annuel',
                    'Mensuelle' => 'mensuelle',
                ],
            ])
//            ->add('image', FileType::class, [
//                'label' => 'Image path (optional)', // Add a label for clarity
//                'mapped' => false, // Don't map it to an entity property (optional)
//            ])
//            ->add('ajouter',  SubmitType::class);
//        ->add('ajouter', ButtonType::class, [
//        'label' => 'Valider',
//        'attr' => ['class' => 'btn btn-primary'],
//    ]);

        ->add('valider', SubmitType::class, [ // Utilisez SubmitType pour soumettre le formulaire
        'label' => 'Valider',
        'attr' => ['class' => 'btn btn-primary'], // Ajoutez des classes pour le style
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
