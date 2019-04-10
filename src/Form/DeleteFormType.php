<?php

namespace App\Form;

use App\Form\Type\ConfirmationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteFormType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder the builder
     * @param array                $options options provided
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('delete', ConfirmationType::class)
        ;
    }

    /**
     * Default configuration.
     *
     * @param OptionsResolver $resolver the opstions resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'DELETE',
        ]);
    }
}
