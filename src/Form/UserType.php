<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class, [])
            ->add('nom')
            ->add('prenom')
            ->add('role', ChoiceType::class, [
                'choices'  => [
                    'Employé' => 'ROLE_EMPLOYEE',
                    'Vétérinaire' => 'ROLE_VETERINARIAN',
                ],
                'choice_label' => function($choice, $key, $value) {
                    // Affiche les labels des options, 'Employé' ou 'Vétérinaire'
                    return $key;
                },
                'label' => 'Role',
                'expanded' => false, // Utiliser un menu déroulant
                'multiple' => false, // Pas de sélection multiple
                'placeholder' => 'Choisissez un rôle',
                'mapped' => false, // Champ non mappé
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
