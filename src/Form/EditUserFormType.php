<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => false,
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
                'label' => 'Voulez-vous vous inscrire en tant qu\'administrateur ? Oui '
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Modifiez votre mot de passe ou laissez vide pour ne pas le modifier',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control my-2',
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // Max length allowed by Symfony for security reasons.
                        'max' => 4096,
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
