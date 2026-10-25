<?php

use CouponURLs\Original\Construction\Events\EventHandlerFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\CustomEvent;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Events\Wordpress\EventsHandler;
use CouponURLs\Original\Events\Wordpress\SubscribersNotifier;
use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use function CouponURLs\Tests\mock;

it('throws exception when an instance that is not CustomEvent is passed', function() {
    (object) $eventHandlerFactory = $this->createMock(EventHandlerFactory::class);
    (object) $subscribersNoifier = new SubscribersNotifier(
        eventHandlerFactory: $eventHandlerFactory
    );

    $subscribersNoifier->notify(stdClass::class);
})->throws(Error::class);

it('calls do_action with the fully quilaified class Name as the name and the insatnce as the arguments', function() {
    (object) $globalFunctionWrapper = $this->createMock(GlobalFunctionWrapper::class);
    /** @var \Mockery\MockInterface&EventsHandler */
    (object) $eventsHandler = $this->createMock(EventsHandler::class);

    (object) $subscribersNoifier = new SubscribersNotifier(
        $globalFunctionWrapper, eventsHandler: $eventsHandler
    );
    /**
     * @var \Mockery\MockInterface&CustomEvent
     */
    (object) $customEvent = mock(CustomEvent::class);

    $globalFunctionWrapper->expects($this->once())->method('__call')->with(
        $methodName = 'do_action', 
        $arguments = [
            'hook_name' => $customEvent::class,
            'arg' => $customEvent
        ]
    );

    $subscribersNoifier->notify($customEvent);
});

it('calls the subscribers with the given event', function() {
    /**
     * @var \Mockery\MockInterface&GlobalFunctionWrapper
     */
    (object) $globalFunctionWrapper = $this->createMock(GlobalFunctionWrapper::class);
    /** @var \Mockery\MockInterface&EventsHandler */
    (object) $eventsHandler = Mockery::mock(EventsHandler::class);
    /**
     * @var \Mockery\MockInterface&customEvent
     */
    (object) $customEvent = Mockery::mock(CustomEvent::class);
    /**
     * @var \Mockery\MockInterface&Subscriber
     */
    (object) $subscriber1 = Mockery::mock(Subscriber::class);
    /**
     * @var \Mockery\MockInterface&Subscriber
     */
    (object) $subscriber2 = Mockery::mock(Subscriber::class);
    /**
     * @var \Mockery\MockInterface&EventHandler
     */
    (object) $eventHandler1 = Mockery::mock(EventHandler::class);
    /**
     * @var \Mockery\MockInterface&EventHandler
     */
    (object) $eventHandler2 = Mockery::mock(EventHandler::class);
    /**
     * @var \Mockery\MockInterface&SubscribersNotifier
     */
    (object) $subscribersNotifier = new SubscribersNotifier(
        $globalFunctionWrapper, 
        $eventsHandler
    );

    $eventsHandler->shouldReceive('addSubscriber')->once()->with($subscriber1);
    $eventsHandler->shouldReceive('addSubscriber')->once()->with($subscriber2);

    $eventsHandler->shouldReceive('handle')->once()->with($customEvent);

    $subscribersNotifier->addSubscriber($subscriber1);
    $subscribersNotifier->addSubscriber($subscriber2);

    $globalFunctionWrapper->expects($this->once())->method('__call')->with(
        $methodName = 'do_action', 
        $arguments = [
            'hook_name' => $customEvent::class,
            'arg' => $customEvent
        ]
    );

    $subscribersNotifier->notify($customEvent);
});
    