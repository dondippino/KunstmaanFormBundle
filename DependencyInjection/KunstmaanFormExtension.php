<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanFormExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        // Hook in the entire export node as a single parameter.
        // This way we can pass the entire node as a parameter to FormExporterService in the service container.
        $processor     = new Processor();
        $configuration = new Configuration();
        /** @var $config array */
        $config = $processor->processConfiguration($configuration, $configs);

        // Set the exporter configuration so the FormExporterService can pick it up.
        // We'll load builtin services this way.
        $param = array();
        if (array_key_exists('exporters', $config)) {
            $param = $config['exporters'];
        }
        $container->setParameter('kunstmaan_form.exporter_configuration', $param);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
