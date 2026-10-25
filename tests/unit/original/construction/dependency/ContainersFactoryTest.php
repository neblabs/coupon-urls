<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\CreatableContainers;
use CouponURLs\Original\Construction\Dependency\ContainersFactory;
use CouponURLs\Original\Dependency\Container;

use function CouponURLs\Original\Utilities\Collection\_;

it('creates containers from the Factories and returns them as a single collection', function() {
    /** @var \Mockery\MockInterface&CreatableContainers */
    (object) $dependencyContainersFactory = Mockery::mock(CreatableContainers::class);
    /** @var \Mockery\MockInterface&CreatableContainers */
    (object) $anotherContainersFactory = Mockery::mock(CreatableContainers::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $container1 = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $container2 = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $container3 = Mockery::mock(Container::class);

    (object) $firstContainers = _(
        $container1,
        $container2
    );

    (object) $secondContainers = _(
        $container3,
    );

    (object) $containersFactory = new ContainersFactory(_(
        $dependencyContainersFactory,
        $anotherContainersFactory
    ));

    $dependencyContainersFactory->shouldReceive('create')->once()->andReturn($firstContainers);
    $anotherContainersFactory->shouldReceive('create')->once()->andReturn($secondContainers);

    expect($containersFactory->create())->toEqual(_(
        $container1,
        $container2,
        $container3
    ));
});
