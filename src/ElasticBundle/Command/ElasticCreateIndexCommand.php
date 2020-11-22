<?php

namespace ElasticBundle\Command;

use ElasticBundle\Services\ElasticClientService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;

class ElasticCreateIndexCommand extends AbstractCommand
{
    protected const OPTIONINDEXNAME = 'indexName';
    protected const CONFIG_FILE_OPTION = 'configFile';
    protected const CONFIG_FILE = 'elastic.yml';

    protected static $defaultName = 'elastic:create-index';

    protected ElasticClientService $elasticClientService;
    protected SerializerInterface $serializer;
    protected string $indexName;

    public function __construct(ElasticClientService $elasticClientService, SerializerInterface $serializer)
    {
        $this->elasticClientService = $elasticClientService;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption(
                self::OPTIONINDEXNAME,
                null,
                InputOption::VALUE_OPTIONAL,
                'Name for the index to create',
                'documents'
            )
            ->addOption(
                self::CONFIG_FILE_OPTION,
                'c',
                InputOption::VALUE_OPTIONAL,
                'Path to the configuration file that should be used',
                './Resources/config/' . self::CONFIG_FILE
            )
            ->setDescription('Creates the given index by a provided configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $input->getOption returns different types, but I only want this as a string and prevent highlight errors
        $indexName = (string) $input->getOption(self::OPTIONINDEXNAME);
        $configFilePath = $input->getOption(self::CONFIG_FILE_OPTION);

        $elasticConfiguration = $this->loadConfiguration($output, $configFilePath);

        if (false === $elasticConfiguration) {
            return false;
        }

        $output->writeln('-----------------------------');

        foreach ($elasticConfiguration['indexes'] as $currentIndex => $documentConfiguration) {
            $indexSettings['mappings']['properties'] = $documentConfiguration;

            $output->writeln("currentIndex: " . $currentIndex);

            if ($this->indexAlreadyExists($currentIndex)) {
                $output->writeln("Index: '" . $currentIndex . "' exists");
                $output->writeln('-----------------------------');
                continue;
            }

            $result = $this->elasticClientService->getClient()->put(
                $currentIndex,
                [
                    'json' => $indexSettings,
                    'http_errors' => false,
                ],
            );

            $output->writeln($result->getStatusCode());
            $output->writeln($result->getBody()->getContents());
            $output->writeln('-----------------------------');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string|null $configFilePath
     * @return false|mixed
     */
    private function loadConfiguration(OutputInterface $output, ?string $configFilePath)
    {
        if (!file_exists($configFilePath)) {
            $output->writeln("<error>Configuration File '${configFilePath}' doesn't exist</error>");

            return false;
        }

        return Yaml::parseFile($configFilePath)['elastic'];
    }

    private function indexAlreadyExists(string $currentIndex): bool
    {
        $response = $this->elasticClientService->getClient()->head(
            $currentIndex,
            [
                'http_errors' => false,
            ]
        );

        return Response::HTTP_OK === $response->getStatusCode();
    }
}
