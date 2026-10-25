<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyContainerFactory;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DependencyContainer;
use CouponURLs\Original\Dependency\Dependent;
use CouponURLs\Original\Dependency\DependentDependencyContainer;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\extendsStdClassFake;

use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Collection\o;

it('uses the Dependent to create a Dependency that will be contained in a DependencyContainer. Only once per instance.', function() {
    /** @var \Mockery\MockInterface&Dependent */
    (object) $dependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer = Mockery::mock(DependencyContainer::class);
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $dependencyObject = o(id: 10);
    (object) $dependentDependencyContainer = new DependentDependencyContainer(
        dependent: $dependent,
        dependencyContainerFactory: $dependencyContainerFactory
    );

    $dependent->shouldReceive('setDependenciesContainer')->once()->with($dependenciesContainer);
    $dependent->shouldReceive('create')->once()->andReturn($dependency);

    $dependencyContainerFactory->shouldReceive('create')->once()->with($dependency)->andReturn($dependencyContainer);

    $dependencyContainer->shouldReceive('matches')->times(3)->with(stdClass::class, $context)->andReturn(true);
    $dependencyContainer->shouldReceive('get')->times(3)->andReturn($dependencyObject);

    $dependentDependencyContainer->setDependenciesContainer($dependenciesContainer);

    $dependent->shouldReceive('type')->andReturn(stdClass::class);

    // the same instance shall be used no mather hoe many times we call the public methods
    expect($dependentDependencyContainer->matches(stdClass::class, $context))->toBeTrue();
    expect($dependentDependencyContainer->matches(stdClass::class, $context))->toBeTrue();
    expect($dependentDependencyContainer->matches(stdClass::class, $context))->toBeTrue();

    // the same instance shall be used no mather hoe many times we call the public methods
    expect($dependentDependencyContainer->get(stdClass::class, $context))->toBe($dependencyObject);
    expect($dependentDependencyContainer->get(stdClass::class, $context))->toBe($dependencyObject);
    expect($dependentDependencyContainer->get(stdClass::class, $context))->toBe($dependencyObject);
});

it('matches both same type and descendants.', function(string $dependentType, string $typeToCheck) {
    /** @var \Mockery\MockInterface&Dependent */
    (object) $dependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer = Mockery::mock(DependencyContainer::class);
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $dependencyObject = o(id: 10);
    (object) $dependentDependencyContainer = new DependentDependencyContainer(
        dependent: $dependent,
        dependencyContainerFactory: $dependencyContainerFactory
    );

    $dependent->shouldReceive('create')->once()->andReturn($dependency);
    $dependent->shouldReceive('type')->andReturn($dependentType);

    $dependencyContainerFactory->shouldReceive('create')->once()->with($dependency)->andReturn($dependencyContainer);

    $dependencyContainer->shouldReceive('matches')->with($typeToCheck, $context)->andReturn(true);
    // the same instance shall be used no mather hoe many times we call the public methods
    expect($dependentDependencyContainer->matches($typeToCheck, $context))->toBeTrue();
})->with(fn() => a(
    sameClass: a(
        dependentType: stdClass::class,
        typeToCheck: stdClass::class
    ),
    extendingClass: a(
        dependentType: stdClass::class,
        typeToCheck: extendsStdClassFake::class
    )
));
//WE NEED TO TEST THEM DEPENDENT!