<?php

namespace App\Form;

use App\Entity\Reeclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('privateKey')
        
            ->add('description')
            ->add('subject')
            ->add('imageFile',VichImageType::class,[
                'label' => 'Photo',
                'label_attr' => [
                    'class' => 'brows-file-wrapper'
                ],
            ])
            ->add('submit',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary text-white w-100 py-3'
                ],
                'label' => "Envoyer"
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reeclamation::class,
        ]);
    }
}
