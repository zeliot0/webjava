<?php

namespace App\Form;

use App\Entity\Feature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('code', TextType::class, [
                'label' => 'Code',
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])

            ->add('limite', IntegerType::class, [
                'label' => 'Limite',
            ])

            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'FREE' => 'FREE',
                    'PRO' => 'PRO',
                    'BUSINESS' => 'BUSINESS',
                ],
                'attr' => [
                    'class' => 'nexa-select'
                ],
            ])

            ->add('statut', CheckboxType::class, [
                'label' => 'Statut actif',
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
        ]);
    }
}
