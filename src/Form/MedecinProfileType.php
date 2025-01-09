<?php
// src/Form/MedecinProfileType.php

namespace App\Form;
use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MedecinProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('specialite', TextType::class, [
                'label' => 'Spécialité'
            ])
            ->add('numINPLM', TextType::class, [
                'label' => 'Numéro INPLM',
                'disabled' => true // Le numéro INPLM ne doit pas être modifiable
            ])
            ->add('numeroPro', TextType::class, [
                'required' => false,
                'label' => 'Numéro professionnel',
                'attr' => [
                    'maxlength' => 15,
                ],
            ])
            


            // ->add('disponibilite', TextType::class, [
            //     'label' => 'Disponibilité'
            // ])

            // ->add('heureDebut', TimeType::class, [
            //     'label' => 'Heure de début',
            //     'widget' => 'single_text',
            //     'required' => true
            // ])
            // ->add('heureFin', TimeType::class, [
            //     'label' => 'Heure de fin',
            //     'widget' => 'single_text',
            //     'required' => true
            // ])

            ->add('disponibilite', ChoiceType::class, [
                'label' => 'Horaire hebdomadaire',
                'choices' => [
                    'Lundi' => [
                        'Matin (8h-12h)' => 'lundi_matin',
                        'Après-midi (14h-18h)' => 'lundi_aprem'
                    ],
                    'Mardi' => [
                        'Matin (8h-12h)' => 'mardi_matin',
                        'Après-midi (14h-18h)' => 'mardi_aprem'
                    ],
                    'Mercredi' => [
                        'Matin (8h-12h)' => 'mercredi_matin',
                        'Après-midi (14h-18h)' => 'mercredi_aprem'
                    ],
                    'Jeudi' => [
                        'Matin (8h-12h)' => 'jeudi_matin',
                        'Après-midi (14h-18h)' => 'jeudi_aprem'
                    ],
                    'Vendredi' => [
                        'Matin (8h-12h)' => 'vendredi_matin',
                        'Après-midi (14h-18h)' => 'vendredi_aprem'
                    ]
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => ['class' => 'horaire-base']
            ])


            // ->add('disponibilite', CollectionType::class, [
            //     'entry_type' => DateTimeType::class,
            //     'entry_options' => [
            //         'widget' => 'single_text',
            //         'attr' => ['class' => 'form-control']
            //     ],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'label' => 'Disponibilités'
            // ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}