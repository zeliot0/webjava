<?php

namespace App\Form;

use App\Entity\Mouvement;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class MouvementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_m', ChoiceType::class, [
                'label' => 'Type *',
                'choices' => [
                    'Entrée' => 'ENTREE',
                    'Sortie' => 'SORTIE',
                ],
                'placeholder' => 'Choisir...',
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire.']),
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité *',
                'attr' => [
                    'min' => 1,
                    'step' => 1,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Range([
                        'min' => 1,
                        'max' => 1000000,
                        'notInRangeMessage' => 'La quantité doit être entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('date_mouvement', DateType::class, [
                'label' => 'Date *',
                'widget' => 'single_text',
                'attr' => [
                    'min' => date('Y-m-d'),
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire.']),
                ],
            ])
            ->add('motif', TextType::class, [
                'label' => 'Motif *',
                'attr' => [
                    'placeholder' => 'Ex: Vente, Achat, Correction...',
                    'minlength' => 2,
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le motif est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le motif doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le motif ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('produit', EntityType::class, [
                'label' => 'Produit *',
                'class' => Produit::class,
                'choice_label' => 'nomP',
                'placeholder' => 'Choisir un produit...',
                'constraints' => [
                    new NotBlank(['message' => 'Le produit est obligatoire.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mouvement::class,
            'attr' => [
                'class' => 'needs-validation',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
