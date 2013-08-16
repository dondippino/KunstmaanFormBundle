<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_form');

        // http://symfony.com/doc/2.2/components/config/definition.html
        $rootNode
            ->children()
                ->arrayNode('export')
                    ->children()
                    ->arrayNode('zendesk')
                        ->children()
                            ->scalarNode('api_key')->isRequired()->end()
                            ->scalarNode('domain')->isRequired()->end()
                    ->end()
                ->end()
            ->end();

        // TODO: How would we add exporters defined by third parties here?
        //       Maybe work with the tagging system? And define regular services?

        return $treeBuilder;
    }
}
