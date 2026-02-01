<?php

namespace App\Form;

use App\Entity\Package;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('devise', ChoiceType::class, [
                'choices' => [
                    'TND' => 'TND',
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                ]
            ])
            ->add('duree')
            ->add('uniteDuree', ChoiceType::class, [
                'choices' => [
                    'MOIS' => 'MOIS',
                    'ANNEE' => 'ANNEE',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'FREE' => 'FREE',
                    'PRO' => 'PRO',
                    'BUSINESS' => 'BUSINESS',
                ]
            ])
            ->add('essaiGratuit')
            ->add('statut')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Package::class,
        ]);
    }
}
