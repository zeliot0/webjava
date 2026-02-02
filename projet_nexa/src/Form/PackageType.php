<?php

namespace App\Form;

use App\Entity\Package;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])

            ->add('prix', NumberType::class, [
                'label' => 'Prix',
            ])

            ->add('devise', ChoiceType::class, [
                'choices' => [
                    'TND' => 'TND',
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                ],
            ])

            ->add('duree', IntegerType::class, [
                'required' => false,
            ])

            ->add('uniteDuree', ChoiceType::class, [
                'choices' => [
                    'MOIS' => 'MOIS',
                    'ANNEE' => 'ANNEE',
                ],
            ])

            ->add('type', ChoiceType::class, [
                'choices' => [
                    'FREE' => 'FREE',
                    'PRO' => 'PRO',
                    'BUSINESS' => 'BUSINESS',
                ],
            ])

            ->add('essaiGratuit', CheckboxType::class, [
                'required' => false,
            ])

            ->add('statut', CheckboxType::class, [
                'required' => false,
            ]);
    }
}
