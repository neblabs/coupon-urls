<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\UnCachedInstanceDependencyContainer;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\BaseClassFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\BaseInterfaceFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ExtendedClassFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ImplementedInterfaceFake;

use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Collection\o;

it('creates a new instance every time using the Dependency', function() {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);

    (object) $cachedInstanceDependencyContainer = new UnCachedInstanceDependencyContainer($dependency);

    (string) $type = 'the type';

    (object) $newDependencyObject1 = o(id: 10);
    (object) $newDependencyObject2 = o(id: 10);
    (object) $newDependencyObject3 = o(id: 10);

    $dependency->shouldReceive('create')->times(3)->andReturn(
        $newDependencyObject1,
        $newDependencyObject2,
        $newDependencyObject3
    );

    (object) $dependencyObject1 = $cachedInstanceDependencyContainer->get($type);
    (object) $dependencyObject2 = $cachedInstanceDependencyContainer->get($type);
    (object) $dependencyObject3 = $cachedInstanceDependencyContainer->get($type);

    expect($dependencyObject1)->toBe($newDependencyObject1);
    expect($dependencyObject2)->toBe($newDependencyObject2);
    expect($dependencyObject3)->toBe($newDependencyObject3);

    expect($dependencyObject1)->not()->toBe($newDependencyObject2);
    expect($dependencyObject1)->not()->toBe($newDependencyObject3);

    expect($dependencyObject2)->not()->toBe($newDependencyObject1);
    expect($dependencyObject2)->not()->toBe($newDependencyObject3);

    expect($dependencyObject3)->not()->toBe($newDependencyObject1);
    expect($dependencyObject3)->not()->toBe($newDependencyObject2);

});

it('fails to match when type matches but canBeCreated() is false', function(){
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new UnCachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn(BaseClassFake::class);
    $dependency->shouldReceive('canBeCreated')->once()->andReturn(false);

    expect($cachedInstanceDependencyContainer->matches(BaseClassFake::class, $context))
        ->toBeFalse();
});
it('fails to match when types dont match but canBeCreated() is true', function(){
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new UnCachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn(stdClass::class);
    $dependency->shouldReceive('canBeCreated')->zeroOrMoreTimes()->andReturn(true);

    expect($cachedInstanceDependencyContainer->matches(BaseClassFake::class, $context))
        ->toBeFalse();
});

it('matches when the type matches and Dependency::canBeCreated() is true', function(string $expectedTypeFromDependency, string $actualType) {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, StaticType::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    (object) $cachedInstanceDependencyContainer = new UnCachedInstanceDependencyContainer($dependency);

    $dependency->shouldReceive('type')->once()->andReturn($expectedTypeFromDependency);
    $dependency->shouldReceive('canBeCreated')->once()->andReturn(true);

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