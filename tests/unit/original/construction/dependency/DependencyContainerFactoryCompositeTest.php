<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\ContainerFactory;
use CouponURLs\Original\Construction\Dependency\DependencyContainerFactories;
use CouponURLs\Original\Construction\Dependency\DependencyContainerFactoryComposite;
use CouponURLs\Original\Construction\FactoryOverloader;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DependencyContainer;

it('creates a DependencyContainer from a Dependency from the given DependencyFactory instances', function() {
    /** @var \Mockery\MockInterface&FactoryOverloader */
    (object) $factoryOverloader = Mockery::mock(FactoryOverloader::class);
    /** @var \Mockery\MockInterface&ContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(ContainerFactory::class);
    (object) $dependency = 'MyDependencyClassName';
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer = Mockery::mock(DependencyContainer::class);

    (object) $depenecyContainerFactories = new DependencyContainerFactoryComposite(
        $factoryOverloader
    );

    $factoryOverloader->shouldReceive('overload')->once()->with($dependency)->andReturn($dependencyContainerFactory);

    $dependencyContainerFactory->shouldReceive('create')->once()->with($dependency)->andReturn($dependencyContainer);
    
    expect($depenecyContainerFactories->create($dependency))->toBe($dependencyContainer);
});
