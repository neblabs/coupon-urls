<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\DependenciesContainerContainer;

it('matches when the type is DependenciesContainer', function() {
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $dependenciesContainerContainer = new DependenciesContainerContainer;

    $dependenciesContainerContainer->setDependenciesContainer($dependenciesContainer);

    expect($dependenciesContainerContainer->matches(DependenciesContainer::class, $context))
        ->toBeTrue();
});

it('doesnt match when given literally any other type', function() {
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $dependenciesContainerContainer = new DependenciesContainerContainer;

    $dependenciesContainerContainer->setDependenciesContainer($dependenciesContainer);

    expect($dependenciesContainerContainer->matches(stdClass::class, $context))->toBeFalse();
});

it('gets the DependenciesContainer that was previously set', function() {
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $dependenciesContainerContainer = new DependenciesContainerContainer;

    $dependenciesContainerContainer->setDependenciesContainer($dependenciesContainer);

    expect($dependenciesContainerContainer->get(DependenciesContainer::class, $context))->toBe($dependenciesContainer);
});