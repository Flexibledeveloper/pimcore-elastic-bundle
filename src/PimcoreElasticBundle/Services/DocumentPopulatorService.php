<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Document;
use Elastica\Index;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DocumentPopulatorService
{
    private IndexService $indexService;
    private ConnectionService $connectionService;
    private ContainerInterface $params;
    private SerializerInterface $serializer;

    public function __construct(
        IndexService $indexService,
        ConnectionService $connectionService,
        ContainerInterface $params,
        SerializerInterface $serializer)
    {
        $this->indexService = $indexService;
        $this->connectionService = $connectionService;
        $this->params = $params;
        $this->serializer = $serializer;
    }

    public function populateIndexWithDocuments(string $indexName, array $objects): void
    {
        /** @var Index $index */
        $index = $this->indexService->getIndex($indexName);

        foreach ( $objects as $object) {
            $index->addDocument(
                new Document(null, $object)
            );
        }

        $index->refresh();
    }
}
