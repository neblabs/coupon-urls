<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\Validators\ValidatedItems;
use CouponURLs\Original\Validation\Validator;

it('validates the passed validator and then returns the items', function() {
    /** @var \Mockery\MockInterface&Collection */
    (object) $items = Mockery::mock(Collection::class);
    /** @var \Mockery\MockInterface&Validator */
    (object) $validator = Mockery::mock(Validator::class);

    (object) $validatedItems = new ValidatedItems(
        items: $items,
        validator: $validator
    );

    $validator->shouldReceive('validate');

    expect($validatedItems->get())->toBe($items);
});

it('wont catch the exception thrown by the given validator', function() {
    /** @var \Mockery\MockInterface&Collection */
    (object) $items = Mockery::mock(Collection::class);
    /** @var \Mockery\MockInterface&Validator */
    (object) $validator = Mockery::mock(Validator::class);

    (object) $validatedItems = new ValidatedItems(
        items: $items,
        validator: $validator
    );

    $validator->shouldReceive('validate')->andThrow(
        new Exception('Should not be caught!')
    );

    expect(fn() => $validatedItems->get())->toThrow(Exception::class);
});