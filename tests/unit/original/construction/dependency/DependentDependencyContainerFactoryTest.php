<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyContainerFactory;
use CouponURLs\Original\Construction\Dependency\DependencyInspectorFactory;
use CouponURLs\Original\Construction\Dependency\DependentDependencyContainerFactory;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DependencyInspector;
use CouponURLs\Original\Dependency\Dependent;
use CouponURLs\Original\Dependency\DependentDependencyContainer;

it('can only create when its an instance of Dependent', function() {
    /** @var \Mockery\MockInterface&DependencyInspectorFactory */
    (object) $dependencyInspectorFactory = Mockery::mock(DependencyInspectorFactory::class);
    /** @var \Mockery\MockInterface&DependencyInspector */
    (object) $dependencyInspector = Mockery::mock(DependencyInspector::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $dependent = Mockery::mock(Dependent::class);

    (object) $dependentDependencyContainerFactory = new DependentDependencyContainerFactory(
        $dependencyInspectorFactory,
        $dependencyContainerFactory
    );

    $dependencyInspectorFactory->shouldReceive('create')->once()->with($dependent::class)->andReturn($dependencyInspector);

    $dependencyInspector->shouldReceive('isDependent')->once()->andReturn(true);

    expect($dependentDependencyContainerFactory->canCreate($dependent))->toBeTrue();
});

it('cannot create when its not an instance of Dependent', function() {
    /** @var \Mockery\MockInterface&DependencyInspectorFactory */
    (object) $dependencyInspectorFactory = Mockery::mock(DependencyInspectorFactory::class);
    /** @var \Mockery\MockInterface&DependencyInspector */
    (object) $dependencyInspector = Mockery::mock(DependencyInspector::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);

    (object) $dependentDependencyContainerFactory = new DependentDependencyContainerFactory(
        $dependencyInspectorFactory,
        $dependencyContainerFactory
    );

    $dependencyInspectorFactory->shouldReceive('create')->once()->with($dependency::class)->andReturn($dependencyInspector);

    $dependencyInspector->shouldReceive('isDependent')->once()->andReturn(false);
    
    expect($dependentDependencyContainerFactory->canCreate($dependency))->toBeFalse();
});

it('creates a DependentDependencyContainer', function() {
   /** @var \Mockery\MockInterface&DependencyInspectorFactory */
    (object) $dependencyInspectorFactory = Mockery::mock(DependencyInspectorFactory::class);
    /** @var \Mockery\MockInterface&DependencyInspector */
    (object) $dependencyInspector = Mockery::mock(DependencyInspector::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $dependent = Mockery::mock(Dependent::class);

    (object) $dependentDependencyContainerFactory = new DependentDependencyContainerFactory(
        $dependencyInspectorFactory,
        $dependencyContainerFactory
    );

    expect($dependentDependencyContainerFactory->create($dependent))
        ->toBeInstanceOf(DependentDependencyContainer::class)
        ->toEqual(new DependentDependencyContainer($dependent, $dependencyContainerFactory));
});