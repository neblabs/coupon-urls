<?php

use CouponURLs\Original\Validation\Validators\DuckInterfaceIsImplemented;
use CouponURLs\Tests\Unit\Original\Validation\Validators\Mocks\DuckInterfaceFake;

return;
it('is invalid when interface Is not corectly implemented', function(object $implementation) {
    (object) $duckInterfaceIsImplemented = new DuckInterfaceIsImplemented(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class
    );

    expect($duckInterfaceIsImplemented->isValid())->toBeFalse();
})->with([
    'interface is not even implemeted' => new class {},
    'no method implemeted' => new class implements DuckInterfaceFake {}
]);

it('is valid when the interface Is correctly implemented', function() {
    (object) $implementation = new class implements DuckInterfaceFake {
        public function create() : void {}
    };

    (object) $duckInterfaceIsImplemented = new DuckInterfaceIsImplemented(
        interface: DuckInterfaceFake::class,
        implementation: $implementation::class
    );

    expect($duckInterfaceIsImplemented->isValid())->toBeTrue();
});