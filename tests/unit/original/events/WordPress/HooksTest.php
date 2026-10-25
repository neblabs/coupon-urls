<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Hook;
use CouponURLs\Original\Events\Wordpress\Hooks;
use function CouponURLs\Original\Utilities\Collection\_;

it('registers the collection of hooks', function() {
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook1 = Mockery::mock(Hook::class);
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook2 = Mockery::mock(Hook::class);
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook3 = Mockery::mock(Hook::class);

    (object) $hooks = new Hooks(_(
        $hook1,
        $hook2,
        $hook3
    ));

    $hook1->shouldReceive('register')->once();
    $hook2->shouldReceive('register')->once();
    $hook3->shouldReceive('register')->once();

    $hooks->register();
});

it('unregisters the collection of hooks', function() {
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook1 = Mockery::mock(Hook::class);
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook2 = Mockery::mock(Hook::class);
    /** @var \Mockery\MockInterface&Hook */
    (object) $hook3 = Mockery::mock(Hook::class);

    (object) $hooks = new Hooks(_(
        $hook1,
        $hook2,
        $hook3
    ));

    $hook1->shouldReceive('unregister')->once();
    $hook2->shouldReceive('unregister')->once();
    $hook3->shouldReceive('unregister')->once();

    $hooks->unregister();
});