<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyFactory;
use CouponURLs\Original\Construction\Dependency\DependencyInspectorFactory;
use CouponURLs\Original\Construction\Dependency\DependentFactory;
use CouponURLs\Original\Construction\Dependency\DynamicTypeFactory;
use CouponURLs\Original\Dependency\Abilities\DynamicType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DependencyInspector;
use CouponURLs\Original\Dependency\Dependent;
use CouponURLs\Tests\Unit\Original\Construction\Dependency\Fakes\SimpleDependencyFake;

it('creates a new Dependent from a Dependency object', function() {
    /** @var \Mockery\MockInterface&DependencyInspectorFactory */
    (object) $dependencyInspectoryFactory = Mockery::mock(DependencyInspectorFactory::class);
    /** @var \Mockery\MockInterface&DependencyInspector */
    (object) $dependencyInspector = Mockery::mock(DependencyInspector::class);
    /** @var \Mockery\MockInterface&DynamicTypeFactory */
    (object) $dynamicTypeFactory = Mockery::mock(DynamicTypeFactory::class);
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&DependentFactory */
    (object) $dependentFactory = Mockery::mock(DependentFactory::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $dependent = Mockery::mock(Dependent::class);

    (object) $dependencyFactory = new DependencyFactory(
        dependencyInspectorFactory: $dependencyInspectoryFactory,
        dependentFactory: $dependentFactory
    );
    (string) $type = 'the dependent dependency type';

    $dependencyInspectoryFactory->shouldReceive('create')->once()->with($type)->andReturn($dependencyInspector);

    $dependencyInspector->shouldReceive('hasDependencies')->once()->andReturn(true);

    $dependentFactory->shouldReceive('create')->once()->with($type)->andReturn($dependent);

    expect($dependencyFactory->create($type))->toBe($dependent);
});

it('creates a new Dependency', function() {
    /** @var \Mockery\MockInterface&DependencyInspectorFactory */
    (object) $dependencyInspectoryFactory = Mockery::mock(DependencyInspectorFactory::class);
    /** @var \Mockery\MockInterface&DependencyInspector */
    (object) $dependencyInspector = Mockery::mock(DependencyInspector::class);
    /** @var \Mockery\MockInterface&DynamicTypeFactory */
    (object) $dynamicTypeFactory = Mockery::mock(DynamicTypeFactory::class);
    /** @var \Mockery\MockInterface&DependentFactory */
    (object) $dependentFactory = Mockery::mock(DependentFactory::class);

    (object) $dependencyFactory = new DependencyFactory(
        dependencyInspectorFactory: $dependencyInspectoryFactory,
        dependentFactory: $dependentFactory
    );
    (string) $type = SimpleDependencyFake::class;
    (object) $dependency = new SimpleDependencyFake;

    $dependencyInspectoryFactory->shouldReceive('create')->once()->with($type)->andReturn($dependencyInspector);

    $dependencyInspector->shouldReceive('hasDependencies')->once()->andReturn(false);
    $dependencyInspector->shouldReceive('isDependency')->once()->andReturn(true);

    expect($dependencyFactory->create($type))
        ->toBeInstanceOf($type)
        ->toEqual(new $dependency);
});