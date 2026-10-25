<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\UnknownContext;
use CouponURLs\Original\Dependency\UnresolvableDependencyContainer;

it('matches() always returns true', function() {
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $unresolvableDependencyContainer = new UnresolvableDependencyContainer;

    expect($unresolvableDependencyContainer->matches('the type', $context))->toBeTrue();
});

it('throws exception when trying to get the type', function() {
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    (object) $unresolvableDependencyContainer = new UnresolvableDependencyContainer;

    $unresolvableDependencyContainer->setDependenciesContainer($dependenciesContainer);
    $dependenciesContainer->shouldReceive('currentDependencyStack')->atLeast()->once()->andReturn(new Collection([]));
    $unresolvableDependencyContainer->matches('the type', Mockery::mock(UnknownContext::class));
    
    expect(fn() => $unresolvableDependencyContainer->get('the type'))
        ->toThrow(
            Exception::class,
            exceptionMessage: "Could not resolve dependency: the type."
        );
});
