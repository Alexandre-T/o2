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

use App\Entity\Settings;
use App\Exception\SettingsException;
use App\Form\Type\ServiceStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Settings form builder.
 *
 * This form is used by admin to create or update an settings.
 */
class SettingsFormType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @throws SettingsException when value_class is not implemented
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('value', $this->getValueClass($options['value_class']), [
            'label' => "settings.{$options['code']}",
            'help' => "form.help.setting.{$options['code']}",
            'required' => true,
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
            'value_class' => 'string',
        ]);

        $resolver->setRequired('code');

        parent::configureOptions($resolver);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "SettingsProfileType" => "settings_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_settings';
    }

    /**
     * Value class getter.
     *
     * @param string $valueClass the class of value (integer, string, DateTimeImmutable, ...)
     *
     * @throws SettingsException when value_class is not implemented
     *
     * @return string
     */
    private function getValueClass(string $valueClass): string
    {
        switch ($valueClass) {
            case 'string':
                return TextType::class;
            case 'int':
                return IntegerType::class;
            case 'date':
                return DateType::class;
            case 'status':
                return ServiceStatusType::class;
        }

        throw new SettingsException("{$valueClass} is not implemented by SettingsFormType");
    }
}
