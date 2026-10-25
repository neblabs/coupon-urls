<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyDynamicTypeFactory;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DynamicTypeFromStaticType;

it('can only create when it implements a Dependency', function() {
    (object) $dependencyDynamicTypeFactory = new DependencyDynamicTypeFactory;
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);

    expect($dependencyDynamicTypeFactory->canCreate($dependency::class))->toBeTrue();
    expect($dependencyDynamicTypeFactory->canCreate(stdClass::class))->toBeFalse();
});

it('creates a DynamicTypeFromStaticType instance', function() {
    (object) $dependencyDynamicTypeFactory = new DependencyDynamicTypeFactory;
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);

    expect($dependencyDynamicTypeFactory->create($dependency::class))
        ->toBeInstanceOf(DynamicTypeFromStaticType::class)
        ->toEqual(new DynamicTypeFromStaticType($dependency::class));
});