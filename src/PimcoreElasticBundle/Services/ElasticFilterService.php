<?php

namespace PimcoreElasticBundle\PimcoreElasticBundle\PimcoreElasticBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class ElasticFilterService
{
    private string $configFilePath;
    private TranslatorInterface $translator;
    protected SerializerInterface $serializer;
    private ContainerInterface $container;

    public function __construct(array $configuration, SerializerInterface $serializer, TranslatorInterface $translator, ContainerInterface $container)
    {
        $this->configFilePath = $configuration['configFilePath'];
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->container = $container;
    }

    public function getAllFilters(string $indexName = '', string $language = 'en'): ?array
    {
        $config = $this->loadConfiguration();
        $filters = null;
        $language = 'en';

        foreach ($config['indexes'] as $configIndexName => $filterService) {
            if (!empty($indexName) && $configIndexName !== $indexName) {
                continue;
            }

            $availableFilterService = $filterService['filter'];
            if ($this->container->hasParameter(sprintf('elastic.indexes.%s.filter',$configIndexName))) {
                $availableFilterService = $this->container->getParameter(sprintf('elastic.indexes.%s.filter',$configIndexName));
            }

            if (null === $availableFilterService) {
                continue;
            }

            if(!$this->implementsFilterInterface(
                $this->container->get($availableFilterService)
            )) {
                continue;
            }

            $filterList = $this->container->get($availableFilterService)->getFilters([], [], $language);
            $filters['filters'][$configIndexName] =
                $filterList->toArray();
        }

        return $filters;
    }

    /**
     * @return mixed
     */
    private function loadConfiguration()
    {
        return Yaml::parseFile(__DIR__. '/'.$this->configFilePath)['elastic'];
    }

    private function implementsFilterInterface(?object $filterService): bool
    {
        $interfaces = class_implements($filterService);
        return isset($interfaces['ElasticBundle\FilterServiceInterface']);
    }
}
