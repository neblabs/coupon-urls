<?php declare(strict_types=1);

use CouponURLs\Original\Validation\Validators\ValidWhen;

it('valid whe true', function() {
    (object) $validWhenTrue = new ValidWhen(true);

    expect($validWhenTrue->isValid())->toBeTrue();
});

it('invalid when false', function() {
    (object) $validWhenTrue = new ValidWhen(false);

    expect($validWhenTrue->isValid())->toBeFalse();
});
