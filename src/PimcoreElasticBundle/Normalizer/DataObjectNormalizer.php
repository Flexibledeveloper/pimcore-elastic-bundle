<?php

namespace Flexibledeveloper\PimcoreElasticBundle\Normalizer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Pimcore\Model\DataObject\News;

class DataObjectNormalizer implements ContextAwareNormalizerInterface
{
    private ContainerInterface $params;

    /**
     * NewsObjectNormalizer constructor.
     * @param ContainerInterface $params
     */
    public function __construct(ContainerInterface $params)
    {
        $this->params = $params;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return 'news' === $format && $data instanceof News;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $indexName = sprintf('elastic.indexes.%s', $context['index']);
        $methodMappings = $this->params->getParameter($indexName)['methodMappings'];
        $documentFields = $this->params->getParameter($indexName)['document'];

        $document = [];
        foreach ($documentFields as $fieldName => $configuration) {
            if (!$this->hasIndexConfigMappingField($methodMappings, $fieldName)) {
                continue;
            }

            $function = $methodMappings[$fieldName];

            if (is_array($function)) {
                $document[$fieldName] = $this->handleFunctionDefinitionWithParams($function, $object, $context);
            }

            if (is_string($function)) {
                try {
                    $document[$fieldName] = $object->$function($context['language']);
                } catch (\Exception $e) {
                    throw new \Exception($e);
                }
            }
        }
        $document['language'] = $context['language'];

        return $document;
    }

    private function hasIndexConfigMappingField(array $methodMappings, string $fieldName): bool
    {
        return array_key_exists($fieldName, $methodMappings);
    }

    /**
     * @param array $function
     * @param News $object
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    private function handleFunctionDefinitionWithParams(array $function, News $object, array $context)
    {
        $functionValue = '';

        $functionName = $function['method'];
        $functionParams = $function['params'];
        $variableStrings = '';

        foreach ($functionParams as $paramName) {
            $variableStrings .= $context[$paramName].',';
        }

        $variableStrings = substr($variableStrings, 0, strlen($variableStrings)-1);

        if ('string' !== gettype($functionName)) {
            throw new \Exception(gettype($functionName). ':'. $functionName);
        }

        try {
            $functionValue = $object->$functionName($variableStrings);
        } catch (\Exception $e) {
            throw new \Exception(print_r($functionName, true));
        }

        return $functionValue;
    }
}
