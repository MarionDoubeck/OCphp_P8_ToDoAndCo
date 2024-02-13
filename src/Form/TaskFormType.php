<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Form type for creating or editing a task.
 *
 * This form type class defines the structure of the form used for creating or editing tasks.
 */
class TaskFormType extends AbstractType
{

    /**
     * Builds the form.
     *
     * This method is responsible for configuring the form fields and their options.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options for configuring the form.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class)
        ;
    }//end buildForm()


}//end class
