<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Events\EventHandlerFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\CustomEvent;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Events\Wordpress\EventsHandler;
use Mockery\Mock;

it('creates an eventHanlder for each subscriber and calls handle() on each with the given arguments', function() {
    /** @var \Mockery\MockInterface&EventHandlerFactory */
    (object) $eventHandlerFactory = Mockery::mock(EventHandlerFactory::class);
    (object) $eventsHandler = new EventsHandler($eventHandlerFactory);

    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber1 = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber2 = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&EventHandler */
    (object) $eventHandler1 = Mockery::mock(EventHandler::class);
    /** @var \Mockery\MockInterface&EventHandler */
    (object) $eventHandler2 = Mockery::mock(EventHandler::class);
    /** @var \Mockery\MockInterface&CustomEvent */
    (object) $customEvent = Mockery::mock(CustomEvent::class);
    (string) $aCustomString = 'hello mock';

    $eventsHandler->addSubscriber($subscriber1);
    $eventsHandler->addSubscriber($subscriber2);

    $eventHandlerFactory->shouldReceive('create')->once()->with($subscriber1)->andReturn($eventHandler1);
    $eventHandler1->shouldReceive('handle')->once()->with($customEvent, $aCustomString);

    $eventHandlerFactory->shouldReceive('create')->once()->with($subscriber2)->andReturn($eventHandler2);
    $eventHandler2->shouldReceive('handle')->once()->with($customEvent, $aCustomString);

    $eventsHandler->handle($customEvent, $aCustomString);
});