<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\ContainerFactory;
use CouponURLs\Original\Construction\Abilities\CreatableContainers;
use CouponURLs\Original\Construction\Dependency\DependencyContainerFactories;
use CouponURLs\Original\Construction\Dependency\DependencyContainerFactoryComposite;
use CouponURLs\Original\Construction\Dependency\DependencyContainersFactory;
use CouponURLs\Original\Dependency\Container;
use CouponURLs\Original\Dependency\DependencyContainer;

use function CouponURLs\Original\Utilities\Collection\_;

it('uses a DependencyContainerFactories to create a Container for every dependency type', function() {
    (object) $dependencyTypes = _('dependencyClassName1', 'dependencyClassName2', 'dependencyClassName3');
    /** @var \Mockery\MockInterface&ContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(ContainerFactory::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $dependencyContainer1 = Mockery::mock(DependencyContainer::class);
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer2 = Mockery::mock(DependencyContainer::class);
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer3 = Mockery::mock(DependencyContainer::class);

    (object) $dependencyContainersFactory = new DependencyContainersFactory(
        dependencyTypes: $dependencyTypes,
        dependencyContainerFactory: $dependencyContainerFactory
    );

    $dependencyContainerFactory->shouldReceive('create')->once()->withSomeOfArgs('dependencyClassName1')->andReturn($dependencyContainer1);
    $dependencyContainerFactory->shouldReceive('create')->once()->withSomeOfArgs('dependencyClassName2')->andReturn($dependencyContainer2);
    $dependencyContainerFactory->shouldReceive('create')->once()->withSomeOfArgs('dependencyClassName3')->andReturn($dependencyContainer3);

    expect($dependencyContainersFactory->create()->asArray())->toBe([
        $dependencyContainer1,
        $dependencyContainer2,
        $dependencyContainer3
    ]);
});