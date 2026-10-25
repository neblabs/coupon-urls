<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\KnownContextFactory;
use CouponURLs\Original\Dependency\KnownContext;
use  function CouponURLs\Original\Utilities\_;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

use CouponURLs\Tests\Unit\Original\Construction\Dependency\Fakes\ReflectedClassFake;
use CouponURLs\Tests\Unit\Original\Construction\Fakes\ClassWithStringParameter;

it('can only create if its a class', function() {
    (object) $knownContextFactory = new KnownContextFactory;
    (object) $reflectionClass = new ReflectionClass(ReflectedClassFake::class);
    (object) $parameter = $reflectionClass->getConstructor()->getParameters()[0];

    expect($knownContextFactory->canCreate($parameter))->toBeTrue();
});

it('cannot create', function(mixed $parameter) {
    (object) $knownContextFactory = new KnownContextFactory;

    expect($knownContextFactory->canCreate($parameter))->toBeFalse();
})->with(a(
    parameterIsNull: a(
        parameter: null
    ),
    parameterIsTrue: a(
        parameter: true
    ),
    parameterIsNotTyped: a(
        parameter: (function() {
            (object) $reflectionClass = new ReflectionClass(ClassWithStringParameter::class);
            return $reflectionClass->getConstructor()->getParameters()[0];
        })()
    )
));

it('(integration!) creates a KnownContext from the ReflectionParameter of the argument of a class method', function() {
    (object) $knownContextFactory = new KnownContextFactory;
    (object) $reflectionClass = new ReflectionClass(ReflectedClassFake::class);
    (object) $firstParameterOfTheConstructor = $reflectionClass->getConstructor()->getParameters()[0];

    expect(
        $knownContextFactory->create(
            $firstParameterOfTheConstructor
        )
    )->toBeInstanceOf(KnownContext::class)
      ->toEqual(
            new KnownContext(
                fullyQualifiedTypeName: i(ReflectedClassFake::class),
                methodName: i('__construct'),
                variableName: i('theFakeName')
            )
      );
});