<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('cin')
            ->add('email')
            ->add('age')
            ->add('phoneNumber')
            ->add('address')
            ->add('role')
            ->add('password')
            ->add('date')
            ->add('dob')
            ->add('status')
            ->add('image', FileType::class,[
                'mapped' => false,
                'multiple' => true,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
