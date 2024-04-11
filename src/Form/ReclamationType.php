<?php

namespace App\Form;

use App\Entity\Reeclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('privateKey',TextType::class, [
                'label' => 'Private Key',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/[0-9]+/',
                        'message' => 'Private key must contain only numbers.'
                    ]),
                ]
            ])
        
            ->add('description',TextareaType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('subject',TextType::class, [
                'label' => 'Subject',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => 'Subject must have at least one character.',
                        'maxMessage' => 'Subject cannot have more than 255 characters.'
                    ]),
                ]
            ])
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
            ->setMethod('PUT')
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reeclamation::class,
        ]);
    }
}
