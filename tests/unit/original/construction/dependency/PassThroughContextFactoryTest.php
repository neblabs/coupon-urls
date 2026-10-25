<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\PassThroughContextFactory;
use CouponURLs\Original\Dependency\Abilities\Context;

it('can only create when given an instance of Context', function() {
    (object) $passThruContextFactory = new PassThroughContextFactory;
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    expect($passThruContextFactory->canCreate($context))->toBeTrue();
});

it('cannot create when given another type (null)', function() {
    (object) $passThruContextFactory = new PassThroughContextFactory;

    $context = null;

    expect($passThruContextFactory->canCreate($context))->toBeFalse();
});

it('returns the same context instance, unaltered', function() {
    (object) $passThruContextFactory = new PassThroughContextFactory;
    /** @var \Mockery\MockInterface&Context */
    (object) $context = Mockery::mock(Context::class);

    expect($passThruContextFactory->create($context))->toBe($context);
});