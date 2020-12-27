<?php

namespace Flexibledeveloper\PimcoreElasticBundle;

use ElasticBundle\DependencyInjection\ElasticExtension;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class PimcoreElasticBundle extends AbstractPimcoreBundle
{
    public function getContainerExtension()
    {
        return new ElasticExtension();
    }
}
