<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_p', TextType::class, [
                'label' => 'Nom du produit *',
                'attr' => [
                    'placeholder' => 'Ex: Riz',
                    'minlength' => 3,
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('categorie_p', TextType::class, [
                'label' => 'Catégorie *',
                'attr' => [
                    'placeholder' => 'Ex: Alimentaire',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La catégorie est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'La catégorie doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La catégorie ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('quantite_stock', IntegerType::class, [
                'label' => 'Quantité *',
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Range([
                        'min' => 0,
                        'max' => 1000000,
                        'notInRangeMessage' => 'La quantité doit être entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('unite_p', TextType::class, [
                'label' => 'Unité *',
                'attr' => [
                    'placeholder' => 'Ex: Kg',
                    'minlength' => 1,
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'unité est obligatoire.']),
                    new Length([
                        'min' => 1,
                        'max' => 20,
                        'maxMessage' => 'L\'unité ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('emplacement', TextType::class, [
                'label' => 'Emplacement *',
                'attr' => [
                    'placeholder' => 'Ex: Rayon A',
                    'minlength' => 2,
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'emplacement est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'L\'emplacement doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'emplacement ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('date_ajout', DateType::class, [
                'label' => 'Date d\'ajout *',
                'widget' => 'single_text',
                'attr' => [
                    'min' => date('Y-m-d'),
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La date d\'ajout est obligatoire.']),
                ],
            ])
            ->add('date_expiration', DateType::class, [
                'label' => 'Date d\'expiration',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'min' => date('Y-m-d'),
                ],
            ])
            ->add('photo_p', FileType::class, [
                'label' => 'Photo (optionnel)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Photo: format invalide (jpg/png/webp/gif).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'constraints' => [
                new Callback(function (?Produit $produit, ExecutionContextInterface $context): void {
                    if (!$produit instanceof Produit) {
                        return;
                    }

                    $start = $produit->getDateAjout();
                    $end = $produit->getDateExpiration();
                    if ($start && $end && $end < $start) {
                        $context
                            ->buildViolation('La date d\'expiration doit être après la date d\'ajout.')
                            ->atPath('date_expiration')
                            ->addViolation();
                    }
                }),
            ],
            'attr' => [
                'class' => 'needs-validation',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
