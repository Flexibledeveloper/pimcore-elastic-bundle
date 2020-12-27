<?php

namespace ElasticBundle\Command;

use ElasticBundle\Services\ElasticaQueryService;
use ElasticBundle\Services\ElasticDataService;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Document;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ElasticPopulateIndexCommand extends AbstractCommand
{
    protected const OPTIONINDEXNAME = 'indexName';

    protected static $defaultName = 'elastic:populate-pages';

    protected ElasticDataService $elasticDataService;
    protected SerializerInterface $serializer;
    protected string $indexName;

    public function __construct(ElasticDataService $elasticDataService, SerializerInterface $serializer)
    {
        $this->elasticDataService = $elasticDataService;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(self::OPTIONINDEXNAME, InputArgument::REQUIRED, InputOption::VALUE_OPTIONAL)
            ->setDescription('Populates the given index with appropriate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $input->getArgument returns different types, but I only want this as a string and prevent highlight errors
        $indexName = (string) $input->getArgument(self::OPTIONINDEXNAME);

        $listing = new Document\Listing();
        $listing->setCondition("type = 'page'");

        $output->writeln('Found: ' . $listing->getTotalCount(), OutputInterface::VERBOSITY_VERBOSE);

        $i = 0;
        foreach ($listing as $document) {
            $result = $this->loadDocumentAndPopulateToIndex($indexName, $document);
            if ($result) {
                $output->writeln('Populated: ' . $i . '/' . $listing->getTotalCount(), OutputInterface::VERBOSITY_VERBOSE);
            } else {
                $output->writeln('Skipped document with id: ' . $document->getId(), OutputInterface::VERBOSITY_VERBOSE);
            }

            $i++;
        }
    }

    public function loadDocumentAndPopulateToIndex(string $indexName, Document $document): bool
    {
        $normalizedPage = $this->serializer->normalize($document, 'documentPages');

        $response = $this->elasticDataService->createData(
            $indexName,
            $normalizedPage
        );

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return true;
        }

        return false;
    }
}
