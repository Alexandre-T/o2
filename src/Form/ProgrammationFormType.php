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

use App\Form\Model\Programmation;
use App\Form\Type\CatOffType;
use App\Form\Type\CommentType;
use App\Form\Type\CylinderCapacityType;
use App\Form\Type\DtcOffType;
use App\Form\Type\EdcOffType;
use App\Form\Type\EgrOffType;
use App\Form\Type\EthanolType;
use App\Form\Type\FapOffType;
use App\Form\Type\GearAutomaticType;
use App\Form\Type\GearType;
use App\Form\Type\MakeType;
use App\Form\Type\ModelType;
use App\Form\Type\OdbType;
use App\Form\Type\OdometerType;
use App\Form\Type\OriginalFileType;
use App\Form\Type\PowerType;
use App\Form\Type\ProtocolType;
use App\Form\Type\ReaderToolType;
use App\Form\Type\ReadType;
use App\Form\Type\SerialType;
use App\Form\Type\StageOneType;
use App\Form\Type\TruckFileType;
use App\Form\Type\VersionType;
use App\Form\Type\YearType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Programmation form.
 */
class ProgrammationFormType extends AbstractType
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
            ->add('make', MakeType::class)
            ->add('model', ModelType::class)
            ->add('version', VersionType::class)
            ->add('serial', SerialType::class)
            ->add('year', YearType::class)
            ->add('cylinderCapacity', CylinderCapacityType::class)
            ->add('power', PowerType::class)
            ->add('odometer', OdometerType::class)
            ->add('gearAutomatic', GearAutomaticType::class)
            ->add('protocol', ProtocolType::class)
            ->add('readerTool', ReaderToolType::class)
            ->add('read', ReadType::class)
            ->add('odb', OdbType::class)
            ->add('catOff', CatOffType::class)
            ->add('dtcOff', DtcOffType::class)
            ->add('edcOff', EdcOffType::class)
            ->add('egrOff', EgrOffType::class)
            ->add('ethanol', EthanolType::class)
            ->add('fapOff', FapOffType::class)
            ->add('gear', GearType::class)
            ->add('stageOne', StageOneType::class)
            ->add('truckFile', TruckFileType::class)
            ->add('originalFile', OriginalFileType::class)
            ->add('comment', CommentType::class)
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
            'data_class' => Programmation::class,
        ]);
    }
}
