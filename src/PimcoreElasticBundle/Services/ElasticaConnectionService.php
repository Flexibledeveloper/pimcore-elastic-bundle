<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ElasticaConnectionService
{
    protected array $configuration;
    protected ?Client $httpClient;
    protected ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->httpClient = null;
        $this->connect();
        $this->params = $params;
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
            'host' => $this->params->get('serverURL'),
            'port' => $this->params->get('serverPort')
        ]);

    }
}
