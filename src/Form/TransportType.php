<?php

namespace App\Form;

use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Type_Vehicule')
            ->add('Reference')
            ->add('Vehicule_Image')
            ->add('Prix')
            ->add('Heure')
            ->add('Station_depart')
            ->add('Station_arrive')

            ->add('ajouter',SubmitType::class); // No need to specify SubmitType::class
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transport::class,
        ]);
    }
}
