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
class BeaconCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('longitude')
            ->add('latitude')
            ->add('isPlaced', CheckboxType::class, [
                'required' => false,
            ])
            ->add('placedAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Beacon::class,
        ]);
    }
}