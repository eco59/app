<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AjoutEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', TextType::class, [
                'label' => 'Titre de l\'event',
                'required' => true,
                'constraints' => [
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Contenu de l\'event',
                'required' => true,
                'constraints' => [
                    new Assert\Length([
                        'min' => 10, // Par exemple, un message d'au moins 10 caractères
                        'max' => 4000, // Limite maximale de 4000 caractères
                    ]),
                ],
            ])
            ->add('Nombre_de_joueur', IntegerType::class, [
                'label' => 'Nombre de joueur',
                'required' => true,
                'constraints' => [
                    new Positive(), // Assure que la valeur est strictement positive
                ],
            ])

            ->add('date_heure_debut', null, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('date_heure_fin', null, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('Image', FileType::class, [
                'label' => 'Image de l\'event',
                'mapped' => false,
                'attr' => [
                    'accept'=> 'image/png, image/jpeg, image/webp'
                ],
                'constraints' => [
                    new Image(
                        minWidth: 200,
                        maxWidth:4000,
                        minHeight:200,
                        maxHeight:4000,
                        allowPortrait:false,
                        maxSize: '2M',
                        mimeTypes:[
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ]
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => true,
        ]);
    }
}
