<?php

namespace App\Form;

use App\Entity\Programmation;
use App\Form\Type\CommentType;
use App\Form\Type\CylinderCapacityType;
use App\Form\Type\EdcOffType;
use App\Form\Type\EgrOffType;
use App\Form\Type\EthanolType;
use App\Form\Type\FapOffType;
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
use App\Form\Type\VersionType;
use App\Form\Type\YearType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('make', MakeType::class)
            ->add('model', ModelType::class)
            ->add('version', VersionType::class)
            ->add('serial', SerialType::class)
            ->add('year', YearType::class)
            ->add('cylinderCapacity', CylinderCapacityType::class)
            ->add('power', PowerType::class)
            ->add('odometer', OdometerType::class)
            ->add('gearAutomatic', GearType::class)
            ->add('protocol', ProtocolType::class)
            ->add('readerTool', ReaderToolType::class)
            ->add('read', ReadType::class)
            ->add('odb', OdbType::class)
            ->add('edcOff', EdcOffType::class)
            ->add('egrOff', EgrOffType::class)
            ->add('ethanol', EthanolType::class)
            ->add('fapOff', FapOffType::class)
            ->add('stageOne', StageOneType::class)
            ->add('originalFile', OriginalFileType::class)
            ->add('comment', CommentType::class);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Programmation::class,
            'render_fieldset' => false,
            'show_legend' => false,
        ]);
    }
}
