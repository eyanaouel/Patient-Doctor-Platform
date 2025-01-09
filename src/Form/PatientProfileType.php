<?php
// src/Form/PatientProfileType.php

namespace App\Form;
use App\Entity\Medecin;
use App\Entity\RDV;
use App\Form\RDVType;
use App\Entity\Patient;
use App\Enum\GroupeSanguin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;  // Changé de EnumType à ChoiceType
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientProfileType extends AbstractType
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
            ->add('CIN', TextType::class, [
                'label' => 'CIN',
                'disabled' => true
            ])
            ->add('numeroCNAM', TextType::class, [
                'label' => 'Numéro CNAM',
                'required' => false
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('groupeSanguin', ChoiceType::class, [  // Modifié ici
                'label' => 'Groupe sanguin',
                'required' => false,
                'choices' => [
                    'A+' => GroupeSanguin::APositif,
                    'A-' => GroupeSanguin::ANegatif,
                    'B+' => GroupeSanguin::BPositif,
                    'B-' => GroupeSanguin::BNegatif,
                    'AB+' => GroupeSanguin::ABPositif,
                    'AB-' => GroupeSanguin::ABNegatif,
                    'O+' => GroupeSanguin::OPositif,
                    'O-' => GroupeSanguin::ONegatif
                ],
                'placeholder' => 'Choisir un groupe sanguin'
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe (laisser vide pour ne pas changer)',
                'required' => false,
                'mapped' => false
            ])
            ->add('numeroTel', TextType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'maxlength' => 15,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}