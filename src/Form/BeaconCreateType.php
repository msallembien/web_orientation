<?php

namespace App\Form;

use App\Entity\Beacon;
use App\Entity\Map; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BeaconCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'mapped' => false, // IMPORTANT
                'choices' => [
                    'Normale' => 'normal',
                    'Départ' => 'depart',
                    'Arrivée' => 'arrivee',
                ],
            ])
            ->add('longitude')
            ->add('latitude')
            ->add('id_map', EntityType::class, [
                'class' => Map::class,
                'choice_label' => 'name_map',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Beacon::class,
        ]);
    }
}