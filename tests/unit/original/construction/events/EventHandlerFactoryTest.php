<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Event\Exceptions\ExceptionHandlerFactory;
use CouponURLs\Original\Construction\Events\EventHandlerFactory;
use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Validation\Validator;

it('creates a new Eventhandler instance with the given subscriber', function() {
    (object) $eventHandlerFactory = new EventHandlerFactory;
    (object) $subscriber = new Class Implements Subscriber {
        use DefaultPriority;

        public function createEventArguments() : EventArguments
        {}
        public function validator() : Validator
        {}
        public function execute() : void
        {}
    };

    expect($eventHandlerFactory->create($subscriber))->toBeInstanceOf(EventHandler::class)
        ->toEqual(
            new EventHandler($subscriber, new ExceptionHandlerFactory)
        );
}); 
    