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

use App\Form\Model\UploadProgrammation;
use App\Form\Type\EdcStoppedType;
use App\Form\Type\EgrStoppedType;
use App\Form\Type\EthanolDoneType;
use App\Form\Type\FapStoppedType;
use App\Form\Type\FinalFileType;
use App\Form\Type\ResponseType;
use App\Form\Type\StageOneDoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadProgrammationFormType extends AbstractType
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
        $builder
            ->add('edcStopped', EdcStoppedType::class)
            ->add('egrStopped', EgrStoppedType::class)
            ->add('ethanolDone', EthanolDoneType::class)
            ->add('fapStopped', FapStoppedType::class)
            ->add('stageOneDone', StageOneDoneType::class)
            ->add('finalFile', FinalFileType::class)
            ->add('response', ResponseType::class)
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
            'data_class' => UploadProgrammation::class,
        ]);
    }
}
