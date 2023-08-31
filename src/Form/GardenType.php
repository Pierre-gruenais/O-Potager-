<?php

namespace App\Form;

use App\Entity\Garden;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GardenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('address')
            ->add('postalCode')
            ->add('city')
            ->add('water')
            ->add('tool')
            ->add('shed')
            ->add('cultivation')
            ->add('surface')
            ->add('phoneAccess')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('state')
            ->add('lat')
            ->add('lon')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Garden::class,
        ]);
    }
}
