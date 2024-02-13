<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


/**
 * Form type for creating or editing a user.
 *
 * This form type class defines the structure of the form used for creating or editing users.
 */
class UserFormType extends AbstractType
{


    /**
     * Builds the form.
     *
     * This method is responsible for configuring the form fields and their options.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options for configuring the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                                                        'type' => PasswordType::class,
                                                        'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                                                        'required' => true,
                                                        'first_options'  => ['label' => 'Mot de passe'],
                                                        'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                                                    ]
            )
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('roles', CheckboxType::class,[
                                                    'mapped' => false,
                                                    'required' => false,
                                                    'attr' => ['class' => 'my-2'],
                                                    'label' => 'Voulez-vous inscrire cet utilisateur en tant qu\'administrateur ? Oui '
                                                ]
            )
        ;
    }//end buildForm()


}
