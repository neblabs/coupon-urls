<?php

namespace CouponURLs\Original\Tests;

use CouponURLs\Original\Environment\Env;

Trait DataProviderLoader
{
    protected function getDataFromSet(string $__DIR__, string $datasetFileName, string $dataSetKey)
    {
        return (require "{$__DIR__}/dataSets/{$datasetFileName}")[$dataSetKey];
    }
}