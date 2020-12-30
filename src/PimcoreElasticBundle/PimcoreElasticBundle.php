<?php

namespace Flexibledeveloper\PimcoreElasticBundle;

use Flexibledeveloper\PimcoreElasticBundle\DependencyInjection\PimcoreElasticExtension;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class PimcoreElasticBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME='flexibledeveloper/pimcore-elastic-bundle';

    public function getContainerExtension()
    {
        return new PimcoreElasticExtension();
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
