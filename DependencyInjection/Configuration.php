<?php

namespace Flexibledeveloper\PimcoreElasticBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('elastic');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('indexes')
                    ->arrayPrototype() // IndexName
                    ->children()
                        ->arrayNode('document')
                            ->variablePrototype()->end()
                        ->end()
                        ->variableNode('filter')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
