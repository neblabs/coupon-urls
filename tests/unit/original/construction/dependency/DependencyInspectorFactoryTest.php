<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyInspectorFactory;
use CouponURLs\Original\Dependency\DependencyInspector;

it('creats a dependency inspectr from a factory class', function() {
    (object) $dependencyInspectorFactory = new DependencyInspectorFactory;

    expect($dependencyInspectorFactory->create('MyDependencyClassName'))
        ->toBeInstanceOf(DependencyInspector::class)
        ->toEqual(new DependencyInspector('MyDependencyClassName'));
});
