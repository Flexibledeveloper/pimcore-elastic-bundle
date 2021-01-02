<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Client;
use Elastica\Index\Stats;
use Elastica\Index;
use Elastica\Mapping;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IndexService
{
    private Client $client;

    private ContainerInterface $container;

    public function __construct(ConnectionService $client, ContainerInterface $container)
    {
        $this->client = $client->getClient();
        $this->container = $container;
    }

    public function createIndex(string $indexName, bool $delete = true, int $shards = 1): Index
    {
        $index = $this->client->getIndex($indexName);

        $index->create([
            'settings' => [
                'index' => [
                    'number_of_shards' => $shards,
                    'number_of_replicas' => 1
                ]
            ]],
            [
                'recreate' => $delete,
            ]);

        $mappings = $this->createMappings($indexName);
        $index->setMapping($mappings);

        return $index;
    }

    public function getIndexStatus(string $indexName): Stats
    {
        return $this->client->getIndex($indexName)->getStats();
    }

    public function getIndex(string $indexName): Index
    {
        return $this->client->getIndex($indexName);
    }

    private function createMappings(string $indexName): Mapping
    {
        $indexMappingConfiguration = $this->container->getParameter(sprintf('elastic.indexes.%s', $indexName))['document'];

        return new Mapping($indexMappingConfiguration);
    }
}
