<?php

namespace Flexibledeveloper\PimcoreElasticBundle\EventSubscriber;

use Flexibledeveloper\PimcoreElasticBundle\Services\DocumentPopulatorService;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\ElementEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataObjectUpdateSubscriber implements EventSubscriberInterface
{
    private const INDEXNAME = 'news';
    private const DATAOBJECTTYPECLASSNAME = 'news';

    private DocumentPopulatorService $documentPopulatorService;

    public function __construct(DocumentPopulatorService $documentPopulatorService)
    {
        $this->documentPopulatorService = $documentPopulatorService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_UPDATE => 'updateElasticIndexDocument',
        ];
    }

    public function updateElasticIndexDocument(ElementEventInterface $elementEvent)
    {
        if ($elementEvent->getElement() instanceof \Pimcore\Model\DataObject\News) {
            $element = $elementEvent->getElement();
        }
    }
}
