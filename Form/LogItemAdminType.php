<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * The type for LogItem
 */
class LogItemAdminType extends AbstractType {

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('exporterName');
        $builder->add('exportableId');
        $builder->add('exportableName');
        $builder->add('createdAt');
        $builder->add('invoker');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName() {
        return "logitem_form";
    }

}