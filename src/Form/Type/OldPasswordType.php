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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Old password type class.
 */
class OldPasswordType extends AbstractType
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
            'required' => true,
            'label' => 'form.field.old-password',
            'help' => 'form.help.old-password',
        ]);
    }

    /**
     * Provide parent type.
     *
     * @return string
     */
    public function getParent()
    {
        return PasswordType::class;
    }
}
