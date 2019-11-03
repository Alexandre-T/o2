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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Vat Type class.
 */
class VatType extends AbstractType
{
    /**
     * Set default options.
     *
     * @param OptionsResolver $resolver the options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'domtom' => false,
        ]);

        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => 'form.field.vat',
            'help' => 'form.help.vat',
            'required' => true,
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                '0.0850' => '0.0850',
                '0.2000' => '0.2000',
                '0.0000' => '0.0000',
            ],
            'choice_label' => function ($choice) {
                switch ($choice) {
                    //TODO change with constant
                    case 0.0000:
                        return 'form.field.vat-europe';
                    case 0.0850:
                        return 'form.field.vat-domtom';
                    default:
                        return 'form.field.vat-default';
                }
            },
        ]);
    }

    /**
     * Provide parent type.
     *
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
