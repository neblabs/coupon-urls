<?php declare(strict_types=1);

use CouponURLs\Original\Core\Services\RegisteredAppServices;

it('services file exists', function() {
    (object) $registeredAppServices = new RegisteredAppServices;

    expect($registeredAppServices->source())->toBeReadableFile();
})->todo('not used atm');