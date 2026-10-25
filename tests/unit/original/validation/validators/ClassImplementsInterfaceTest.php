<?php

use CouponURLs\Original\Validation\Validators\ClassImplementsInterface;
use CouponURLs\Tests\Unit\Original\Validation\Validators\Mocks\DuckInterfaceFake;

it('is invalid when interface Is not implemented at all', function() {
    (object) $implementation = new class {};
    (object) $classImplementsInterface = new ClassImplementsInterface(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class
    );

    expect($classImplementsInterface->isValid())->toBeFalse();
});

it('is valid when interface Is implemented', function() {
    (object) $implementation = new class Implements DuckInterfaceFake {};
    (object) $classImplementsInterface = new ClassImplementsInterface(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class
    );

    expect($classImplementsInterface->isValid())->toBeTrue();
});