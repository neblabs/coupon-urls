<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\GettableCollection;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Framework\FileSubscribersGetter;
use CouponURLs\Original\Events\Wordpress\Framework\RegisteredSubscribers;
use function CouponURLs\Original\Utilities\Collection\_;

it('gets both original and app subscribers using the getter', function() {
    /** @var \Mockery\MockInterface&FileSubscribersGetter */
    (object) $originalSubscribersGetter = Mockery::mock(FileSubscribersGetter::class);
    /** @var \Mockery\MockInterface&FileSubscribersGetter */
    (object) $appSubscribersGetter = Mockery::mock(FileSubscribersGetter::class);
    (object) $registeredSubscribers = new RegisteredSubscribers(
        originalSubscribersGetter: $originalSubscribersGetter,
        appSubscribersGetter: $appSubscribersGetter
    );

    (object) $originalSubscribers = _(
        init: [
            'initSubscriber1',
            'initSubscriber2'
        ],
        admin_init: [
            'adminInitSubscriber1'
        ]
    );

    (object) $appSubscribers = _(
        init: [
            'initSubscriber3',
        ],
        save_post: [
            'savePostSubscriber1'
        ],
        admin_init: [
            'adminInitSubscriber2',
            'adminInitSubscriber3',
            'adminInitSubscriber4'
        ]
    );

    $originalSubscribersGetter->shouldReceive('get')->once()->andReturn($originalSubscribers);
    $appSubscribersGetter->shouldReceive('get')->once()->andReturn($appSubscribers);

    expect($registeredSubscribers->get())->toEqual(_(
        init: _(
            'initSubscriber1',
            'initSubscriber2',
            'initSubscriber3'
        ),
        save_post: _(
            'savePostSubscriber1'
        ),
        admin_init: _(
            'adminInitSubscriber1',
            'adminInitSubscriber2',
            'adminInitSubscriber3',
            'adminInitSubscriber4'
        )
    ));
});