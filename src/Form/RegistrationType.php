<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;





class RegistrationType extends AbstractType
{


public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Champs communs pour tous les utilisateurs
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un email']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                ],
            ]);

        // Champs spécifiques aux médecins
        if ($options['is_medecin']) {
            $builder
                ->add('specialite', TextType::class, [
                    'label' => 'Specialite',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer votre spécialité']),
                    ],
                ])
                ->add('numINPLM', TextType::class, [
                    'label' => 'Numéro INPLM',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer votre numéro INPLM']),
                    ],
                ]);
        }


        if (!$options['is_medecin']) {
            $builder
                ->add('CIN', TextType::class, [
                    'label' => 'CIN',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer votre CIN']),
                        new Length([
                            'min' => 8,
                            'max' => 8,
                            'exactMessage' => 'Le CIN doit contenir exactement 8 caractères.',
                        ]),
                    ],
                ])
                ->add('dateNaissance', DateType::class, [
                    'label' => 'Date de naissance',
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer votre date de naissance']),
                        new Date(['message' => 'Veuillez entrer une date valide.']),
                    ],
                ]);
        }
        
    }



    public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => User::class, // On met null pour permettre l'héritage
        'is_medecin' => false,
    ]);
}

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'data_class' => User::class, // Remplacez par votre classe d'entité, par exemple App\Entity\User
    //         //'validation_groups' => ['Default', 'registration'],
    //         'is_medecin' => false, // Définit si le formulaire est destiné aux médecins
    //     ]);
    // }
}