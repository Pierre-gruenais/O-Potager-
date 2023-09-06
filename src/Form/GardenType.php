<?php

namespace App\Form;

use App\Entity\Garden;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GardenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse du jardin'
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal du jardin'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville du jardin'
            ])
            ->add('surface', IntegerType::class, [
                'label' => 'Surface du jardin (m²)'
            ])
            ->add('water', ChoiceType::class, [
                'label' => 'Point d\'eau',
                'choices' => [
                    'oui' => true,
                    'non' => false
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]
            ] )
            ->add('tool', ChoiceType::class, [
                'label' => 'Présence d\'outils',
                'choices' => [
                    'oui' => true,
                    'non' => false
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]    
            ])
            ->add('shed', ChoiceType::class, [
                'label' => 'Présence d\'un abri de jardin',
                'choices' => [
                    'oui' => true,
                    'non' => false
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]    
            ])
            ->add('cultivation', ChoiceType::class, [
                'label' => 'Cultivation déjà en cours',
                'choices' => [
                    'oui' => true,
                    'non' => false
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]    
            ])

            ->add('state', ChoiceType::class, [
                'label' => 'Etat du jardin',
                'choices' => [
                    'A l\'abandon' => 'abandon',
                    'Entretenu' => 'entretenu',
                    'Nettoyer' => 'nettoyer',
                ],
                'expanded' => true,
            ])
            ->add('phoneAccess', ChoiceType::class, [
                'label' => 'Affichage du numéro de téléphone sur l\'annonce',
                'choices' => [
                    'oui' => true,
                    'non' => false
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]   
                ])
            ->add('checked', ChoiceType::class, [
                'label' => 'Validation de l\'annonce',
                'choices' => [
                    'Valider' => "Valider",
                    'Refuser' => "Refuser"
                ],
                'expanded' => true,
                "attr" => [
                    'class' => 'd-flex flex-wrap gap-4'
                ]   
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Garden::class,
        ]);
    }
}
