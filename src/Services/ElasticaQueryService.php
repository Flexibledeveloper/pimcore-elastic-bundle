<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use AppBundle\Constant\ElasticIndexConstants;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Wildcard;
use Elastica\Search;
use Symfony\Component\Serializer\SerializerInterface;
use Pimcore\Config;

class ElasticaQueryService implements ElasticIndexConstants
{
    protected ElasticaConnectionService $elasticaClient;
    protected SerializerInterface $serializer;
    private Config $config;

    public function __construct(
        ElasticaConnectionService $elasticaClient,
        SerializerInterface $serializer,
        Config $config
    )
    {
        $this->elasticaClient = $elasticaClient;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function initialIndexQuery(
        string $searchstring,
        int $pageSize = 10,
        int $offset = 0,
        string $languageCode = null
    ): array
    {
        $boolQuery = new BoolQuery();
        if (null !== $languageCode) {
            $boolQuery->addMust(
                new Match(
                    'language',
                    [
                        'query' => $languageCode
                    ]
                )
            );
        }

        $boolQuery->addShould(
            new Wildcard(
                'content.content',
               $searchstring
            )
        );

        $boolQuery->addShould(
            new Match(
                'pageTitle',
                [
                    'query' => $searchstring,
                    'boost' => 1.1
                ]
            )
        );

        $boolQuery->addShould(new Wildcard('pageTitle', $searchstring));

        $query = new Query($boolQuery);
        $elasticSearch = new Search($this->elasticaClient->getClient());

        $result = $elasticSearch->search($query);
        return $result->getResponse()->getData();
    }

    public function queryIndex(string $indexName, Query $query, array $options = null): array
    {
        $client = $this->elasticaClient->getClient();

        $index = $client->getIndex($indexName);
        return $index->search($query, $options)->getResults();
    }
}
