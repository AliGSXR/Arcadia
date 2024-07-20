<?php

namespace App\Form;

use App\Entity\Habitat;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class HabitatType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $connectedUser = $this->security->getUser();
        if ($connectedUser->getRoles()[0] === 'ROLE_ADMIN'){
            $builder
                ->add('nom')
                ->add('description')
                ->add('images', EntityType::class, [
                    'class' => Image::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                ]);
        }
        if ($connectedUser->getRoles()[0] === 'ROLE_VETERINARIAN') {
            $builder->add('commentaire');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habitat::class,
        ]);
    }
}
