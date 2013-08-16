<?php

namespace Kunstmaan\FormBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference;

/**
 * Listen for the kunstmaan_form.exporter tag.
 * Add every service with this tag as an exporter on the kunstmaan_form.exporter_service.
 *
 * Internal serices like Zendesk aren't loaded this way.
 * Instead they are only instantiated when their configuration is present.
 * This happens in KunstmaanFormExtension and the FormExporterService.
 */
class FormExporterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(
            'kunstmaan_form.exporter_service'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kunstmaan_form.exporter'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addExporter',
                array(new Reference($id))
            );
        }
    }
}
