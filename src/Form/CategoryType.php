<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true])

            ->add('description', TextareaType::class, [
                'required' => false,
            ])

            ->add('color', ColorType::class, [
                'required' => false,
            ])

            ->add('icon', TextType::class, [
                'required' => false,
            ])

            ->add('isActive', CheckboxType::class, [
                'required' => false,
            ])

            ->add('position', IntegerType::class, [
                'required' => false,
            ])

            ->add('visibility', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Public' => 'public',
                    'Privé' => 'private',
                ],
                'placeholder' => 'Choisir...',
            ])

            ->add('taskLimit', IntegerType::class, [
                'required' => false,
            ])

            ->add('createAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])

            ->add('updateAt', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])

            // ✅ FIX: jamais null
            ->add('no', TextType::class, [
                'required' => false,
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
