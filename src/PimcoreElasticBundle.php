<?php

namespace Flexibledeveloper\PimcoreElasticBundle;

use Flexibledeveloper\PimcoreElasticBundle\DependencyInjection\ElasticExtension;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use RuntimeException;


class PimcoreElasticBundle extends AbstractPimcoreBundle
{
    public function getContainerExtension()
    {
        return new ElasticExtension();
    }

    protected function getComposerPackageName(): string
    {
        $composer = file_get_contents(__DIR__ . '/../composer.json');
        if ($composer === false) {
            throw new RuntimeException();
        }

        return json_decode($composer)->name;
    }
}
