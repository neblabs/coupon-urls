<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\CachedInstanceDependencyContainer;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\BaseClassFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\BaseInterfaceFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ExactSameClassFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ExtendedClassFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ImplementedInterfaceFake;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Collection\o;

it('creates the instance once using the Dependency and returns the same instance on every call', function() {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);

    (object) $cachedInstanceDependencyContainer = new CachedInstanceDependencyContainer($dependency);
    (object) $originalDependencyObject = o(id: 10);
    (string) $type = 'the type';
    $dependency->shouldReceive('create')->once()->andReturn($originalDependencyObject);

    (object) $dependencyObject1 = $cachedInstanceDependencyContainer->get($type);
    (object) $dependencyObject2 = $cachedInstanceDependencyContainer->get($type);
    (object) $dependencyObject3 = $cachedInstanceDependencyContainer->get($type);

    expect($cachedInstanceDependencyContainer->get($type))->toBe($originalDependencyObject);
    expect($dependencyObject1)->toBe($originalDependencyObject);
    expect($dependencyObject2)->toBe($originalDependencyObject);
    expect($dependencyObject3)->toBe($originalDependencyObject);
});

it('fails to match when type matches but canBeCreated() is false', function(){
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new CachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn(BaseClassFake::class);
    $dependency->shouldReceive('canBeCreated')->once()->with($context)->andReturn(false);

    expect($cachedInstanceDependencyContainer->matches(BaseClassFake::class, $context))->toBeFalse();
});
it('fails to match when types dont match but canBeCreated() is true', function(){
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new CachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn(stdClass::class);
    $dependency->shouldReceive('canBeCreated')->zeroOrMoreTimes()->with($context)->andReturn(true);

    expect($cachedInstanceDependencyContainer->matches(BaseClassFake::class, $context))->toBeFalse();
});
// type matches, but canBeCreated doesnt, 
// type doesnt match, but canBeCreated() does

it('matches when the type matches and Dependency::canBeCreated() is true', function(string $expectedTypeFromDependency, string $actualType) {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new CachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn($expectedTypeFromDependency);
    $dependency->shouldReceive('canBeCreated')->once()->with($context)->andReturn(true);

    expect($cachedInstanceDependencyContainer->matches($actualType, $context))->toBeTrue();
})->with(a(
    exactType: a(
        expectedTypeFromDependency: BaseClassFake::class,
        typeIsTheExactSameClass: BaseClassFake::class
    ),
    dependencyExpectsAParentClassType: a(
        expectedTypeFromDependency: BaseClassFake::class,
        typeIsAClassThatExtendsTheBaseClass: ExtendedClassFake::class
    ),
    dependencyExpectsAnInterface: a(
        expectedTypeFromDependency: BaseInterfaceFake::class,
        typeIsAClassThatExtendsTheBaseClass: ImplementedInterfaceFake::class
    ),
));