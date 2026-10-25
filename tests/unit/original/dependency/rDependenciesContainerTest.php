<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\ContextFactory;
use CouponURLs\Original\Construction\Dependency\ContainersFactory;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Container;
use CouponURLs\Original\Dependency\DependenciesContainer;
use function CouponURLs\Original\Utilities\Collection\{_, o};

//an actual context instance passed to the fatcory
it('resolves a simple container, passes the context to matches(), null passed to the factory', function() {
    /** @var \Mockery\MockInterface&ContainersFactory */
    (object) $containersFactory = Mockery::mock(ContainersFactory::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $contextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    /** @var \Mockery\MockInterface&Container */
    (object) $firstContainer = Mockery::mock(Container::class);

    (string) $typeToGet = 'the type to get';
    (object) $resolvedObject = o(id: 20);
    (object) $containers = _(
        $firstContainer,
    );

    $containersFactory->shouldReceive('create')->once()->andReturn(
        $containers
    );

    $firstContainer->shouldReceive('setDependenciesContainer')->once();

    (object) $dependenciesContainer = new DependenciesContainer(
        $containersFactory, $contextFactory
    );

    $firstContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );

    $contextFactory->shouldReceive('create')->atLeast()->once()->with(null)->andReturn($context);

    $firstContainer->shouldReceive('matches')->once()->with($typeToGet, $context)->andReturnTrue();

    $firstContainer->shouldReceive('get')->once()->with($typeToGet)->andReturn($resolvedObject);

    expect($dependenciesContainer->get($typeToGet))->toBe($resolvedObject);
});

it('resolves a simple container, passes the context to matches(), the factory returns the passed context instance', function() {
    /** @var \Mockery\MockInterface&ContainersFactory */
    (object) $containersFactory = Mockery::mock(ContainersFactory::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $contextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $existingContext = Mockery::mock(Context::class);

    /** @var \Mockery\MockInterface&Container */
    (object) $firstContainer = Mockery::mock(Container::class);

    (string) $typeToGet = 'the type to get';
    (object) $resolvedObject = o(id: 20);
    (object) $containers = _(
        $firstContainer,
    );

    $containersFactory->shouldReceive('create')->once()->andReturn(
        $containers
    );

    $firstContainer->shouldReceive('setDependenciesContainer')->once();

    (object) $dependenciesContainer = new DependenciesContainer(
        $containersFactory, $contextFactory
    );

    $firstContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );

    $contextFactory->shouldReceive('create')->atLeast()->once()->with($existingContext)->andReturn($existingContext);

    $firstContainer->shouldReceive('matches')->once()->with($typeToGet, $existingContext)->andReturnTrue();

    $firstContainer->shouldReceive('get')->once()->with($typeToGet)->andReturn($resolvedObject);

    expect(
        $dependenciesContainer->get(
            type: $typeToGet,
            context: $existingContext
        )
    )->toBe($resolvedObject);
});

it('gets the dependency object for the first container that matches. Uses the same container instances on subsequent calls.', function() {
    /** @var \Mockery\MockInterface&ContainersFactory */
    (object) $containersFactory = Mockery::mock(ContainersFactory::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $contextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    /** @var \Mockery\MockInterface&Container */
    (object) $firstContainer = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $secondContainer = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $thirdContainer = Mockery::mock(Container::class);

    (string) $typeToGet = 'the type to get';
    (object) $resolvedObject = o(id: 20);
    (object) $resolvedObjectASecondTime = o(id: 89);
    (object) $containers = _(
        $firstContainer,
        $secondContainer,
        $thirdContainer,
    );

    $containersFactory->shouldReceive('create')->once()->andReturn($containers);

    $firstContainer->shouldReceive('setDependenciesContainer')->once();
    $secondContainer->shouldReceive('setDependenciesContainer')->once();
    $thirdContainer->shouldReceive('setDependenciesContainer')->once();

    (object) $dependenciesContainer = new DependenciesContainer(
        $containersFactory, $contextFactory
    );

    $firstContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );
    $secondContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );
    $thirdContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );

    $contextFactory->shouldReceive('create')->atLeast()->once()->with(null)->andReturn($context);

    $firstContainer->shouldReceive('matches')->twice()->with($typeToGet, $context)->andReturn(false);
    $secondContainer->shouldReceive('matches')->twice()->with($typeToGet, $context)->andReturn(true);
    $thirdContainer->shouldReceive('matches')->never()->with($typeToGet, $context)->andReturn(false);

    $firstContainer->shouldReceive('get')->never()->with($typeToGet);
    $secondContainer->shouldReceive('get')->once()->with($typeToGet)->andReturn($resolvedObject);
    $secondContainer->shouldReceive('get')->once()->with($typeToGet)->andReturn($resolvedObjectASecondTime);
    $thirdContainer->shouldReceive('get')->never()->with($typeToGet);

    expect($dependenciesContainer->get($typeToGet))->toBe($resolvedObject);
    // once again, the same Container instances should be used,
    // DOESNT MEAN THE SAME OBJECT WILL BE RETURNED. IT IS *NOT* FOR THE DependenciesContainer
    // TO CACHE THE RESULTS, IT IS UP TO EACH INDIVIDUAL Container instance.
    // THE ONLY RESPONSIBILITY OF THE DependenciesContainer IS TO MAKE SURE THAT WE 
    // HAVE THE SAME Containers per instance so as to enable the individual Container instances
    // cache the results if need be.
    expect($dependenciesContainer->get($typeToGet))->toBe($resolvedObjectASecondTime);
});

it('the second container calls the main DependenciesContainer again (recursively), accessing the third container (the container after the second container) and use it to produce the final result. The context passed recursively should be used by the DependenciesContainer', function() {
    /** @var \Mockery\MockInterface&ContainersFactory */
    (object) $containersFactory = Mockery::mock(ContainersFactory::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $contextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $ContextFromDependent = Mockery::mock(Context::class);

    /** @var \Mockery\MockInterface&Container */
    (object) $firstContainer = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $secondContainer = Mockery::mock(Container::class);
    /** @var \Mockery\MockInterface&Container */
    (object) $thirdContainer = Mockery::mock(Container::class);

    (string) $typeToGet = 'the type to get';
    (string) $innerTypeToGet = 'The type to get from inside the second container!';
    (object) $resolvedObject = o(id: 20);
    (object) $innerUser = o(name: 'rafa!');
    (object) $containers = _(
        $firstContainer,
        $secondContainer,
        $thirdContainer,
    );

    $containersFactory->shouldReceive('create')->once()->andReturn($containers);

    $firstContainer->shouldReceive('setDependenciesContainer')->once();
    $secondContainer->shouldReceive('setDependenciesContainer')->once();
    $thirdContainer->shouldReceive('setDependenciesContainer')->once();

    (object) $dependenciesContainer = new DependenciesContainer(
        $containersFactory, $contextFactory
    );

    $firstContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );
    $secondContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );
    $thirdContainer->shouldHaveReceived(
        'setDependenciesContainer', args: [$dependenciesContainer]
    );

    $contextFactory->shouldReceive('create')->once()->with(null)->andReturn($context);
    $contextFactory->shouldReceive('create')->once()->with($ContextFromDependent)->andReturn($ContextFromDependent);

    $firstContainer->shouldReceive('matches')->once()->with($typeToGet, $context)->andReturn(false)->globally()->ordered();
    $secondContainer->shouldReceive('matches')->once()->with($typeToGet, $context)->andReturn(true)->globally()->ordered();
    $thirdContainer->shouldReceive('matches')->never()->with($typeToGet, $context)->andReturn(false)->globally()->ordered();

    $firstContainer->shouldReceive('get')->never()->with($typeToGet)->globally()->ordered();
    $secondContainer->shouldReceive('get')->once()->with($typeToGet)->andReturnUsing(
        fn() => o(
            id: 10, 
            user: $dependenciesContainer->get(
                type: $innerTypeToGet, 
                context: $ContextFromDependent
            )
        )
    )->globally()->ordered();

    $firstContainer->shouldReceive('matches')->once()->with($innerTypeToGet, $ContextFromDependent)->andReturn(false)->globally()->ordered();
    $secondContainer->shouldReceive('matches')->once()->with($innerTypeToGet, $ContextFromDependent)->andReturn(false)->globally()->ordered();
    $thirdContainer->shouldReceive('matches')->once()->with($innerTypeToGet, $ContextFromDependent)->andReturn(true)->globally()->ordered();

    $thirdContainer->shouldReceive('get')->once()->with($innerTypeToGet)->andReturn($innerUser)->globally()->ordered();
    $thirdContainer->shouldReceive('get')->never()->with($typeToGet, $ContextFromDependent)->globally()->ordered();

    (object) $resolvedObject = $dependenciesContainer->get($typeToGet);

    expect($resolvedObject)->toEqual(
        o(id: 10, user: $innerUser)
    );
    expect($resolvedObject->user)->toBe($innerUser);
});

// then lets do an integration test wit the final composite objects