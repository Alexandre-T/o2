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

namespace App\Form;

use App\Form\Model\CreditOrder;
use App\Form\Type\QuantityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Credit Form.
 */
class CreditFormType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('ten', QuantityType::class, [
                'label' => 'form.field.article-10',
                'help' => 'form.help.article-10',
            ])
            ->add('fifty', QuantityType::class, [
                'label' => 'form.field.article-50',
                'help' => 'form.help.article-50',
            ])
            ->add('hundred', QuantityType::class, [
                'label' => 'form.field.article-100',
                'help' => 'form.help.article-100',
            ])
            ->add('fiveHundred', QuantityType::class, [
                'label' => 'form.field.article-500',
                'help' => 'form.help.article-500',
            ])
        ;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreditOrder::class,
        ]);
    }
}
