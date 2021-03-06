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

use App\Model\ServiceStatusInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ServiceStatusType.
 */
class ServiceStatusType extends AbstractType
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
            'label' => 'form.field.status-service',
            'choices' => [
                'service.status.open.text' => ServiceStatusInterface::OPEN,
                'service.status.close.text' => ServiceStatusInterface::CLOSE,
                'service.status.vacancy.text' => ServiceStatusInterface::VACANCY,
            ],
            'help' => 'form.help.status-service',
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'attr' => ['class' => 'form-check-inline p-0 pt-2 m-0'],
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
