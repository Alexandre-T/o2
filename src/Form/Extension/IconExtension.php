<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Icon button extension class.
 *
 * Create an icon in a button class.
 */
class IconExtension extends AbstractTypeExtension implements FormTypeExtensionInterface
{
    /**
     * Builds the form view.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the view.
     *
     * A view of a form is built before the views of the child forms are built.
     * This means that you cannot access child views in this method. If you need
     * to do so, move your logic to {@link finishView()} instead.
     *
     * @see FormTypeExtensionInterface::buildView()
     *
     * @param FormView      $view    The view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['icon'] = $options['icon'];
        $view->vars['icon_family'] = $options['icon_family'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'icon' => null,
            'icon_family' => null,
        ]);
        $resolver->setAllowedValues('icon_family', [
            null,
            'solid',
            'regular',
            'light',
            'brands',
        ]);
    }

    /**
     * Return all types using this extension.
     *
     * @return array
     */
    public static function getExtendedTypes(): iterable
    {
        return [
            ButtonType::class,
            TextType::class,
        ];
    }
}
