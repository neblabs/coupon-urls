<?php

use CouponURLs\Original\Validation\Validators\NotEmpty;

it('throws an excpetion when value is empty', function(mixed $value) {
    (object) $notEmptyValidator = new NotEmpty($value);

    $notEmptyValidator->validate();
})->throws(Exception::class)->with([
    $emptyString = '',
    $null = null
]);