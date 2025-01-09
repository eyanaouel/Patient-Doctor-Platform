<?php

namespace App\Form;
use App\Form\RDVType;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\RDV;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    // {
    //     $builder
    //         ->add('dateHeure', null, [
    //             'widget' => 'single_text',
    //         ])
    //         ->add('motif')
    //         ->add('notes')
    //         ->add('statut')
    //         ->add('medecin', EntityType::class, [
    //             'class' => Medecin::class,
    //             'choice_label' => 'id',
    //         ])
    //         ->add('patient', EntityType::class, [
    //             'class' => Patient::class,
    //             'choice_label' => 'id',
    //         ])
    //     ;
    // }


    {
        $builder
            ->add('dateHeure', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('motif', ChoiceType::class, [
                'choices' => [
                    'Urgence' => 'Urgence',
                    'Visite de contrôle' => 'Visite de contrôle',
                    'Consultation' => 'Consultation'
                ]
            ])
            ->add('notes');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RDV::class,
        ]);
    }
}
