<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Services;

use Elastica\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConnectionService
{
    private ?Client $httpClient;
    private ContainerInterface $params;

    public function __construct(ContainerInterface $params)
    {
        $this->httpClient = null;
        $this->params = $params;

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
            'host' => $this->params->getParameter('serverURL'),
            'port' => $this->params->getParameter('serverPort')
        ]);

    }
}
