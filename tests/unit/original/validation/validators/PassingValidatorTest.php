<?php

use CouponURLs\Original\Validation\Validators\PassingValidator;

it('passes', function() {
    (object) $passingValidator = new PassingValidator;

    expect($passingValidator->isValid())->toBeTrue();
});