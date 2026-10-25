<?php

use CouponURLs\Original\Exceptions\InvalidImplementationException;
use CouponURLs\Original\Validation\Validators\DuckMethodsAreImplementedCorrectly;
use CouponURLs\Tests\Unit\Original\Validation\Validators\Mocks\DuckInterfaceFake;
return;
it('throws exception when doesn"t implement methods', function() {
    (object) $implementation = new Class implements DuckInterfaceFake {};

    (object) $duckMethodsAreImplementedCorectly = new DuckMethodsAreImplementedCorrectly(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class  
    );

    $duckMethodsAreImplementedCorectly->validate();
})->throws(InvalidImplementationException::class);

it('passes when it implements methods', function() {
    (object) $implementation = new Class implements DuckInterfaceFake {
        public function create() {} 
    };

    (object) $duckMethodsAreImplementedCorectly = new DuckMethodsAreImplementedCorrectly(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class  
    );

    $duckMethodsAreImplementedCorectly->validate();
    expect($duckMethodsAreImplementedCorectly->isValid())->toBeTrue();
});