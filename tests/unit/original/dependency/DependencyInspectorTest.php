<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\DependencyInspector;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassThatImplementsTheInterface;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithAnEmptyConstructorFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithNoConstrutorFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\DependentFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\InterfaceExtendingDependencyFake;

use function CouponURLs\Original\Utilities\Collection\a;

it('returns true when string implements The Dependency inerface', function(string $dependencyType) {
    (object) $dependencyInspector = new DependencyInspector($dependencyType);

    expect($dependencyInspector->isDependency())->toBeTrue();
})->with(a(
    interfaceThatImplementsTheInterface: InterfaceExtendingDependencyFake::class,
    classThatImplementsTheInterface: ClassThatImplementsTheInterface::class
));

it('returns false when string does not implement the Dependency inerface', function(string $dependencyType) {
    (object) $dependencyInspector = new DependencyInspector($dependencyType);

    expect($dependencyInspector->isDependency())->toBeFalse();
})->with(a(
    notAClass: 'i dont exist',
    classThatDoesntImnplementTheInterface: \stdClass::class,
));

it('returns true when string has a constructor with one or more arguemtns', function(string $dependencyType) {
    (object) $dependencyInspector = new DependencyInspector($dependencyType);

    expect($dependencyInspector->hasDependencies())->toBeTrue();
    expect($dependencyInspector->isDependent())->toBeFalse();
})->with(a(
    classWithOneArgument: ClassWithOneArgumentFake::class,
));

it('returns false when string is not a class With a constructor with one or more arguemnts', function(string $dependencyType) {
    (object) $dependencyInspector = new DependencyInspector($dependencyType);

    expect($dependencyInspector->hasDependencies())->toBeFalse();
})->with(a(
    notAClass: 'i dont exist!',
    classWithNoConstructor: ClassWithNoConstrutorFake::class,
    classWithAnEmptyConstructor: ClassWithAnEmptyConstructorFake::class
));

it('returns true when isDependent and is an instance of Dependent', function() {
    (object) $dependencyInspector = new DependencyInspector(DependentFake::class);

    expect($dependencyInspector->isDependent())->toBeTrue();
});