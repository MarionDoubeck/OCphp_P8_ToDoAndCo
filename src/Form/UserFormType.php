<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'E-mail'
            ])
            ->add('roles', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'my-2'
                ],
                'label' => 'Voulez-vous inscrire cet utilisateur en tant qu\'administrateur ? Oui '
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control my-2',
                    ],
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe',
                    'attr' => [
                        'class' => 'form-control my-2',
                    ],
                ],
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
