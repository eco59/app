<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),  // Assure que l'email n'est pas vide
                    new Assert\Email(),     // Valide le format de l'email
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['class' => 'form-control', 'rows' => 6],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(), // Assure que le message n'est pas vide
                    new Assert\Length([
                        'min' => 10, // Par exemple, un message d'au moins 10 caractères
                        'max' => 2000, // Limite maximale de 2000 caractères
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => true,
        ]);
    }
}
