<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependentFactory;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\AutomaticDependencyContainer;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Dependent;

use function CouponURLs\Original\Utilities\Collection\o;

it('matches when the dependent matches (when class Exists), this should be unique for different types using in the same instance', function() {
    /** @var \Mockery\MockInterface&DependentFactory */
    (object) $dependentFactory = Mockery::mock(DependentFactory::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $firstDependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $secondDependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $automaticDependencyContainer = new AutomaticDependencyContainer($dependentFactory);
    (string) $firstType = stdClass::class;
    (string) $secondType = ArrayAccess::class; //interface, not instatiatable

    $dependentFactory->shouldReceive('create')->once()->with($firstType)->andReturn($firstDependent);
    $dependentFactory->shouldReceive('create')->never()->with($secondType)->andReturn($secondDependent);

    $firstDependent->shouldReceive('canBeCreated')->once()->andReturn(true);

    $secondDependent->shouldReceive('canBeCreated')->never()->andReturn(false);

    expect($automaticDependencyContainer->matches($firstType, $context))->toBeTrue();
    expect($automaticDependencyContainer->matches($secondType, $context))->toBeFalse();
});

it('gets the object from the dependent', function() {
    /** @var \Mockery\MockInterface&DependentFactory */
    (object) $dependentFactory = Mockery::mock(DependentFactory::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $firstDependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&Dependent */
    (object) $secondDependent = Mockery::mock(Dependent::class);
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);

    (object) $automaticDependencyContainer = new AutomaticDependencyContainer($dependentFactory);
    (string) $theFirstType = 'the first type';
    (object) $firstDependencyObject = o(id: 10);
    (string) $theSecondType = 'the second type';
    (object) $secondDependencyObject = o(name: 'rafa');

    $dependentFactory->shouldReceive('create')->once()->with($theFirstType)->andReturn($firstDependent);
    $dependentFactory->shouldReceive('create')->once()->with($theSecondType)->andReturn($secondDependent);

    $firstDependent->shouldReceive('setDependenciesContainer')->once()->with($dependenciesContainer);
    $firstDependent->shouldReceive('create')->once()->andReturn($firstDependencyObject);

    $secondDependent->shouldReceive('setDependenciesContainer')->once()->with($dependenciesContainer);
    $secondDependent->shouldReceive('create')->once()->andReturn($secondDependencyObject);

    $automaticDependencyContainer->setDependenciesContainer($dependenciesContainer);

    expect($automaticDependencyContainer->get($theFirstType))->toBe($firstDependencyObject);
    expect($automaticDependencyContainer->get($theSecondType))->toBe($secondDependencyObject);
});