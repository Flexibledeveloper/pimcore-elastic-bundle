<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Client;

class ElasticaConnectionService
{
    protected array $configuration;
    protected ?Client $httpClient;

    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
        $this->httpClient = null;
        $this->connect();
    }

    public function getClient(): Client
    {
        if (null === $this->httpClient) {
            $this->connect();
        }

        return $this->httpClient;
    }

    private function connect(): void
    {
        $this->httpClient = new Client([
            'host' => $this->configuration['elasticHost'],
            'port' => $this->configuration['elasticPort']
        ]);

    }
}
