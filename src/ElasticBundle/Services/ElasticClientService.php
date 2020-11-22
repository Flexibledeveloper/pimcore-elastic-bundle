<?php

namespace ElasticBundle\Services;

use GuzzleHttp\Client;

class ElasticClientService
{
    protected array $configuration;
    protected Client $httpClient;

    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
        $this->connect();
    }

    public function getClient(): Client
    {
        if (!$this->httpClient) {
            $this->connect();
        }

        return $this->httpClient;
    }

    private function connect(): void
    {
        $clientOptions = $this->buildClientOptions();
        $clientOptions['base_uri'] = $this->getElasticHostUrl();

        $this->httpClient = new Client($clientOptions);
    }

    private function getElasticHostUrl(): string
    {
        return array_key_exists('elasticHost', $this->configuration) ?
            $this->configuration['elasticHost'] . ':' .$this->configuration['elasticPort'] :
            'http://elasticsearch:9200';
    }

    private function buildClientOptions(): array
    {
        return [
            'timeout'  => 2.0,
            // 'auth' => ['username', 'password'] TODO Implement
        ];
    }

}
