<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Person Type class.
 */
class PlainPasswordType extends AbstractType
{
    /**
     * Set default options.
     *
     * @param OptionsResolver $resolver the options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => PasswordType::class,
            'invalid_message' => 'error.plain-password.repeated',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options' => [
                'label' => 'form.field.password',
                'help' => 'form.help.password',
            ],
            'second_options' => [
                'label' => 'form.field.repeat-password',
                'help' => 'form.help.repeat-password',
            ],
        ]);
    }

    /**
     * Provide parent type.
     *
     * @return string
     */
    public function getParent()
    {
        return RepeatedType::class;
    }
}
