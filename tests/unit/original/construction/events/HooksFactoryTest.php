<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Construction\Abilities\SubscriberFactory;
use CouponURLs\Original\Construction\Events\HookFactory;
use CouponURLs\Original\Construction\Events\HooksFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Action;
use CouponURLs\Original\Events\Wordpress\Hooks;
use CouponURLs\Original\Events\Wordpress\Request;

use function CouponURLs\Original\Utilities\Collection\_;

it('creates a Hooks instance with Hook instances with their Subscriber instances from a Collection of grouped subscriber types', function() {
    /** @var \Mockery\MockInterface&HookFactory */
    (object) $hookFactory = Mockery::mock(HookFactory::class);
    /** @var \Mockery\MockInterface&SubscriberFactory */
    (object) $subscriberFactory = Mockery::mock(SubscriberFactory::class);
    /** @var \Mockery\MockInterface&Action */
    (object) $initActionHook = Mockery::mock(Action::class);
    /** @var \Mockery\MockInterface&Action */
    (object) $savePostActionHook = Mockery::mock(Action::class);
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber1 = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber2 = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber3 = Mockery::mock(Subscriber::class);

    (object) $hooksFactory = new HooksFactory(
        hookFactory: $hookFactory,
        subscriberFactory: $subscriberFactory
    );

    (object) $groupedSubscriberTypes = _(
        init: _(
            'subscriberClassName1',
            'subscriberClassName2',
        ),
        save_post: _(
            'subscriberClassName3',
        )
    );

    $hookFactory->shouldReceive('createFromRequest')->once()
        ->withArgs(fn($argument) => $argument  instanceof Request\Action && $argument->name() === 'init')->andReturn($initActionHook);
    $hookFactory->shouldReceive('createFromRequest')->once()
        ->withArgs(fn($argument) => $argument  instanceof Request\Action && $argument->name() === 'save_post')->andReturn($savePostActionHook);

    $subscriberFactory->shouldReceive('create')->once()->with('subscriberClassName1')->andReturn($subscriber1);
    $subscriberFactory->shouldReceive('create')->once()->with('subscriberClassName2')->andReturn($subscriber2);
    $subscriberFactory->shouldReceive('create')->once()->with('subscriberClassName3')->andReturn($subscriber3);

    $initActionHook->shouldReceive('addSubscribers')->once()->withArgs(
        fn(Collection $subsribers) => (($subsribers->asArray()) == [
                    $subscriber1,
                    $subscriber2
                ])
    );

    $savePostActionHook->shouldReceive('addSubscribers')->once()->withArgs(
        fn(Collection $subsribers) => (($subsribers->asArray()) == [
                    $subscriber3
                ])
    );

    expect($hooksFactory->createFromGroupedSubscriberTypes($groupedSubscriberTypes))
        ->toBeInstanceOf(Hooks::class);
});