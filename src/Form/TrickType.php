<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name') // mettre type chaine de caractères
            // ajouter les propriétés simple (description avec contrainte et le groupe, ajouter contraintes dans formulaire, faire afficher erreur sur front, on peut typer juste après le champ , c'est le cas pour le groupe)
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
