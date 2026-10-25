<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\ContextFactory;
use CouponURLs\Original\Construction\Dependency\DependentFactory;
use CouponURLs\Original\Construction\Dependency\DynamicTypeFactory;
use CouponURLs\Original\Construction\Dependency\KnownContextFactory;
use CouponURLs\Original\Dependency\Abilities\DynamicType;
use CouponURLs\Original\Dependency\Dependent;

it('creates a dependent from the given type', function() {
    /** @var \Mockery\MockInterface&DynamicTypeFactory */
    (object) $dynamicTypeFactory = Mockery::mock(DynamicTypeFactory::class);
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);

    (object) $dependentFactory = new DependentFactory(
        dynamicTypeFactory: $dynamicTypeFactory,
        contextFactory: $ContextFactory
    );

    $dynamicTypeFactory->shouldReceive('create')->once()->with(stdClass::class)->andReturn($dynamicType);

    expect($dependentFactory->create(stdClass::class))
        ->toBeInstanceOf(Dependent::class)
        ->toEqual(new Dependent(
            $dynamicType, $ContextFactory
        ));
});
