<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Query;

class ElasticaQueryService
{
    protected ElasticaConnectionService $elasticaClient;

    public function __construct(ElasticaConnectionService $elasticaClient)
    {
        $this->elasticaClient = $elasticaClient;
    }

    public function queryIndex(string $indexName, Query $query, array $options = null): array
    {
        $client = $this->elasticaClient->getClient();

        $index = $client->getIndex($indexName);
        return $index->search($query, $options)->getResults();
    }
}
