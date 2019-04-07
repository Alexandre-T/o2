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

use App\Entity\User;
use App\Form\Type\ComplementType;
use App\Form\Type\CountryType;
use App\Form\Type\FamilyNameType;
use App\Form\Type\GivenNameType;
use App\Form\Type\LocalityType;
use App\Form\Type\PersonType;
use App\Form\Type\PostalCodeType;
use App\Form\Type\SocietyType;
use App\Form\Type\StreetAddressType;
use App\Form\Type\TelephoneType;
use App\Form\Type\TvaNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Profile form builder.
 */
class ProfileFormType extends AbstractType
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
            ->add('type', PersonType::class)
            ->add('givenName', GivenNameType::class)
            ->add('name', FamilyNameType::class)
            ->add('society', SocietyType::class)
            ->add('tvaNumber', TvaNumberType::class)
            ->add('streetAddress', StreetAddressType::class)
            ->add('complement', ComplementType::class)
            ->add('postalCode', PostalCodeType::class)
            ->add('locality', LocalityType::class)
            ->add('country', CountryType::class)
            ->add('telephone', TelephoneType::class)
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
            'data_class' => User::class,
            'render_fieldset' => false,
            'show_legend' => false,
            'validation_groups' => ['Default'],
        ]);

        parent::configureOptions($resolver);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_profile';
    }
}
