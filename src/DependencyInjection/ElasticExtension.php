<?php

namespace Flexibledeveloper\PimcoreElasticBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ElasticExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $this->loadServicesConfiguration($container);

        foreach($config['indexes'] as $indexName => $indexConfig) {
            if (!array_key_exists('filter', $indexConfig)) {
                continue;
            }

            $container->setParameter(sprintf('elastic.indexes.%s.filter', $indexName), $indexConfig['filter']);
        }
    }

    private function loadServicesConfiguration(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}