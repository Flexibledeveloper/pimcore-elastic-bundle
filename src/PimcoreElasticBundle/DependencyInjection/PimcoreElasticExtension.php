<?php

namespace Flexibledeveloper\PimcoreElasticBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PimcoreElasticExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $this->loadServicesConfiguration($container);

        $this->loadServerSettings($config, $container);
        $this->loadConfigSettingsForIndexesIfExists($config, $container);
    }

    private function loadServicesConfiguration(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    private function loadConfigSettingsForIndexesIfExists(array $config, ContainerBuilder $container): void
    {
        foreach($config['indexes'] as $indexName => $indexConfig) {
            $container->setParameter(sprintf('elastic.indexes.%s', $indexName), $indexConfig);
        }
    }

    private function loadServerSettings(array $config, ContainerBuilder $container): void
    {
        if (array_key_exists('serverURL', $config)) {
            $container->setParameter('serverURL', $config['serverURL']);
        }

        if (array_key_exists('serverPort', $config)) {
            $container->setParameter('serverPort', $config['serverPort']);
        }
    }
}
