<?php

namespace App\Form;

use App\Entity\Tasks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Titre'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description de la tâche',
                'attr' => [
                    'class' => 'form-control my-2 mb-3',
                    'rows' => 6,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez la description de la tâche',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'La description de la tâche doit faire entre {{ limit }} et 4096 caractères',
                        // Max length allowed by Symfony for security reasons.
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
