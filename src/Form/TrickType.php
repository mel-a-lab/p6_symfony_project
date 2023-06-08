<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Form\TrickVideoType;
use App\Form\Type\LinksType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
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
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Nom" ne doit pas être vide.'
                    ])
                ]

            ])

            ->add('description', TextareaType::class, [
                'label' => 'Contenu de l\'article',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Description" ne doit pas être vide.'
                    ]),
                    new Length([
                        'max' => 10000,
                        // Modifier le nombre ici selon la longueur maximale que vous voulez
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])

            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotNull([
                        'message' => 'Le groupe ne doit pas être vide.'
                    ])
                ]
            ])

            ->add('images', FileType::class, [
                'label' => 'Images',
                'attr' => ['class' => 'form-control'],
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
                'multiple' => true,
                // Allow multiple images
                'attr' => ['accept' => 'image/*'], // Allow only image files to be selected
            ])

            //  ->add('trickVideos', LinksType::class)

            ->add('trickVideos', CollectionType::class, [
                'entry_type' => TrickVideoType::class,
                'label' => 'Vidéos',
                'attr' => ['class' => 'form-control'],
                'allow_add' => true,

                'required' => false,
                'by_reference' => false,

                'attr' => [
                    'class' => 'videos-collection',
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
