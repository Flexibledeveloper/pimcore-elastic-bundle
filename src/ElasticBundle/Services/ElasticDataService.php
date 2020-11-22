<?php

namespace ElasticBundle\Services;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Pimcore\Config;
use Symfony\Component\HttpFoundation\Response;

class ElasticDataService
{
    protected ElasticClientService $elasticClient;
    protected SerializerInterface $serializer;
    private Config $config;


    public function __construct(
        ElasticClientService $elasticClient,
        SerializerInterface $serializer,
        Config $config
    ){
        $this->elasticClient = $elasticClient;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function createData(string $indexName, array $objectDataToCreate): ResponseInterface
    {
        $elasticClient = $this->elasticClient->getClient();

        return $elasticClient->post(
            $this->getElasticDocumentUrl($indexName, $objectDataToCreate['id']),
            [
                'json' => $objectDataToCreate,
            ]);
    }

    public function updateData(string $indexName, array $objectDataToCreate): ResponseInterface
    {
        $elasticClient = $this->elasticClient->getClient();

        return $elasticClient->put(
            $this->getElasticDocumentUrl($indexName, $objectDataToCreate['id']),
            [
                'json' => $objectDataToCreate,
            ]);
    }

    public function queryIndex(string $indexName, string $queryType = "match", array $query = []):? string
    {
        if (empty($query)) {
            $query = [
                "language" => $this->config['general']['default_language']
            ];
        }

        $queryArray = [
            "query" => [
                $queryType => $query,
            ]
        ];

        $response = $this->elasticClient->getClient()->request(
            'GET',
            '/'.$indexName.'/_search',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($queryArray, JSON_FORCE_OBJECT),
                'http_errors' => false,
            ]
        );

        if (
            Response::HTTP_OK !== $response->getStatusCode() ||
            Response::HTTP_NO_CONTENT !== $response->getStatusCode()
        ) {
            return null;
        }

        return $response->getBody()->getContents();
    }

    private function getElasticDocumentUrl(string $indexName, int $id): string
    {
        return '/'.$indexName.'/_doc/'.$id;
    }
}
