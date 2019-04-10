<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButtonTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Confirmation Type class.
 */
class ConfirmationType extends SubmitType implements SubmitButtonTypeInterface
{
    /**
     * Set default options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => ['class' => 'btn-danger confirm-delete'],
            'label' => 'modal.entity.delete.yes',
            'icon' => 'trash',
            'icon_family' => 'solid',
        ]);
    }

    /**
     * Provide parent type.
     *
     * @return string
     */
    public function getParent()
    {
        return SubmitType::class;
    }
}
