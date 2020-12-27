<?php

namespace Flexibledeveloper\PimcoreElasticBundle;

interface FilterServiceInterface
{
    public function getFilters(array $documentList, array $existingFilters, string $locale = null);
}
