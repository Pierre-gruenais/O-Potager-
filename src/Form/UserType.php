<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('username', TextType::class, [
                "label" => "Username"
            ]);

        if ($options[ "custom_option" ] !== "edit") {
            $builder
                ->add('password', RepeatedType::class, [
                    "help"            => "Le mot de passe doit contenir au moins 4 caractères",
                    "constraints"     => [
                        new Length([
                            "min"        => 4,
                            "minMessage" => "Le mot de passe doit contenir au moins 4 caractères"
                        ])
                    ],
                    "type"            => PasswordType::class,
                    'invalid_message' => 'Les deux champs doivent être identique',
                    'required'        => true,
                    'first_options'   => ['label' => 'Le mot de passe', "attr" => ["placeholder" => "*****"]],
                    'second_options'  => ['label' => 'Répétez le mot de passe', "attr" => ["placeholder" => "*****"]],
                ]);
        }
        $builder
            ->add('email', EmailType::class, [
                "label" => "L'email",
                "attr"  => [
                    "placeholder" => "Email de l'utilisateur"
                ]
            ])
            ->add('phone', TextType::class, [
                "label" => "Phone number"
            ])
            ->add('roles', ChoiceType::class, [
                "label"        => "Privilèges",
                "choices"      => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => '',
                ],
                "expanded"     => true,
                "multiple"     => true,
                
            ])
            ->add('avatar', UrlType::class, [
                "label" => "Avatar",
                "attr"  => [
                    "placeholder" => "Url pour le moment"
                ]
            ])
            ->add('createdAt', DateType::class, [
                "label"    => "Date de creation",
                "input"    => "datetime_immutable",
                "widget"   => "single_text",
                'row_attr' => ['class' => 'col-2']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => User::class,
            'custom_option' => "default",
        ]);
    }
}