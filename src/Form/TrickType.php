<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
<<<<<<< Updated upstream
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
=======
>>>>>>> Stashed changes
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< Updated upstream
            ->add('name', TextType::class, [
=======
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
>>>>>>> Stashed changes
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Nom" ne doit pas être vide.'
                    ])
                ]
<<<<<<< Updated upstream
            ])
=======
            ]) 
>>>>>>> Stashed changes
            ->add('description', TextareaType::class, [
                'label' => 'Contenu de l\'article',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ "Description" ne doit pas être vide.'
                    ])
                ]
            ]);
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
