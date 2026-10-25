<?php declare(strict_types=1);

use CouponURLs\Original\Language\CallableBuilder;
use CouponURLs\Tests\Unit\Original\Language\Fakes\ObjectToCallFake;

it('returns a callable with binded arguments', function() {
    (object) $object = Mockery::mock(ObjectToCallFake::class);

    (object) $callableBuilder = new CallableBuilder($object);

    (object) $callback = $callableBuilder->methodToCall('rafa', 'serna');

    $object->shouldReceive('methodToCall')->once()->with('rafa', 'serna', 'third')->andReturn(10);

    expect($callback('third'))->toBe(10);
});
