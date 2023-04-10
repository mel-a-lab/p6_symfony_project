<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name', 
                'constraints' => [
                    new NotNull([
                        'message' => 'Le groupe ne doit pas être vide.'
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Nom" ne doit pas être vide.'
                    ])
                ]

            ]) 

            ->add('description', TextareaType::class, [
                'label' => 'Contenu de l\'article',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Description" ne doit pas être vide.'
                    ])
                ]
            ])

            ->add('images', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/*'
                                ],
                                'mimeTypesMessage' => 'Merci de télécharger une image valide.',
                            ])
                        ]
                    ])
            ],
                'multiple' => true, // Allow multiple images
                'attr' => ['accept' => 'image/*'], // Allow only image files to be selected
            ])

            ->add('videos', CollectionType::class, [
                'entry_type' => UrlType::class,
                'label' => 'Vidéos',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'attr' => [
                    'class' => 'videos-collection',
                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'video-input',
                        'placeholder' => 'Ajouter une URL de vidéo',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le champ "Vidéos" ne doit pas être vide.',
                        ]),
                        new Url([
                            'message' => 'Veuillez saisir une URL valide pour la vidéo.',
                        ]),
                    ],
                ],
                'constraints' => [
                    new Count([
                        'max' => 10,
                        'maxMessage' => 'Vous ne pouvez pas ajouter plus de {{ limit }} vidéos.',
                    ]),
                ],
            ]);
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
