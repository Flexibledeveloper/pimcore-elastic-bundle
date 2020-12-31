<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Query;

class QueryService
{
    protected ConnectionService $elasticaClient;

    public function __construct(ConnectionService $elasticaClient)
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
