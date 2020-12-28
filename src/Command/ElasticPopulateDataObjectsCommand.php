<?php

namespace ElasticBundle\Command;

use Flexibledeveloper\PimcoreElasticBundle\Services\ElasticaQueryService;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Course;
use Pimcore\Model\Document;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ElasticPopulateDataObjectsCommand extends AbstractCommand
{
    protected const OPTIONINDEXNAME = 'indexName';
    protected const OPTIONDATAOBJECTCLASS = 'dataObjectClass';

    protected static $defaultName = 'elastic:populate-dataobjects';

    protected ElasticaQueryService $elasticDataService;
    protected SerializerInterface $serializer;
    protected string $indexName;

    public function __construct(ElasticaQueryService $elasticDataService, SerializerInterface $serializer)
    {
        $this->elasticDataService = $elasticDataService;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(self::OPTIONINDEXNAME, InputArgument::REQUIRED, 'Index name to populate into')
            ->addArgument(self::OPTIONDATAOBJECTCLASS, InputArgument::REQUIRED, 'DataObjectClass to load into the index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $input->getArgument returns different types, but I only want this as a string and prevent highlight errors
        $indexName = (string) $input->getArgument(self::OPTIONINDEXNAME);
        $dataObjectClass = (string) $input->getArgument(self::OPTIONDATAOBJECTCLASS);
        $dataObjectListing = sprintf('%1\Listing()', $dataObjectClass);

        $dataObjectListing = new $dataObjectListing;

        $output->writeln('Found: ' . $dataObjectListing->getTotalCount(), OutputInterface::VERBOSITY_VERBOSE);

        $i = 0;
        foreach ($dataObjectListing as $document) {
            $result = $this->loadDocumentAndPopulateToIndex($indexName, $document);
            if ($result) {
                $output->writeln('Populated: ' . $i . '/' . $dataObjectListing->getTotalCount(), OutputInterface::VERBOSITY_VERBOSE);
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
