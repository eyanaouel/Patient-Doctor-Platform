<?php

namespace App\Form;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;

use App\Entity\Medecin;
use App\Entity\RDV;
use App\Form\RDVType;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('numeroCNAM')
            ->add('CIN')
            ->add('numeroTel')
            ->add('adresse')
            ->add('groupeSanguin')
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre date de naissance']),
                    new Date(['message' => 'Veuillez entrer une date valide.']),
                ],
            ]);
            
        ;
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
